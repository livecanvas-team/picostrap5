<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//SUPPORT FUNCTIONS FOR DETERMINING THE RIGHT CSS BUNDLE FILENAME AND LOCATION
function picostrap_get_css_url (){
    //onboarding
    if(get_theme_mod("picostrap_scss_last_filesmod_timestamp",0)==0) return get_stylesheet_directory_uri() . '/'. picostrap_get_css_optional_subfolder_name() . picostrap_get_base_css_filename(); 

    //standard case
    return get_stylesheet_directory_uri() . '/' . picostrap_get_css_optional_subfolder_name() . picostrap_get_complete_css_filename(); 

}

if (!function_exists('picostrap_get_css_optional_subfolder_name')):
    function picostrap_get_css_optional_subfolder_name() { return ""; }
endif;

if (!function_exists('picostrap_get_base_css_filename')):
    function picostrap_get_base_css_filename() { return "styles-bundle.css"; }
endif;

if (!function_exists('picostrap_get_complete_css_filename')):
    function picostrap_get_complete_css_filename() { 
        $filename = picostrap_get_base_css_filename();
        if (is_multisite()) $filename = str_replace('.', '-' . get_current_blog_id() . '.', $filename );
        return $filename;
    }
endif;


//ADD THE MAIN CSS FILE
add_action( 'wp_enqueue_scripts',  function  () {

    //DETERMINE a VERSION NUMBER
    if (current_user_can("administrator")) $version=rand(1,9999); else 
        $version = intval((get_theme_mod("picostrap_scss_last_filesmod_timestamp")) % 999); 
    
    //ENQUEUE THE CSS FILE
    wp_enqueue_style( 'picostrap-styles', picostrap_get_css_url(), array(), $version); 
    
});

///ADD THE MAIN JS FILE
//enqueue js in footer, async
add_action( 'wp_enqueue_scripts', function() {

    //want to override file in child theme? use get_stylesheet_directory_uri in place of get_template_directory_uri
    //this was done for compatibility reasons towards older child themes
    wp_enqueue_script( 'bootstrap5', get_template_directory_uri() . "/js/bootstrap.bundle.min.js#asyncload", array(), null, true );
    
} ,100);

  

//ADD THE CUSTOM HEADER CODE (SET IN CUSTOMIZER)
add_action( 'wp_head', 'picostrap_add_header_code' );
function picostrap_add_header_code() {
    if (!get_theme_mod("picostrap_fonts_header_code_disable")) {
        echo  get_theme_mod("picostrap_fonts_header_code")." ";
    }
    echo get_theme_mod("picostrap_header_code");
}

//ADD THE CUSTOM FOOTER CODE (SET IN CUSTOMIZER)
add_action( 'wp_footer', 'picostrap_add_footer_code' );
function picostrap_add_footer_code() {
	  //if (!current_user_can('administrator'))
      echo get_theme_mod("picostrap_footer_code");
}

//ADD THE CUSTOM CHROME COLOR TAG (SET IN CUSTOMIZER)
add_action( 'wp_head', 'picostrap_add_header_chrome_color' );
function picostrap_add_header_chrome_color() {
	 if (get_theme_mod('picostrap_header_chrome_color')!=""):
        ?><meta name="theme-color" content="<?php echo get_theme_mod('picostrap_header_chrome_color'); ?>" />
	<?php endif;
}


//JS ASYNC ENQUEUE: add an async load option as per https://ikreativ.com/async-with-wordpress-enqueue/
function picostrap_async_scripts($url){
    if ( strpos( $url, '#asyncload') === false )
        return $url;
    else if ( is_admin() )
        return str_replace( '#asyncload', '', $url );
    else
	return str_replace( '#asyncload', '', $url )."' async='async"; 
    }
add_filter( 'clean_url', 'picostrap_async_scripts', 11, 1 );


//UNRENDER-BLOCK CSS- STILL AN EXPERIMENT
// as per https://www.phpied.com/faster-wordpress-rendering-with-3-lines-of-configuration/
// still commented as chrome shows errors, unfinished. Use at your own risk.
/*

function picostrap_hints() {  
    //original demo
    //header("link: </wp-content/themes/phpied2/style.css>; rel=preload, </wp-includes/css/dist/block-library/style.min.css?ver=5.4.1>; rel=preload");
    
    header("link: <".picostrap_get_css_hint_link().">; rel=preload, </wp-includes/css/dist/block-library/style.min.css?ver=".get_bloginfo( 'version' ).">; rel=preload");
    
}
add_action('send_headers', 'picostrap_hints'); 

//function to get relative css url for hints
function picostrap_get_css_hint_link(){
    $css_url_array= explode('/wp-content/', picostrap_get_css_url());
    return '/wp-content/'.$css_url_array[1];
}

//for testing of the picostrap_get_css_hint_link function
if (0) add_action ("wp_loaded",function(){
    echo "/wp-content/themes/phpied2/style.css";
    echo "<br>";
    echo picostrap_get_css_hint_link();
    echo "<br>"; 
    die;
});

*/

