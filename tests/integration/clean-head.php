<?php
/**
 * Run with WP-CLI against a disposable installation with Picostrap active:
 * wp --path=/path/to/test-wordpress eval-file /path/to/picostrap5/tests/integration/clean-head.php
 *
 * Tests alter only in-process hooks, registries and queues; no options are saved.
 * Run without LiveCanvas: the gtblocks option is simulated below.
 */
if ( ! defined( 'WP_CLI' ) || ! WP_CLI || ! function_exists( 'picostrap_cleanup' ) ) {
    throw new RuntimeException( 'Run through WP-CLI with Picostrap active.' );
}
if ( function_exists( 'lc_plugin_option_is_set' ) ) {
    throw new RuntimeException( 'Run on a disposable installation without LiveCanvas or gtblocks stubs enabled.' );
}

global $picostrap_test_checks, $picostrap_test_gtblocks, $picostrap_test_disable_gutenberg;
$picostrap_test_checks = 0;
function picostrap_test_check( $condition, $message ) {
    global $picostrap_test_checks;
    if ( ! $condition ) throw new RuntimeException( $message );
    ++$picostrap_test_checks;
    WP_CLI::log( 'PASS ' . $message );
}

// Simulate compatibility and theme settings without touching the database.
$picostrap_test_gtblocks = false;
$picostrap_test_disable_gutenberg = false;
if ( ! function_exists( 'lc_plugin_option_is_set' ) ) {
    function lc_plugin_option_is_set( $option ) {
        global $picostrap_test_gtblocks;
        return 'gtblocks' === $option && $picostrap_test_gtblocks;
    }
}
add_filter( 'theme_mod_disable_gutenberg', function () {
    global $picostrap_test_disable_gutenberg;
    return $picostrap_test_disable_gutenberg;
} );
require_once get_template_directory() . '/inc/opt-in/disable-gutenberg.php';

picostrap_test_check( false === has_action( 'template_redirect', 'wp_shortlink_header' ), 'HTTP shortlink hook removed' );
picostrap_test_check( false === has_action( 'wp_head', 'wp_oembed_add_discovery_links' ), 'Both oEmbed discovery priorities removed' );
picostrap_test_check( false === has_filter( 'embed_oembed_html', 'wp_maybe_enqueue_oembed_host_js' ), 'Automatic wp-embed enqueue callback removed' );
picostrap_test_check( 10 === has_filter( 'oembed_dataparse', 'wp_filter_oembed_result' ), 'oEmbed sanitization retained' );
picostrap_test_check( 10 === has_filter( 'pre_oembed_result', 'wp_filter_pre_oembed_result' ), 'Local oEmbed HTTP shortcut retained' );

// Trusted video HTML must survive; unknown providers must still be sanitized.
$video = '<iframe title="Video" src="https://www.youtube.com/embed/test"></iframe>';
$trusted = apply_filters( 'oembed_dataparse', $video, (object) array( 'type' => 'video', 'title' => 'Video' ), 'https://www.youtube.com/watch?v=test' );
picostrap_test_check( $video === $trusted, 'Trusted video provider HTML unchanged' );
$unsafe = '<iframe title="Video" src="https://unknown.example/video" onload="alert(1)"></iframe><script>alert(1)</script>';
$safe = apply_filters( 'oembed_dataparse', $unsafe, (object) array( 'type' => 'video', 'title' => 'Video' ), 'https://unknown.example/post' );
picostrap_test_check( false !== strpos( $safe, 'sandbox=' ) && false === strpos( $safe, 'onload=' ) && false === strpos( $safe, '<script' ), 'Unknown provider HTML remains sandboxed and sanitized' );
picostrap_test_check( array() === picostrap_disable_embeds_tiny_mce_plugin( null ), 'Invalid TinyMCE embed plugin list handled' );

$hints = array(
    'https://s.w.org/images/core/emoji/17.0.2/svg/',
    array( 'href' => '//s.w.org', 'crossorigin' => 'anonymous' ),
    array( 'href' => 'https://fonts.example', 'crossorigin' => 'anonymous' ),
    'https://s.w.org/images/other/',
);
$filtered = picostrap_disable_emojis_remove_dns_prefetch( $hints, 'dns-prefetch' );
picostrap_test_check( array( 1 => $hints[1], 2 => $hints[2], 3 => $hints[3] ) === $filtered, 'Emoji hints removed without damaging array hints or shared CDN resources' );
picostrap_test_check( $hints === picostrap_disable_emojis_remove_dns_prefetch( $hints, 'preconnect' ), 'Other resource hint relations preserved' );

