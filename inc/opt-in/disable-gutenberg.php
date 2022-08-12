<?php
 
//////// DISABLE GUTENBERG ////////////////////////////////////////////////////
// this is a purely opt-in feature:
// this code is executed only if the option is enabled in the  Customizer

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//DISABLE GUTENBERG EDITOR, unless LC needs it
add_filter('use_block_editor_for_post', function($in){
	
    //if user wants to use Gutenberg along with LC editor, exit
    if (function_exists('lc_plugin_option_is_set') && lc_plugin_option_is_set('gtblocks')) return $in;
	
	//generally
	return FALSE;

}, 10);


/// REMOVE GUTENBERG BLOCKS CSS - if classic editor plugin is active
add_action( 'wp_print_styles', 'picostrap_deregister_gstyles', 100 );
function picostrap_deregister_gstyles() {

    //if user wants to use Gutenberg along with LC editor, exit
    if (function_exists('lc_plugin_option_is_set') && lc_plugin_option_is_set('gtblocks')) return;
    
	//De - enqueue GT styles
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' ); 
}
