<?php
/**
 * Experimental, conservative CSS reduction against the complete rendered page.
 * The original enqueue stays intact until a complete replacement is available.
 */
defined( 'ABSPATH' ) || exit;

function picostrap_used_css_enabled() {
    if ( ! get_theme_mod( 'picostrap_inline_css', false ) || get_theme_mod( 'disable_bootstrap' ) ) return false;
    if ( is_admin() || wp_doing_ajax() || is_customize_preview() || is_preview() || is_feed() || is_embed() || is_robots() || is_trackback() ) return false;
    if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) return false;
    foreach ( array( 'lc_page_editing_mode', 'lc_action_launch_editing', 'lc_action_new_page', 'customize_theme', 'customize_changeset_uuid', 'compile_sass', 'autorecompile' ) as $key ) {
        if ( isset( $_GET[ $key ] ) ) return false;
    }
    return (bool) apply_filters( 'picostrap_used_css_enabled', true );
}

/** Preserve runtime classes, including elements created by Bootstrap itself. */
function picostrap_used_css_safelist() {
    return apply_filters( 'picostrap_used_css_safelist', array(
        'active', 'show', 'showing', 'hide', 'hiding', 'fade', 'collapsing', 'collapsed',
        'disabled', 'focus', 'was-validated', 'is-valid', 'is-invalid',
        'modal-open', 'modal-backdrop', 'modal-static', 'offcanvas-backdrop', 'pointer-event', 'collapse',
        'carousel-item-next', 'carousel-item-prev', 'carousel-item-start', 'carousel-item-end',
        'tooltip', 'tooltip-arrow', 'tooltip-inner', 'popover', 'popover-arrow', 'popover-header', 'popover-body',
        'bs-tooltip-auto', 'bs-tooltip-top', 'bs-tooltip-bottom', 'bs-tooltip-start', 'bs-tooltip-end',
        'bs-popover-auto', 'bs-popover-top', 'bs-popover-bottom', 'bs-popover-start', 'bs-popover-end',
    ) );
}

/** A selector can be discarded only when a plain required class/ID is absent. */
function picostrap_used_css_selector_needed( $selector, $tokens ) {
    // Functional pseudos, attribute values, escapes and non-ASCII syntax are ambiguous.
    if ( preg_match( '/[()\[\\\\]|[^\x09\x0a\x0d\x20-\x7e]/', $selector ) ) return true;
    foreach ( explode( ',', $selector ) as $branch ) {
        preg_match_all( '/[.#](-?[_a-zA-Z][_a-zA-Z0-9-]*)/', $branch, $matches );
        $needed = true;
        foreach ( $matches[1] as $token ) {
            if ( ! isset( $tokens[ $token ] ) ) {
                $needed = false;
                break;
            }
        }
        if ( $needed ) return true;
    }
    return false;
}

/**
 * Balanced scanner: strings, comments and parentheses cannot terminate a rule.
 * Unknown at-rules and declaration bodies are kept verbatim. Broken input fails closed.
 */