// Reproduce WordPress loading admin hooks after init, then clean them again.
require_once ABSPATH . 'wp-admin/includes/admin-filters.php';
picostrap_test_check( false !== has_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' ), 'Late admin emoji hooks reproduced' );
picostrap_disable_emojis();
picostrap_test_check( false === has_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' ) && false === has_action( 'admin_print_scripts', 'print_emoji_detection_script' ), 'Admin emoji hooks removed after registration' );
picostrap_test_check( false === has_action( 'enqueue_embed_scripts', 'wp_enqueue_emoji_styles' ) && false === has_action( 'embed_head', 'print_emoji_detection_script' ), 'Embed template emoji hooks removed' );

$home_filter = function () { return 'https://site.example/blog/'; };
add_filter( 'home_url', $home_filter );
$links = array(
    'https://site.example/blog/post',
    'http://site.example/blog?query=1',
    'https://SITE.EXAMPLE/blog/',
    'https://site.example:443/blog/post',
    'http://site.example:80/blog/post',
    'https://site.example/blog-other/post',
    'https://site.example.evil/blog/post',
    'https://site.example:8888/blog/post',
    'https://another.example/blog/post',
);
$external = array_slice( $links, 5 );
disable_self_pingbacks( $links );
picostrap_test_check( $external === array_values( $links ), 'Self-pings match host, port and path boundaries' );
remove_filter( 'home_url', $home_filter );

$original_scripts = $GLOBALS['wp_scripts'] ?? null;
$GLOBALS['wp_scripts'] = new WP_Scripts();
wp_enqueue_script( 'wp-embed' );
picostrap_dequeue_embed_script();
picostrap_test_check( ! wp_script_is( 'wp-embed', 'enqueued' ) && wp_script_is( 'wp-embed', 'registered' ), 'wp-embed dequeued without deregistration' );
wp_register_script( 'audit-dependent', 'https://example.test/plugin.js', array( 'wp-embed' ), null );
ob_start();
wp_print_scripts( array( 'audit-dependent' ) );
$script_output = ob_get_clean();
picostrap_test_check( false !== strpos( $script_output, 'wp-embed' ) && false !== strpos( $script_output, 'plugin.js' ), 'Explicit plugin dependency on wp-embed still resolves' );
$GLOBALS['wp_scripts'] = $original_scripts;

// Cleanup must remove individual core styles while retaining plugin styles.
register_block_type( 'core/picostrap-audit', array( 'api_version' => 3, 'style_handles' => array( 'audit-core-style' ), 'view_style_handles' => array( 'audit-core-view-style' ) ) );
register_block_type( 'audit/plugin', array( 'api_version' => 3, 'style_handles' => array( 'audit-plugin-style' ) ) );
$handles = array( 'wp-block-library', 'global-styles', 'core-block-supports-duotone', 'block-style-variation-styles', 'audit-core-style', 'audit-core-view-style', 'audit-plugin-style' );
foreach ( $handles as $handle ) {
    wp_register_style( $handle, false );
    wp_enqueue_style( $handle );
}
picostrap_deregister_gstyles();
picostrap_test_check( wp_style_is( 'audit-core-style', 'enqueued' ), 'Core styles kept with Gutenberg enabled' );
$picostrap_test_disable_gutenberg = true;
$picostrap_test_gtblocks = true;
picostrap_deregister_gstyles();
picostrap_test_check( wp_style_is( 'audit-core-style', 'enqueued' ), 'Core styles kept with LiveCanvas gtblocks enabled' );
$picostrap_test_gtblocks = false;
picostrap_deregister_gstyles();
foreach ( array_diff( $handles, array( 'audit-plugin-style' ) ) as $handle ) {
    picostrap_test_check( ! wp_style_is( $handle, 'enqueued' ), 'Disabled Gutenberg removes ' . $handle );
}
picostrap_test_check( wp_style_is( 'audit-plugin-style', 'enqueued' ), 'Third-party block stylesheet kept' );
picostrap_wp_remove_global_css();
picostrap_test_check( false === has_action( 'wp_footer', 'wp_enqueue_global_styles' ), 'Footer global style generator removed' );
picostrap_test_check( false === has_action( 'wp_footer', array( 'WP_Duotone', 'output_footer_assets' ) ), 'Unused duotone CSS and SVG generator removed' );

WP_CLI::success( $picostrap_test_checks . ' cleanup assertions passed.' );
