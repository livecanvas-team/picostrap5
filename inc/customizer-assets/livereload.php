<?php
 
///ENQUEUE THE JS FILE FOR AUTOCOMPILE / LIVERELOAD
add_action( 'wp_enqueue_scripts', 'picostrap_enqueue_livereload_scripts' );

function picostrap_enqueue_livereload_scripts() {
    
    //exit if not appropriate: non admins, customizer, or disabled livereload option is true
    if (!current_user_can('administrator') or isset($_GET['customize_theme'])) return; //exit if not admin

    wp_enqueue_script('picostrap_livereload', get_template_directory_uri().'/inc/customizer-assets/livereload.js', array(), rand(0,100), false	);

	wp_localize_script('picostrap_livereload', 'picostrap_ajax_obj', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'picostrap_livereload' ),
            'disable_livereload' => get_theme_mod("picostrap_disable_livereload")
		)
	);
}

//HANDLE ACTION for AJAX REQUEST: picostrap_check_for_sass_changes    
add_action("wp_ajax_picostrap_check_for_sass_changes", function (){
    
    //exit if unlogged or non admin
	if(!is_user_logged_in() OR !current_user_can("administrator")  ) return; 
	
    //check nonce
    check_ajax_referer('picostrap_livereload', 'nonce');

    //onboarding
    if(get_theme_mod("picostrap_scss_last_filesmod_timestamp_v2", 0) == 0) { echo "<YES>"; die(); } //set_theme_mod("picostrap_scss_last_filesmod_timestamp_v2", picostrap_get_scss_last_filesmod_timestamp());
    
    //DEBUG 
    //echo get_theme_mod("picostrap_scss_last_filesmod_timestamp_v2",0)."<br>".picostrap_get_scss_last_filesmod_timestamp(); die;

    //check if timestamps differ 
    if (get_theme_mod("picostrap_scss_last_filesmod_timestamp_v2", 0) != picostrap_get_scss_last_filesmod_timestamp()) echo "<YES>"; else echo "<NO>";
    
    wp_die();
 
});

 
//HANDLE ACTION for AJAX REQUEST: picostrap_recompile_sass
add_action("wp_ajax_picostrap_recompile_sass", function (){
    
	//exit if unlogged or non admin
	if(!is_user_logged_in() OR !current_user_can("administrator")  ) return; 
	
    //check nonce
    check_ajax_referer('picostrap_livereload', 'nonce');

    //trigger the compiler
    picostrap_generate_css();

    wp_die();
 
});


//HANDLE ACTION for AJAX REQUEST: picostrap_reset_theme_options
add_action("wp_ajax_picostrap_reset_theme_options", function (){
    
	//exit if unlogged or non admin
	if(!is_user_logged_in() OR !current_user_can("administrator")  ) return; 
	
    //check nonce
    check_ajax_referer('picostrap_livereload', 'nonce'); 

    //reset theme options
    remove_theme_mods(); 

    //trigger the compiler
    picostrap_generate_css();

    wp_die();
 
});


/**
 * Returns a list of scss and css files to be compiled
 *
 * @return void
 */
function picostrap_get_scss_files_list($includeRootFolder = true, $excludeBs5 = false) {
    //get current sass folder directory listing
    $the_directory = get_stylesheet_directory().'/sass/';
    $extPattern = '*.{scss,css}';
    $currentFiles = [];

    //Get all files in rootDir if allowed
    if($includeRootFolder) {
        $currentFiles = glob($the_directory . $extPattern, GLOB_BRACE);
    }

    //Get all subdirs
    foreach (glob($the_directory . '*', GLOB_ONLYDIR|GLOB_NOSORT) as $curdir) {

        //skip default bs5 dir
        if ($curdir == $the_directory . 'bootstrap5' && $excludeBs5) continue;
        
        $currentGlob = glob($curdir . '/' . $extPattern, GLOB_BRACE);
        $currentFiles = array_merge(array_values($currentFiles), array_values($currentGlob));
    }

    return $currentFiles;
}

//FUNCTION TO MAKE A TIMESTAMP OF CHILD THEME SASS DIRECTORY
function picostrap_get_scss_last_filesmod_timestamp() {

	$files_listing = picostrap_get_scss_files_list();

    if (!count($files_listing)) wp_die("Cannot read SASS folder. If you're using a custom child theme, make sure its name is coherent with the folder name. Otherwise it can be a server issue, some server security settings can prevent the PHP glob function to work. Picostrap needs it to get the listing of the SCSS files. ");
	
    $mod_time_total=0;
	foreach($files_listing as $file_name):
			$file_stats = stat( $file_name );
			$mod_time_total+= $file_stats['mtime'];
	endforeach;

	return $mod_time_total; 
}