function picostrap_used_css_reduce( $css, $tokens, $depth = 0 ) {
    if ( $depth > 32 ) throw new RuntimeException( 'CSS nesting limit.' );
    $length = strlen( $css );
    $start = 0;
    $open = null;
    $braces = 0;
    $parens = 0;
    $brackets = 0;
    $quote = '';
    $comment = false;
    $output = '';
    for ( $i = 0; $i < $length; ++$i ) {
        $char = $css[ $i ];
        $next = $i + 1 < $length ? $css[ $i + 1 ] : '';
        if ( $comment ) {
            if ( '*' === $char && '/' === $next ) { $comment = false; ++$i; }
            continue;
        }
        if ( '' !== $quote ) {
            if ( '\\' === $char ) { ++$i; continue; }
            if ( $char === $quote ) $quote = '';
            continue;
        }
        if ( '/' === $char && '*' === $next ) { $comment = true; ++$i; continue; }
        if ( '"' === $char || "'" === $char ) { $quote = $char; continue; }
        if ( '\\' === $char ) { ++$i; continue; }
        if ( '(' === $char ) ++$parens;
        if ( ')' === $char ) --$parens;
        if ( '[' === $char ) ++$brackets;
        if ( ']' === $char ) --$brackets;
        if ( $parens < 0 || $brackets < 0 ) throw new RuntimeException( 'Unbalanced CSS.' );
        if ( $parens || $brackets ) continue;
        if ( '{' === $char ) {
            if ( 0 === $braces ) $open = $i;
            ++$braces;
        } elseif ( '}' === $char ) {
            if ( --$braces < 0 ) throw new RuntimeException( 'Unbalanced CSS block.' );
            if ( 0 !== $braces ) continue;
            $header = substr( $css, $start, $open - $start );
            $selector = trim( preg_replace( '~/\*.*?\*/~s', '', $header ) );
            $body = substr( $css, $open + 1, $i - $open - 1 );
            if ( '' === $selector ) throw new RuntimeException( 'Missing CSS selector.' );
            if ( preg_match( '/^@(media|supports|layer|container)\b/i', $selector ) ) {
                $body = picostrap_used_css_reduce( $body, $tokens, $depth + 1 );
                if ( '' !== trim( $body ) ) $output .= $header . '{' . $body . '}';
            } elseif ( '@' === $selector[0] || picostrap_used_css_selector_needed( $selector, $tokens ) ) {
                $output .= substr( $css, $start, $i - $start + 1 );
            }
            $start = $i + 1;
        } elseif ( ';' === $char && 0 === $braces ) {
            $statement = substr( $css, $start, $i - $start + 1 );
            $clean = trim( preg_replace( '~/\*.*?\*/~s', '', $statement ) );
            // Imports depend on external CSS and cannot safely be moved into inline CSS.
            if ( ! preg_match( '/^@(charset|layer)\b/i', $clean ) ) throw new RuntimeException( 'Unsupported CSS statement.' );
            if ( ! preg_match( '/^@charset\b/i', $clean ) ) $output .= $statement;
            $start = $i + 1;
        }
    }
    if ( $braces || $parens || $brackets || $comment || '' !== $quote ) throw new RuntimeException( 'Incomplete CSS.' );
    $tail = substr( $css, $start );
    if ( '' !== trim( preg_replace( '~/\*.*?\*/~s', '', $tail ) ) ) throw new RuntimeException( 'Unexpected CSS tail.' );
    return $output;
}

/** Rebase relative assets; unfamiliar URL syntax leaves the original bundle in place. */
function picostrap_used_css_rebase( $css, $url ) {
    $length = strlen( $css );
    $output = '';
    for ( $i = 0; $i < $length; ) {
        // Copy comments and strings without mistaking their contents for functions.
        if ( substr( $css, $i, 2 ) === '/*' ) {
            $end = strpos( $css, '*/', $i + 2 );
            if ( false === $end ) throw new RuntimeException( 'Incomplete CSS comment.' );
            $output .= substr( $css, $i, $end + 2 - $i );
            $i = $end + 2;
            continue;
        }
        $char = $css[ $i ];
        if ( '"' === $char || "'" === $char ) {
            $start = $i++;
            while ( $i < $length ) {
                if ( '\\' === $css[ $i ] ) { $i += 2; continue; }
                if ( $css[ $i++ ] === $char ) break;
            }
            $output .= substr( $css, $start, $i - $start );
            continue;
        }
        // Keep escaped selectors/content; escaped identifiers could hide a URL function.
        if ( '\\' === $char ) {
            throw new RuntimeException( 'Escaped CSS identifier requires original bundle.' );
        }
        if ( ( 0 === $i || ! preg_match( '/[a-z0-9_-]/i', $css[ $i - 1 ] ) ) && preg_match( '/\G(?:-webkit-)?image-set\(/i', $css, $unused, 0, $i ) ) {
            throw new RuntimeException( 'Image-set requires original bundle.' );
        }
        if ( ( 0 === $i || ! preg_match( '/[a-z0-9_-]/i', $css[ $i - 1 ] ) ) && strncasecmp( substr( $css, $i, 4 ), 'url(', 4 ) === 0 ) {
            if ( ! preg_match( '~\Gurl\(\s*(?:"([^"\r\n]*)"|\'([^\'\r\n]*)\'|([^\s()"\']*))\s*\)~i', $css, $match, PREG_UNMATCHED_AS_NULL, $i ) ) {
                throw new RuntimeException( 'Unsupported CSS URL syntax.' );
            }
            $asset = null !== $match[1] ? $match[1] : ( null !== $match[2] ? $match[2] : $match[3] );
            if ( false !== strpos( $asset, '\\' ) ) throw new RuntimeException( 'Escaped CSS URL requires original bundle.' );
            if ( '' === $asset || preg_match( '~^(?:[a-z][a-z0-9+.-]*:|//|\#)~i', $asset ) ) {
                $output .= $match[0];
            } else {
                $output .= 'url("' . str_replace( '"', '%22', WP_Http::make_absolute_url( $asset, $url ) ) . '")';
            }
            $i += strlen( $match[0] );
            continue;
        }
        $output .= $char;
        ++$i;
    }
    return $output;
}

