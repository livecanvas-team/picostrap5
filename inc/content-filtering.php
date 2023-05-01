<?php
//TO ALLOW LC SITES TO BE RENDERED PERFECTLY EVEN IF PLUGIN IS DEACTIVATED
//JUST REMOVES SOME WORDPRESS FILTERS ON LC-PAGES


/// ALTER CONTENT FILTERING ON PAGES WHERE LIVECANVAS IS ENABLED
add_action('wp', 'pico_alter_content_filters', PHP_INT_MAX);
function pico_alter_content_filters() {

	// IF LC PLUGIN IS TURNED ON, EARLY EXIT
	if ( pico_is_plugin_active( 'livecanvas/livecanvas-plugin-index.php' ) ) return;

	// for a double security: in case lc_alter_content_filters is defined, lc plugin exists:exit
	if (function_exists('lc_alter_content_filters') ) return;
	
	//IF PAGE IS NOT USING LIVECANVAS, and isnt a lc cpt,  EXIT FUNCTION
	$page_id = get_queried_object_id();  
	if (!pico_post_is_using_livecanvas($page_id))	return;
	
	//as a quick test
	//die('test');

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


//FUNCTION TO DETERMINE IF POST IS USING LIVECANVAS
function pico_post_is_using_livecanvas($post_id) {
	
	return (
		in_array(get_post_type($post_id), array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_template')) OR 
		get_post_meta($post_id, '_lc_livecanvas_enabled', true) == '1'
	);

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

//SUPPORT FUNCTIONS
function pico_is_plugin_active_for_network( $plugin ) {
	if ( ! is_multisite() ) {
		return false;
	}

	$plugins = get_site_option( 'active_sitewide_plugins' );
	if ( isset( $plugins[ $plugin ] ) ) {
		return true;
	}

	return false;
}

function pico_is_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || pico_is_plugin_active_for_network( $plugin );
}