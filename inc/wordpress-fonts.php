<?php
/** Optional WordPress Font Library choices for the existing Sass typography controls. */
defined( 'ABSPATH' ) || exit;

function picostrap_wordpress_fonts_available() {
    global $wp_version;
    return version_compare( $wp_version, '7.0', '>=' )
        && function_exists( 'wp_font_library_wp_admin_render_page' )
        && function_exists( 'wp_get_global_settings' )
        && function_exists( 'wp_print_font_faces' );
}

/** Read enabled faces, not every installed font. Core remains responsible for their output. */
function picostrap_get_wordpress_fonts() {
    if ( ! picostrap_wordpress_fonts_available() ) return array();
    $groups = wp_get_global_settings( array( 'typography', 'fontFamilies' ) );
    $fonts = array();
    foreach ( (array) $groups as $families ) {
        foreach ( (array) $families as $definition ) {
            if ( ! is_array( $definition ) || empty( $definition['slug'] ) || empty( $definition['fontFamily'] ) || empty( $definition['fontFace'] ) ) continue;
            $variants = array();
            foreach ( (array) $definition['fontFace'] as $face ) {
                if ( ! is_array( $face ) || empty( $face['src'] ) ) continue;
                $variants[] = trim( (string) ( $face['fontWeight'] ?? '400' ) . ' ' . (string) ( $face['fontStyle'] ?? 'normal' ) );
            }
            if ( ! $variants ) continue;
            // Match core's first-family resolution, then escape the value as a Sass string.
            $family = trim( explode( ',', $definition['fontFamily'] )[0], " \t\n\r\0\x0B\"'" );
            if ( '' === $family ) continue;
            $slug = sanitize_title( $definition['slug'] );
            $fonts[ $slug ] = array(
                'name' => wp_strip_all_tags( $definition['name'] ?? $family ),
                'family' => $family,
                'sass' => '"' . strtr( $family, array( '\\' => '\\\\', '"' => '\\"', '$' => '\\$', '#' => '\\#', "\n" => ' ', "\r" => ' ' ) ) . '"',
                'variants' => implode( ', ', array_unique( $variants ) ),
            );
        }
    }
    uasort( $fonts, function ( $a, $b ) { return strnatcasecmp( $a['name'], $b['name'] ); } );
    return $fonts;
}

add_action( 'customize_register', function ( $customize ) {
    if ( ! picostrap_wordpress_fonts_available() ) return;
    foreach ( array( 'body', 'headings' ) as $role ) {
        $id = 'picostrap_wp_font_' . $role;
        $customize->add_setting( $id, array(
            'default' => '',
            'transport' => 'postMessage',
            'sanitize_callback' => 'sanitize_title',
        ) );

    }
}, 20 );

add_action( 'customize_controls_enqueue_scripts', function () {
    if ( ! picostrap_wordpress_fonts_available() ) return;
    wp_enqueue_script( 'picostrap-host-font', get_template_directory_uri() . '/inc/customizer-assets/host-font-locally.js', array( 'wp-api-fetch' ), filemtime( __DIR__ . '/customizer-assets/host-font-locally.js' ), true );
    wp_enqueue_script( 'picostrap-wordpress-fonts', get_template_directory_uri() . '/inc/customizer-assets/wordpress-fonts.js', array( 'custom-customize', 'fontpicker', 'picostrap-host-font' ), filemtime( __DIR__ . '/customizer-assets/wordpress-fonts.js' ), true );
    wp_localize_script( 'picostrap-wordpress-fonts', 'picostrapWordPressFontData', array(
        'fonts' => picostrap_get_wordpress_fonts(),
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'picostrap_wordpress_fonts' ),
        'canImport' => current_user_can( 'upload_files' ) && current_user_can( 'edit_theme_options' ),
        'stylesheet' => get_stylesheet(),
        'hostedLabel' => __( 'Hosted font: ', 'picostrap5' ),
        'manageUrl' => admin_url( 'font-library.php?picostrap-font-modal=1' ),
        'libraryUrl' => admin_url( 'font-library.php' ),
        'missing' => __( 'Selected font is unavailable. Enable its variants in WordPress Fonts, or choose another font. The family setting is retained and CSS fallback applies.', 'picostrap5' ),
        'empty' => __( 'No hosted fonts are enabled yet. Add a font in the library, enable the variants you need and click Update.', 'picostrap5' ),
        'error' => __( 'Could not refresh fonts. Try again. Your selections have been kept.', 'picostrap5' ),
    ) );
} );

// Create the native global-styles record only after an explicit import action.
add_action( 'wp_ajax_picostrap_font_import_context', function () {
    check_ajax_referer( 'picostrap_wordpress_fonts', 'nonce' );
    if ( ! picostrap_wordpress_fonts_available() || ! current_user_can( 'edit_theme_options' ) || ! current_user_can( 'upload_files' ) ) wp_send_json_error( null, 403 );
    $directory = wp_get_font_dir();
    if ( ! empty( $directory['error'] ) ) wp_send_json_error( array( 'message' => __( 'The WordPress fonts directory is not writable. Check its permissions or use the Font Library.', 'picostrap5' ) ), 400 );
    wp_send_json_success( array(
        'stylesId' => WP_Theme_JSON_Resolver::get_user_global_styles_post_id(),
        'stylesheet' => get_stylesheet(),
        'fontDirectoryUrl' => $directory['url'],
    ) );
} );

// Refresh the catalog without reloading the Customizer or losing unpublished changes.
add_action( 'wp_ajax_picostrap_wordpress_fonts', function () {
    check_ajax_referer( 'picostrap_wordpress_fonts', 'nonce' );
    if ( ! current_user_can( 'edit_theme_options' ) ) wp_send_json_error( null, 403 );
    wp_send_json_success( array(
        'fonts' => picostrap_get_wordpress_fonts(),
        'headerCode' => picostrap_get_wordpress_font_header_code(),
    ) );
} );

// Use the native authenticated screen, with admin chrome hidden only inside this dialog.
add_action( 'admin_head', function () {
    global $pagenow;
    if ( 'font-library.php' !== $pagenow || ! isset( $_GET['picostrap-font-modal'] ) || '1' !== $_GET['picostrap-font-modal'] ) return;
    ?>
    <style>
        html.wp-toolbar { padding-top: 0 !important; }
        html { --wp-admin--admin-bar--height: 0px; background: #fff; }
        #adminmenumain, #wpadminbar, #wpfooter { display: none !important; }
        #wpcontent { margin-left: 0 !important; }
        #wpbody-content { padding-bottom: 0 !important; }
        /* Fill the iframe after removing admin chrome; only the outer dialog owns the corners. */
        #wpwrap, .boot-layout-container { min-height: 100dvh; }
        .boot-layout-container .boot-layout { min-height: 100dvh !important; background: #fff; }
        .boot-layout__canvas, .boot-layout__stage, .boot-layout__inspector { border-radius: 0 !important; box-shadow: none !important; }
        .boot-layout__canvas { border: 0 !important; }
    </style>
    <?php
} );

// Core's declarations also make hosted previews available in the picker itself.
add_action( 'customize_controls_print_styles', function () {
    if ( picostrap_wordpress_fonts_available() ) wp_print_font_faces();
} );