/**
 * Persistent object cache when available, otherwise one transient per public page.
 * The signature includes the bundle contents and all page tokens: stale data never matches.
 * Query strings/user variations replace a slot instead of creating unlimited cache keys.
 */
function picostrap_used_css_cache_slot() {
    if ( wp_using_ext_object_cache() || is_user_logged_in() || ! apply_filters( 'picostrap_used_css_persistent_cache', true ) ) return false;
    return 'pico_css_' . md5( get_stylesheet() . '|' . (int) is_singular() . '|' . get_queried_object_id() );
}

/** Start only on normal HTML template requests. Do not buffer REST, feeds or editors. */
add_action( 'template_redirect', function () {
    if ( ! picostrap_used_css_enabled() || get_query_var( 'sitemap' ) || get_query_var( 'sitemap-stylesheet' ) ) return;
    if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' !== $_SERVER['REQUEST_METHOD'] ) return;
    $GLOBALS['picostrap_used_css_buffering'] = true;
    ob_start( 'picostrap_used_css_render' );
}, 0 );

// Capture the actual tag, so media attributes and plugin rewrites can be respected.
add_filter( 'style_loader_tag', function ( $html, $handle ) {
    if ( 'picostrap-styles' === $handle && ! empty( $GLOBALS['picostrap_used_css_buffering'] ) ) {
        $GLOBALS['picostrap_used_css_tag'] = $html;
    }
    return $html;
}, PHP_INT_MAX, 2 );

add_action( 'admin_bar_menu', function ( $bar ) {
    if ( is_admin() || ! current_user_can( 'administrator' ) || ! get_theme_mod( 'picostrap_inline_css' ) ) return;
    $bar->add_node( array(
        'id' => 'picostrap-css-savings',
        'parent' => 'ps-recompile-sass',
        'title' => '<span id="picostrap-css-savings-note">' . esc_html__( 'CSS: original bundle', 'picostrap5' ) . '</span>',
        'meta' => array( 'title' => __( 'Uncompressed CSS and helper markup bytes. Dynamic content may restore the original bundle.', 'picostrap5' ) ),
    ) );
}, 110 );

