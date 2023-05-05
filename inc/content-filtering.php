<?php

/**
 * Allow LC - powered sites to be rendered perfectly, also if LC plugin is deactivated.
 * Just remove some WP default content filtering on LC-pages
 *
 * @package picostrap5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


/// ALTER CONTENT FILTERING ON PAGES WHERE LIVECANVAS IS ENABLED
add_action('wp', 'pico_alter_content_filters', PHP_INT_MAX);
function pico_alter_content_filters() {

	// IF LC PLUGIN IS TURNED ON, EARLY EXIT
	if (function_exists('lc_post_is_using_livecanvas') ) return;

	//ACT ONLY ON SINGLE (POSTS / PAGES / CPT)
	if (!is_singular()) return;
	
	//IF PAGE IS NOT USING LIVECANVAS, and isnt a lc cpt,  EXIT FUNCTION
	$page_id = get_queried_object_id();  
	if (!is_numeric($page_id) OR get_post_meta($page_id, '_lc_livecanvas_enabled', true) != '1')	return;
	
	//as a quick test
	//die('this will be executed');

	//Got this list from core wp /wp-includes/default-filters.php - might be useful to update it in the future. Wp is now 572
	remove_filter( 'the_content', 'do_blocks', 9 );
	remove_filter( 'the_content', 'wptexturize' );
	remove_filter( 'the_content', 'convert_smilies', 20 );
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'the_content', 'shortcode_unautop' );
	remove_filter( 'the_content', 'prepend_attachment' );
	remove_filter( 'the_content', 'wp_filter_content_tags' );
	remove_filter( 'the_content', 'wp_replace_insecure_home_url' );

	//more to remove, by inspection
	remove_filter( 'the_content', 'capital_P_dangit', 11 ); 
	
	//embedz, thank you rap1s
	remove_filter('the_content', array($GLOBALS['wp_embed'], 'run_shortcode'), 8);
	remove_filter('the_content', array($GLOBALS['wp_embed'], 'autoembed'), 8);

	//add filter to remove useless lc attributes, necessary only when editing
	add_filter('the_content','pico_strip_lc_attributes');

}



function pico_strip_lc_attributes($html){
	$html = str_replace(' editable="inline"', "", $html);
	$html = str_replace(' editable="rich"', "", $html);
	$html = str_replace(' lc-helper="svg-icon"', "", $html);
	//
	$html = str_replace(' lc-helper="background"', " ", $html);
	$html = str_replace(' lc-helper="video-bg"', " ", $html);
	$html = str_replace(' lc-helper="gmap-embed"', " ", $html);
	$html = str_replace(' lc-helper="video-embed"', " ", $html);
	$html = str_replace(' lc-helper="shortcode"', " ", $html);
	$html = str_replace(' lc-helper="image"', " ", $html);
	$html = str_replace(' lc-helper="icon"', " ", $html);
	
	return $html;
}


