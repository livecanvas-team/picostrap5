<?php
/** Shared Extras: the Customizer and backend edit the same theme modifications. */
defined( 'ABSPATH' ) || exit;

function picostrap_get_extra_settings() {
    return array(
        'disable_gutenberg' => array(
            'label' => __( 'Disable Gutenberg and core block styles', 'picostrap5' ),
            'description' => __( 'Disables the Gutenberg block editor for posts and pages, restoring the classic editor. Also removes frontend WordPress CSS for the core block library, individual core blocks, global styles and classic theme styles, along with core block support and duotone CSS/SVG assets. Use for sites built entirely with LiveCanvas or other layouts that do not need WordPress blocks. Existing block content is kept, but may lose its styling. LiveCanvas Gutenberg compatibility preserves block styles and keeps the editor available for LiveCanvas Gutenberg blocks. Third-party block assets are not removed by this setting. The block-based Widgets Editor has its own separate switch.', 'picostrap5' ),
        ),
        'disable_widgets_block_editor' => array(
            'label' => __( "Disable the Block-based Widgets Editor", 'picostrap5' ),
            'description' => __( "Disables the Block-based Widgets Editor and restores the classic widgets editor.", 'picostrap5' ),
        ),
        'singlepost_disable_comments' => array(
            'label' => __( "Disable WordPress Comments", 'picostrap5' ),
            'description' => __( "Will completely disable the entire WP comments feature.", 'picostrap5' ),
        ),
        'disable_xml_rpc' => array(
            'label' => __( "Disable XML - RPC", 'picostrap5' ),
            'description' => __( "Disable the legacy XML-RPC interface.", 'picostrap5' ),
        ),
        'enable_back_to_top' => array(
            'label' => __( "Add a 'Back to Top' button to site", 'picostrap5' ),
            'description' => __( "Very light implementation. To see the button, you will also need to Publish, exit the Customizer, and scroll down a long page", 'picostrap5' ),
        ),
        'enable_open_menu_on_hover' => array(
            'label' => __( "Open the main menu on hover", 'picostrap5' ),
            'description' => __( "Very light implementation. To see the change, you will also need to Publish, exit the Customizer, and scroll down a long page", 'picostrap5' ),
        ),
        'enable_lightbox' => array(
            'label' => __( "Enable Lightbox", 'picostrap5' ),
            'description' => __( "Will lazily add one JS and one CSS file from cdn.jsdelivr.net before closing the BODY of the page, to let you use  <a target='_blank' href='https://github.com/biati-digital/glightbox'>gLightBox</a>: a very lightweight lightbox implementation. <br><br>The lightbox will be triggered on single posts and pages, automatically adding the <b>glightbox</b> class to each IMG in an A element.<br><br>To add the lightbox to other elements arbitrarily, add the <b>glightbox</b> class.<br><br> To prevent the lightbox on a linked image, add the <b>nolightbox</b> class.<br><br>To lightbox all images inside a DIV, add the <b>autolightbox</b> class.", 'picostrap5' ),
        ),
        'enable_tooltips' => array(
            'label' => __( "Enable Tooltips & Popovers", 'picostrap5' ),
            'description' => __( "Adds inline <a target='_blank' href='https://getbootstrap.com/docs/5.2/components/tooltips/#enable-tooltips'>two rows of JavaScript</a> to enable Boostrap 5 tooltips and popovers. Publish and exit the Customizer to see the change.", 'picostrap5' ),
        ),
        'disable_bootstrap' => array(
            'label' => __( "Disable Bootstrap", 'picostrap5' ),
            'description' => __( "Advanced option: enable only if you know what you are doing. Removes the entire theme CSS bundle, including Bootstrap, NinjaBootstrap and custom Sass styles, as well as Bootstrap JavaScript. You must add your own CSS to style the site and provide replacement JavaScript for any Bootstrap interactions you still need.", 'picostrap5' ),
        ),
        'picostrap_inline_css' => array(
            'label' => __( 'Page-specific CSS — remove the bundle CSS request and serve page-specific styles directly inline.', 'picostrap5' ),
            'description' => __( 'Experimental: replaces the external theme CSS bundle request with reduced CSS embedded directly in each page HTML. PHP reads the compiled bundle, removes rules it can identify as unnecessary for that page and inserts the remaining styles inline. The bundle file is not deleted or compilation disabled: it remains the source and fallback. Plugin stylesheets are separate. Normal frontend visits can use reduced CSS for both anonymous and logged-in users. LiveCanvas editing, previews, the Customizer and Sass compilation keep the original bundle; safety checks can also keep it, and a JavaScript guard can load it again for dynamic content. A higher PageSpeed score is not guaranteed: compare results with this option on and off, including repeat visits with a warm cache. Test layouts, menus, galleries, forms and other dynamic content on desktop and mobile. Clear your page cache after changing this setting. If styling or interactions break, disable it and clear the cache again.', 'picostrap5' ),
        ),
    );
}

add_action( 'admin_post_picostrap_save_extras', 'picostrap_save_extra_settings' );
function picostrap_save_extra_settings() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_die( esc_html__( 'You are not allowed to edit theme settings.', 'picostrap5' ) );
    }
    check_admin_referer( 'picostrap_save_extras' );
    $settings = isset( $_POST['picostrap_extras'] ) && is_array( $_POST['picostrap_extras'] ) ? wp_unslash( $_POST['picostrap_extras'] ) : array();
    foreach ( picostrap_get_extra_settings() as $key => $definition ) {
        set_theme_mod( $key, isset( $settings[ $key ] ) && '1' === $settings[ $key ] );
    }
    wp_safe_redirect( admin_url( 'themes.php?page=picostrap-theme-options&extras-saved=1#pico-extras-title' ) );
    exit;
}
