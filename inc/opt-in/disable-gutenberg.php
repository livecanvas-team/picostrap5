<?php
 
//////// DISABLE GUTENBERG ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//DISABLE GUTENBERG EDITOR, unless for lc_gt_block post type
add_filter('use_block_editor_for_post_type', 'pico_disable_gutenberg', 10, 2);
function pico_disable_gutenberg($current_status, $post_type){

    //on  lc_gt_block posts, GT shall be used
    if ($post_type === 'lc_gt_block') return TRUE;

    //on all other post types, no
    return FALSE;
}


/// REMOVE GUTENBERG BLOCKS CSS
add_action( 'wp_print_styles', 'picostrap_deregister_gstyles', 100 );
// Block styles can be enqueued during the_content, after wp_head has run.
add_action( 'wp_footer', 'picostrap_deregister_gstyles', 19 );
function picostrap_deregister_gstyles() {

    //if user wants to use Gutenberg along with LC editor, exit
    if ( ! picostrap_should_remove_block_styles() ) return;
    
	//De - enqueue GT styles
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' ); 
    wp_dequeue_style( 'classic-theme-styles' );
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'wp-global-styles-placeholder' );
    wp_dequeue_style( 'wp-block-styles-placeholder' );
    wp_dequeue_style( 'core-block-supports' );
    wp_dequeue_style( 'core-block-supports-duotone' );
    wp_dequeue_style( 'block-style-variation-styles' );

    // Since WP 6.9, classic themes load individual core block styles on demand.
    // Keep third-party block assets registered and available.
    foreach ( WP_Block_Type_Registry::get_instance()->get_all_registered() as $block_type ) {
        if ( strpos( $block_type->name, 'core/' ) !== 0 ) continue;

        foreach ( array_merge( $block_type->style_handles ?? array(), $block_type->view_style_handles ?? array() ) as $handle ) {
            wp_dequeue_style( $handle );
        }
    }
}
