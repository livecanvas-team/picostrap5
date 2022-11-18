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
function picostrap_deregister_gstyles() {

    //if user wants to use Gutenberg along with LC editor, exit
    if (function_exists('lc_plugin_option_is_set') && lc_plugin_option_is_set('gtblocks')) return;
    
	//De - enqueue GT styles
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' ); 
    wp_dequeue_style( 'classic-theme-styles' );
}
