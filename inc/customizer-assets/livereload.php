<?php
 
add_action( 'wp_head', function  () {
	if (!current_user_can('administrator') or isset($_GET['customize_theme']) or get_theme_mod("picostrap_disable_livereload")) return; //exit if not admin
    ?>
    <script>

        //alert("Livereload yo");
        var picostrap_livereload_timeout=1500;
        
        function picostrap_livereload_woodpecker(){
            //console.log("picostrap_livereload_woodpecker start");
            fetch("<?php echo admin_url() ?>?ps_check_sass_changes")
                .then(function(response) {
                    return response.text();
                }).then(function(text) {
                    //console.log(text);
                    if (text==="N") {
                        //no sass change has been detected
                        //console.log("No sass change has been detected");
                        setTimeout(function(){ picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
                    }
                    if (text==="Y") {
                        //sass change has been detected
                        //console.log("Sass change has been detected");
                        picostrap_recompile_sass();
                    }
                }).catch(function(err) {
                    console.log("picostrap_livereload_woodpecker Fetch Error");
                }); 
        } //end function
        


        function picostrap_recompile_sass(){
            console.log("picostrap_recompile_sass start");
            if (document.querySelector("#scss-compiler-output")) document.querySelector("#scss-compiler-output").innerHTML = "<div style='font-size:30px;background:#212337;color:lime;font-family:courier;border:8px solid red;padding:15px;display:block;user-select: none;'>Compiling SCSS....</div>";
            fetch("<?php echo admin_url() ?>?ps_compile_scss&ps_compiler_api=1")
                .then(function(response) {
                    return response.text();
                }).then(function(text) {
                    //console.log(text); //but we don't need it anymore, just needs to include "New CSS bundle"
                    if (text.includes("New CSS bundle")) {
                        //SUCCESS

                        //as there are no errors, clear the output feedback
                        document.querySelector("#scss-compiler-output").innerHTML = ''; 
                        
                        //un-cache the frontend css
                        url = document.getElementById('picostrap-styles-css').href;
                        document.getElementById('picostrap-styles-css').href = url;

                        //retrigger the woodpecker
                        setTimeout(function(){ picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
                    }
                    else {
                        //COMPILE ERRORS
                        document.querySelector("#scss-compiler-output").innerHTML = text; //display errors
                        setTimeout(function(){ picostrap_livereload_woodpecker(); }, picostrap_livereload_timeout);
                    }
                    
                }).catch(function(err) {
                    console.log("picostrap_recompile_sass Fetch Error");
                }); 
        } //end function

         

        //END FUNCTIONS

        //IF CSS DOES NOT LOAD, IT MAY NOT EXIST: REBUILD
        document.querySelector("#picostrap-styles-css").onerror = picostrap_recompile_sass();  

        //ON DOMContentLoaded START THE ENGINE / Like document ready :)
        document.addEventListener('DOMContentLoaded', function(event) {
            //add div for feedback
            document.querySelector("html").insertAdjacentHTML("afterbegin","<div id='scss-compiler-output' style=' position: fixed; z-index: 99999999;'></div>");            
            //trigger the woodpecker
            picostrap_livereload_woodpecker();
        });

        

    </script>
    <?php
});



//HANDLE ps_check_sass_changes  URL 
add_action("admin_init", function (){
    
	if(!is_user_logged_in() OR !current_user_can("administrator") /* OR isset($_GET['customize_theme']) */ ) return; //exit if unlogged
	
	if (isset($_GET['ps_check_sass_changes'])) {
        
        //onboarding
        if(get_theme_mod("picostrap_scss_last_filesmod_timestamp",0)==0) { echo "Y"; die(); } //set_theme_mod("picostrap_scss_last_filesmod_timestamp",picostrap_get_scss_last_filesmod_timestamp());
        
        //DEBUG 
        //echo get_theme_mod("picostrap_scss_last_filesmod_timestamp",0)."<br>".picostrap_get_scss_last_filesmod_timestamp();die;

        //check if timestamps differ 
        if (get_theme_mod("picostrap_scss_last_filesmod_timestamp",0)!=picostrap_get_scss_last_filesmod_timestamp()) echo "Y"; else echo ("N");
        die();
    } 
});

/**
 * Returns unique lists of folders containing files to be compiled
 *
 * @return void
 */
/*
function picostrap_get_scss_files_paths() {
    $files = picostrap_get_scss_files_list();
    $results = [];
    foreach($files as $file) {
        $results[] = dirname($file);
    }
    return array_unique($results);
}
*/


/**
 * Returns a list of scass and css files to be compiled
 *
 * @return void
 */
function picostrap_get_scss_files_list($includeRootFolder = true, $excludeBs5 = true) {
    //get current sass folder directory listing
    $the_directory = WP_CONTENT_DIR . '/themes/' . get_stylesheet() . '/sass/';
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

	$files_listing = picostrap_get_scss_files_list(true, false);

    if (!count($files_listing)) die("<div id='compile-error' style='font-size:20px;background:#212337;color:lime;font-family:courier;border:8px solid red;padding:15px;display:block'> Cannot find SASS folder. Are you sure child theme name is coherent with folder name? </div>");
	
    $mod_time_total=0;
	foreach($files_listing as $file_name):
			$file_stats = stat( $file_name );
			$mod_time_total+= $file_stats['mtime'];
	endforeach;

	return $mod_time_total; 
}