/** Inline runtime guard: no additional JS request, and only emitted after reduction. */
function picostrap_used_css_helper( $href, $tokens, $css ) {
    preg_match_all( '/#(-?[_a-zA-Z][_a-zA-Z0-9-]*)/', $css, $id_matches );
    $missing_ids = array_diff( array_unique( $id_matches[1] ), array_keys( $tokens ) );
    $script = <<<'JS'
/** Restore the full theme stylesheet when runtime markup introduces unknown tokens. */
(() => {
    'use strict';

    const config = document.currentScript;
    if (!config) return;
    const start = () => {
        const inline = document.getElementById('picostrap-styles-css');
        if (!inline || inline.tagName !== 'STYLE' || !config.dataset.bundle) return;

        // Use PHP's actual reduction tokens, including its Bootstrap safelist.
        // Taking a browser-only baseline would miss changes made before this DOM-ready guard runs.
        const known = new Set((config.dataset.tokens || '').split(' '));
        known.add(config.id);
        const missingIds = new Set((config.dataset.ids || '').split(' '));
        const simpleToken = /^-?[_a-zA-Z][_a-zA-Z0-9-]*$/;
        let observer;
        let loading = false;

        const note = (label) => {
            const element = document.getElementById('picostrap-css-savings-note');
            if (element && label) element.textContent = label;
        };

        const restore = () => {
            if (loading || !inline.isConnected) return;
            loading = true;
            if (observer) observer.disconnect();

            const full = document.createElement('link');
            full.id = 'picostrap-styles-full-css';
            full.rel = 'stylesheet';
            full.href = config.dataset.bundle;
            // Fetch without changing the cascade until the load event confirms success.
            full.media = 'not all';
            full.onload = () => {
                full.media = 'all';
                inline.remove();
                full.id = 'picostrap-styles-css';
                note(config.dataset.fullLabel);
            };
            full.onerror = () => {
                full.remove();
                loading = false;
                note(config.dataset.errorLabel);
                // Preserve inline CSS on failure. Retry once connectivity returns.
                window.addEventListener('online', restore, { once: true });
            };
            inline.after(full);
        };

        const unknown = (element) => {
            if (element.id && missingIds.has(element.id)) return true;
            for (const name of element.classList) {
                if (simpleToken.test(name) && !known.has(name)) return true;
            }
            return false;
        };

        const inspect = (root) => {
            if (root.nodeType !== 1 || !root.isConnected) return false;
            if (unknown(root)) return true;
            for (const element of root.querySelectorAll('[class], [id]')) {
                if (unknown(element)) return true;
            }
            return false;
        };

        if (!window.MutationObserver) {
            restore();
            return;
        }
        observer = new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                if (mutation.type === 'attributes') {
                    if (mutation.target.isConnected && unknown(mutation.target)) {
                        restore();
                        return;
                    }
                } else {
                    for (const node of mutation.addedNodes) {
                        if (inspect(node)) {
                            restore();
                            return;
                        }
                    }
                }
            }
        });
        observer.observe(document.documentElement, {
            subtree: true,
            childList: true,
            attributes: true,
            attributeFilter: ['class', 'id'],
        });
        if (inspect(document.documentElement)) restore();
    };
    // Inline scripts do not support defer. Defer the work until parsing is complete.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start, { once: true });
    } else {
        start();
    }
})();
JS;
    return wp_get_inline_script_tag( $script, array(
        'id' => 'picostrap-used-css-js',
        'data-bundle' => $href,
        'data-ids' => implode( ' ', $missing_ids ),
        'data-tokens' => implode( ' ', array_keys( $tokens ) ),
        'data-full-label' => __( 'CSS: original bundle (dynamic content)', 'picostrap5' ),
        'data-error-label' => __( 'CSS: inline · full bundle unavailable', 'picostrap5' ),
    ) );
}

