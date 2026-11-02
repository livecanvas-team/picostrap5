<?php
/** Measurements for the options screen. No public tracking or frontend writes. */
defined( 'ABSPATH' ) || exit;

function picostrap_options_asset_bytes( $path ) {
    return is_file( $path ) && is_readable( $path ) ? (int) filesize( $path ) : 0;
}

/** A reproducible gzip estimate, independent of HTTP server configuration. */
function picostrap_options_gzip_bytes( $content ) {
    if ( ! is_string( $content ) || ! function_exists( 'gzencode' ) ) return null;
    $compressed = gzencode( $content, 6 );
    return false === $compressed ? null : strlen( $compressed );
}

function picostrap_options_asset_gzip_bytes( $path ) {
    if ( ! is_file( $path ) || ! is_readable( $path ) || filesize( $path ) > 2 * MB_IN_BYTES ) return null;
    return picostrap_options_gzip_bytes( file_get_contents( $path ) );
}

/** Clickable native popovers work with touch and keyboard, including Escape. */
function picostrap_options_help( $title, $explanation ) {
    $id = wp_unique_id( 'pico-help-' );
    ?>
    <span class="pico-help-control"><button type="button" class="pico-help-button" popovertarget="<?php echo esc_attr( $id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'About %s', 'picostrap5' ), $title ) ); ?>"><svg width="15" height="15" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7.5" stroke="currentColor" stroke-width="1.5"/><path d="M10 9v5M10 5.5v1" stroke="currentColor" stroke-width="1.5"/></svg></button><span class="pico-tooltip" popover id="<?php echo esc_attr( $id ); ?>" role="note"><strong><?php echo esc_html( $title ); ?></strong><span><?php echo wp_kses_post( $explanation ); ?></span></span></span>
    <?php
}

function picostrap_options_bundle_path() {
    $name = get_theme_mod( 'css_bundle_version_number', 0 ) ? picostrap_get_complete_css_filename() : picostrap_get_base_css_filename();
    return get_stylesheet_directory() . '/' . picostrap_get_css_optional_subfolder_name() . $name;
}

/** Preview the existing reducer without enabling it or writing theme settings. */
function picostrap_options_css_preview( $html ) {
    $path = picostrap_options_bundle_path();
    if ( ! is_string( $html ) || strlen( $html ) > 4 * MB_IN_BYTES || false === stripos( $html, '</html>' ) ) {
        return new WP_Error( 'pico_preview_html', __( 'A complete HTML page of up to 4 MB is required. Try again after the homepage finishes loading.', 'picostrap5' ) );
    }
    if ( ! is_readable( $path ) || ! is_file( $path ) || filesize( $path ) > 2 * MB_IN_BYTES ) {
        return new WP_Error( 'pico_preview_bundle', __( 'The CSS bundle is missing or larger than 2 MB. Recompile Sass, then try again.', 'picostrap5' ) );
    }
    $css = file_get_contents( $path );
    if ( false === $css || '' === $css || false !== stripos( $css, '</style' ) ) {
        return new WP_Error( 'pico_preview_css', __( 'The CSS bundle could not be analyzed. Recompile Sass, then try again.', 'picostrap5' ) );
    }
    try {
        $content = preg_replace( '~<style\b[^>]*>.*?</style\s*>~is', '', $html );
        preg_match_all( '/-?[_a-zA-Z][_a-zA-Z0-9-]*/', html_entity_decode( $content, ENT_QUOTES, 'UTF-8' ), $matches );
        $tokens = array_fill_keys( array_merge( $matches[0], picostrap_used_css_safelist() ), true );
        ksort( $tokens );
        $reduced = picostrap_used_css_rebase( picostrap_used_css_reduce( $css, $tokens ), picostrap_get_css_url() );
        if ( false !== stripos( $reduced, '</style' ) ) {
            throw new RuntimeException( 'Unsafe stylesheet.' );
        }
        $inline = '<style id="picostrap-styles-css">' . $reduced . '</style>';
        $helper = picostrap_used_css_helper( picostrap_get_css_url(), $tokens, $css );
        $candidate_content = $inline . $helper;
        $candidate = strlen( $candidate_content );
        $original = strlen( $css );
        // The frontend retains the full bundle when reduction would add weight.
        if ( $candidate >= $original ) {
            $candidate_content = $css;
            $candidate = $original;
        }
        return array( 'original' => $original, 'candidate' => $candidate, 'saved' => $original - $candidate,
            'original_gzip' => picostrap_options_gzip_bytes( $css ),
            'candidate_gzip' => picostrap_options_gzip_bytes( $candidate_content ),
            'inline' => strlen( $inline ), 'helper' => strlen( $helper ),
            'percent' => round( 100 * ( $original - $candidate ) / $original, 1 ) );
    } catch ( Throwable $error ) {
        return new WP_Error( 'pico_preview_parse', __( 'This stylesheet could not be reduced safely. The original bundle will be kept.', 'picostrap5' ) );
    }
}

add_action( 'wp_ajax_picostrap_css_preview', function () {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_send_json_error( array( 'message' => __( 'You are not allowed to analyze this theme.', 'picostrap5' ) ), 403 );
    }
    check_ajax_referer( 'picostrap_css_preview', 'nonce' );
    $html = isset( $_POST['html'] ) && is_string( $_POST['html'] ) ? wp_unslash( $_POST['html'] ) : '';
    $result = picostrap_options_css_preview( $html );
    if ( is_wp_error( $result ) ) {
        wp_send_json_error( array( 'message' => $result->get_error_message() ), 422 );
    }
    wp_send_json_success( $result );
} );
