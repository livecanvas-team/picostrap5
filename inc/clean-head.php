<?php

/**
 * Remove unnecessary discovery markup and optional WordPress frontend assets.
 *
 * @package picostrap5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

function picostrap_cleanup() {

    // Keep legacy removals for older WordPress versions.
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'index_rel_link');
    remove_action('wp_head', 'feed_links', 2);
    remove_action('wp_head', 'feed_links_extra', 3);
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
    remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
    remove_action('template_redirect', 'wp_shortlink_header', 11);
    
    picostrap_disable_emojis();

    // Remove REST discovery links, keeping the REST API itself available.
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'template_redirect', 'rest_output_link_header', 11 );

    // Disable the public oEmbed provider route, not the other REST endpoints.
    if ( ! is_user_logged_in() ) remove_action( 'rest_api_init', 'wp_oembed_register_route' );
}
add_action( 'init', 'picostrap_cleanup' );

function picostrap_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'embed_head', 'print_emoji_detection_script' );
    remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
    remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
    remove_action( 'enqueue_embed_scripts', 'wp_enqueue_emoji_styles' );
    // Retain removals for older WordPress versions and editor iframe hooks.
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'picostrap_disable_emojis_tinymce' );
    add_filter( 'wp_resource_hints', 'picostrap_disable_emojis_remove_dns_prefetch', 10, 2 );
    add_filter( 'emoji_svg_url', '__return_false' );
}
// Admin filters are registered after the theme's init callback has already run.
add_action( 'admin_init', 'picostrap_disable_emojis' );

function picostrap_disable_emojis_tinymce( $plugins ) {
    return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}

function picostrap_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
    if ( 'dns-prefetch' !== $relation_type ) return $urls;

    // Hints can be strings or attribute arrays. Do not compare arrays as strings,
    // or use emoji_svg_url here: that filter has already been set to false.
    foreach ( $urls as $key => $hint ) {
        $url = is_array( $hint ) ? ( $hint['href'] ?? '' ) : $hint;
        $parts = wp_parse_url( $url );
        if ( ! $parts || 's.w.org' !== strtolower( $parts['host'] ?? '' ) ) continue;

        // A host-only hint may support other resources on the same CDN.
        if ( strpos( $parts['path'] ?? '', '/images/core/emoji/' ) === 0 ) {
            unset( $urls[ $key ] );
        }
    }
    return $urls;
}

//Disable Self Pingbacks
add_action('pre_ping', 'disable_self_pingbacks');

function disable_self_pingbacks( &$links ) {
    $home = wp_parse_url( home_url( '/' ) );
    if ( empty( $home['host'] ) ) return;

    $home_path = untrailingslashit( $home['path'] ?? '' );
    $home_port = $home['port'] ?? 0;
    if ( $home_port === ( 'https' === ( $home['scheme'] ?? '' ) ? 443 : 80 ) ) $home_port = 0;

    foreach ( $links as $key => $link ) {
        $target = wp_parse_url( $link );
        if ( ! $target || strtolower( $target['host'] ?? '' ) !== strtolower( $home['host'] ) ) continue;
        $port = $target['port'] ?? 0;
        if ( $port === ( 'https' === ( $target['scheme'] ?? '' ) ? 443 : 80 ) ) $port = 0;
        if ( $port !== $home_port ) continue;

        $path = $target['path'] ?? '';
        if ( $path === $home_path || strpos( $path, $home_path . '/' ) === 0 ) {
            unset( $links[ $key ] );
        }
    }
}


// Keep the same generic error for unknown users and incorrect passwords.
function picostrap_show_less_login_info() {
    return '<strong>ERROR</strong>: Stop guessing!';
}
add_filter( 'login_errors', 'picostrap_show_less_login_info' );

 
// Suppress the generator in feeds as well as the document head.
function picostrap_no_generator() {
    return '';
}
add_filter( 'the_generator', 'picostrap_no_generator' );



// Strip generated menu IDs, retaining the classes needed for active/dropdown states.
add_filter( 'nav_menu_item_id', 'my_css_attributes_filter', 100, 1 );
function my_css_attributes_filter( $var ) {
    return is_array( $var ) ? array() : '';
}
// Disable WordPress post-embed discovery and automatic host JS, keeping video
// providers and core oEmbed sanitization. Leave registration intact for plugins
// that explicitly need wp-embed as a dependency.
function picostrap_dequeue_embed_script() {
    wp_dequeue_script( 'wp-embed' );
}
add_action( 'wp_print_scripts', 'picostrap_dequeue_embed_script', 100 );
add_action( 'wp_print_footer_scripts', 'picostrap_dequeue_embed_script', 0 );
add_action( 'init', 'picostrap_disable_embeds_code_init', 9999 );

function picostrap_disable_embeds_code_init() {
	
	// Turn off oEmbed auto discovery.
	add_filter( 'embed_oembed_discover', '__return_false' );
	
	// Remove oEmbed discovery links.
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 4 );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
	
	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action( 'wp_head', 'wp_oembed_add_host_js' );
	remove_filter( 'embed_oembed_html', 'wp_maybe_enqueue_oembed_host_js' );
	add_filter( 'tiny_mce_plugins', 'picostrap_disable_embeds_tiny_mce_plugin' );
	
	// Remove all embeds rewrite rules.
	add_filter( 'rewrite_rules_array', 'picostrap_disable_embeds_rewrites' );
	
    // Keep wp_filter_pre_oembed_result: local embeds can avoid an HTTP request.
}

function picostrap_disable_embeds_tiny_mce_plugin( $plugins ) {
    return is_array( $plugins ) ? array_diff( $plugins, array( 'wpembed' ) ) : array();
}

function picostrap_disable_embeds_rewrites( $rules ) {
    foreach ( $rules as $rule => $rewrite ) {
        if ( false !== strpos( $rewrite, 'embed=true' ) ) unset( $rules[ $rule ] );
    }
    return $rules;
}

// Share the opt-in condition between early generators and late asset cleanup.
function picostrap_should_remove_block_styles() {
    return ! is_admin()
        && get_theme_mod( 'disable_gutenberg' )
        && ! ( function_exists( 'lc_plugin_option_is_set' ) && lc_plugin_option_is_set( 'gtblocks' ) );
}

function picostrap_wp_remove_global_css() {
    if ( ! picostrap_should_remove_block_styles() ) return;

    remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
    // Modern classic themes also enqueue global styles after rendering blocks.
    remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );
    remove_action( 'wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles' );
    remove_action( 'enqueue_block_assets', 'wp_enqueue_classic_theme_styles' );
    remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );
    // Current WordPress emits duotone CSS and SVGs through these callbacks.
    remove_action( 'wp_enqueue_scripts', array( 'WP_Duotone', 'output_block_styles' ), 9 );
    remove_action( 'wp_enqueue_scripts', array( 'WP_Duotone', 'output_global_styles' ), 11 );
    remove_action( 'wp_footer', array( 'WP_Duotone', 'output_footer_assets' ), 10 );
}

if ( get_theme_mod( 'disable_gutenberg' ) ) add_action( 'init', 'picostrap_wp_remove_global_css' );
