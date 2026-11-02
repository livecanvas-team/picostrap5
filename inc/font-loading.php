<?php
/** Inspect font-loading output without changing its frontend ownership or saved settings. */
defined( 'ABSPATH' ) || exit;

function picostrap_get_wordpress_font_header_code() {
    if ( ! function_exists( 'wp_print_font_faces' ) ) return '';
    ob_start();
    wp_print_font_faces();
    return trim( ob_get_clean() );
}

function picostrap_get_font_header_preview( $theme_code, $theme_disabled, $wordpress_code ) {
    $parts = array();
    if ( ! $theme_disabled && trim( (string) $theme_code ) ) $parts[] = trim( $theme_code );
    if ( trim( (string) $wordpress_code ) ) $parts[] = trim( $wordpress_code );
    return implode( "\n\n", $parts );
}

add_action( 'customize_controls_enqueue_scripts', function () {
    wp_enqueue_script( 'picostrap-font-loading-preview', get_template_directory_uri() . '/inc/customizer-assets/font-loading-preview.js', array( 'customize-controls' ), filemtime( __DIR__ . '/customizer-assets/font-loading-preview.js' ), true );
    wp_localize_script( 'picostrap-font-loading-preview', 'picostrapFontLoadingLabels', array(
        'mixed' => __( 'Theme font code and WordPress font-face rules are shown below.', 'picostrap5' ),
        'local' => __( 'WordPress generates these font-face rules. No theme font code is needed for these fonts.', 'picostrap5' ),
        'theme' => __( 'Theme font code is shown below. No WordPress font-face rules are currently enabled.', 'picostrap5' ),
        'empty' => __( 'No theme font code or WordPress font-face rules are currently enabled.', 'picostrap5' ),
        'disabled' => __( 'Theme font code is disabled. WordPress font-face rules remain enabled.', 'picostrap5' ),
        'disabledEmpty' => __( 'Theme font code is disabled. No WordPress font-face rules are currently enabled.', 'picostrap5' ),
        'stale' => __( 'WordPress font rules could not be refreshed. The last retrieved rules are shown; try Refresh list in the picker.', 'picostrap5' ),
    ) );
} );