function picostrap_used_css_render( $html, $phase = PHP_OUTPUT_HANDLER_FINAL ) {
    // A manual flush means the full document is unavailable: keep every original byte.
    if ( ! ( $phase & PHP_OUTPUT_HANDLER_FINAL ) || ( $phase & PHP_OUTPUT_HANDLER_CLEAN ) ) {
        $GLOBALS['picostrap_used_css_buffering'] = false;
        return $html;
    }
    if ( empty( $GLOBALS['picostrap_used_css_buffering'] ) || ! picostrap_used_css_enabled() ) return $html;
    $tag = isset( $GLOBALS['picostrap_used_css_tag'] ) ? $GLOBALS['picostrap_used_css_tag'] : '';
    if ( ! $tag || 1 !== substr_count( $html, $tag ) || stripos( $html, '</html>' ) === false || stripos( $html, '<head' ) === false ) return $html;
    foreach ( headers_list() as $header ) {
        if ( stripos( $header, 'Content-Type:' ) === 0 && ! preg_match( '~^Content-Type:\s*text/html\b~i', $header ) ) return $html;
        // A CSP may need an external stylesheet, a nonce or a hash. Preserve the bundle.
        if ( stripos( $header, 'Content-Security-Policy:' ) === 0 ) return $html;
    }
    if ( preg_match( '~<meta\b[^>]*http-equiv\s*=\s*["\']?Content-Security-Policy\b~i', $html ) ) return $html;
    // Only replace the ordinary all-media tag for the expected local stylesheet.
    if ( ! preg_match( '~\bmedia=["\']all["\']~i', $tag ) || preg_match( '~\b(?:integrity|onload|disabled|nonce)\s*(?:=|[\s>])~i', $tag ) ) return $html;
    if ( ! preg_match( '~\bhref=["\']([^"\']+)["\']~i', $tag, $href ) ) return $html;
    $href = html_entity_decode( $href[1], ENT_QUOTES, 'UTF-8' );
    if ( strtok( $href, '?#' ) !== strtok( picostrap_get_css_url(), '?#' ) ) return $html;
    $filename = get_theme_mod( 'css_bundle_version_number', 0 ) ? picostrap_get_complete_css_filename() : picostrap_get_base_css_filename();
    $path = get_stylesheet_directory() . '/' . picostrap_get_css_optional_subfolder_name() . $filename;
    if ( ! is_readable( $path ) || ! is_file( $path ) || filesize( $path ) > 2 * 1024 * 1024 || strlen( $html ) > 4 * 1024 * 1024 ) return $html;
    $css = @file_get_contents( $path ); // A concurrent Sass write/removal must fall back quietly.
    if ( false === $css || '' === $css || stripos( $css, '</style' ) !== false ) return $html;
    $original_html = $html;
    try {
        // Scripts/templates intentionally count: they can contain runtime class names.
        $content = preg_replace( '~<style\b[^>]*>.*?</style\s*>~is', '', $html );
        preg_match_all( '/-?[_a-zA-Z][_a-zA-Z0-9-]*/', html_entity_decode( $content, ENT_QUOTES, 'UTF-8' ), $matches );
        $tokens = array_fill_keys( array_merge( $matches[0], picostrap_used_css_safelist() ), true );
        ksort( $tokens );
        $key = hash( 'sha256', 'v2|' . $href . '|' . $css . '|' . implode( '|', array_keys( $tokens ) ) );
        $reduced = wp_cache_get( $key, 'picostrap-used-css' );
        $slot = picostrap_used_css_cache_slot();
        if ( false === $reduced && $slot ) {
            $cached = get_transient( $slot );
            if ( is_array( $cached ) && isset( $cached['signature'], $cached['css'] ) && $cached['signature'] === $key ) $reduced = $cached['css'];
        }
        if ( false === $reduced ) {
            $reduced = picostrap_used_css_rebase( picostrap_used_css_reduce( $css, $tokens ), picostrap_get_css_url() );
            if ( $slot ) set_transient( $slot, array( 'signature' => $key, 'css' => $reduced ), HOUR_IN_SECONDS );
        }
        wp_cache_set( $key, $reduced, 'picostrap-used-css', HOUR_IN_SECONDS );
        if ( ! is_string( $reduced ) ) {
            return $original_html;
        }
        if ( stripos( $reduced, '</style' ) !== false ) return $original_html;
        $inline = '<style id="picostrap-styles-css">' . $reduced . '</style>';
        // Emit the helper only when the stylesheet is actually replaced. A server-side
        // baseline catches changes made before the DOM-ready helper starts observing.
        $helper = picostrap_used_css_helper( $href, $tokens, $css );
        // Count the helper markup and its token manifest as part of the inline payload.
        $saved = strlen( $css ) - strlen( $inline ) - strlen( $helper );
        if ( $saved <= 0 ) return $html;
        $html = str_replace( $tag, $inline . $helper, $html );
        $note = sprintf(
            /* translators: 1: saved size, 2: saved percentage, 3: original size, 4: inline size. */
            __( 'CSS: %1$s saved (%2$s%%) · %3$s → %4$s', 'picostrap5' ),
            size_format( $saved, 1 ), number_format_i18n( 100 * $saved / strlen( $css ), 1 ), size_format( strlen( $css ), 1 ), size_format( strlen( $inline ) + strlen( $helper ), 1 )
        );
        $html = preg_replace_callback( '~(<span id="picostrap-css-savings-note">).*?(</span>)~s', function ( $match ) use ( $note ) {
            return $match[1] . esc_html( $note ) . $match[2];
        }, $html );
        if ( ! headers_sent() ) header_remove( 'Content-Length' );
        return $html;
    } catch ( Throwable $error ) {
        return $original_html;
    }
}
