<?php

////   PICOSASS JS INTEGRATION ////

//FOR THE CUSTOMIZER AND FRONTEND SCSS COMPILER: ADD TO HEADER 
add_action( 'wp_head', function  () {
	if (!current_user_can('administrator') ) return;
	if (!isset($_GET['customize_theme']) && !isset($_GET['compile_sass'])) return;
    ?>
		<!-- add picoSASS JS --> 
		<script type="module" src="<?php echo get_template_directory_uri() ?>/inc/customizer-assets/picosass/picosass.js"></script>

		<!-- add the SCSS source code --> 
		<template id="the-scss" class="prevent-autocompile" baseurl="<?php echo get_stylesheet_directory_uri() ?>/">
			<?php echo ps_get_main_sass() ?>
		</template>
	<?php
} );

//FOR THE CUSTOMIZER AND FRONTEND SCSS COMPILER: ADD TO FOOTER 
add_action( 'wp_footer', function  () {
	if (!current_user_can('administrator') ) return;
	if (!isset($_GET['customize_theme']) && !isset($_GET['compile_sass'])) return;
    ?>
		<!-- mark the static theme CSS as provisional --> 
		<script>
			document.querySelector("#picostrap-styles-css").classList.add("picostrap-provisional-css");
		</script>
	<?php
} );

// FOR THE FRONTEND SCSS COMPILER ONLY / NOT FOR THE CUSTOMIZER: ADD TO FOOTER
add_action( 'wp_footer', function  () {
	
	if (!current_user_can('administrator') ) return;
	if (isset($_GET['customize_theme']) OR  !isset($_GET['compile_sass'])) return;
    ?>
		<script>

			//init var
			let lastCssBundle='';

			//DEFINE CALLBACK FOR SAVING AFTER COMPILING
			function compilingSassFinishedCallback(compiled) { 
				//console.log("About to save the CSS bundle");
			 
				// check if saving is needed or return
				if (lastCssBundle!=compiled.css) {

					//build the request to send via AJAX POST for saving css
					const formdata = new FormData();
					formdata.append("nonce", "<?php echo wp_create_nonce("picostrap_save_css_bundle") ?>");
					formdata.append("action", "picostrap_save_css_bundle");
					formdata.append("css", compiled.css);
					fetch("<?php echo admin_url( 'admin-ajax.php' ) ?>", {
						method: "POST",
						credentials: "same-origin",
						headers: {
							"Cache-Control": "no-cache",
						},
						body: formdata
					}).then(response => response.text())
						.then(response => {
							
							console.log("Saved successfully: " + response);
							
						}).catch(function (err) {
							console.log("ps_save_css_bundle Error: "+err);
						}); 

					lastCssBundle=compiled.css;
				}

				//check if browser tab has focus, retrigger
				if (document.visibilityState === 'visible') {
					//schedule for later
					setTimeout(function () { 
						window.Picosass.Compile({}, compilingSassFinishedCallback);
					 }, 7000);
					return;
				}

			}

			/////// ON DOM CONTENT LOADED, RUN COMPILER
			window.addEventListener("DOMContentLoaded", (event) => {

				window.Picosass.Compile({}, compilingSassFinishedCallback);
				 
			}); //end onDOMContentLoaded

		</script>
		<style>
			#picosass-output-feedback {
    			top: 32px !important;
			}
		</style>
	<?php
} );
 
//BUILD SASS MAIN CODE FROM VARIABLES & VALUES IN THEME MODS, AND AND main SCSS FILE
function ps_get_main_sass(){
	
	$sass='';
	
	if (get_theme_mods()) foreach(get_theme_mods() as $theme_mod_name => $theme_mod_value):
		
		//check we are treating a scss variable, or skip
		if(substr($theme_mod_name,0,8) != "SCSSvar_") continue;

		//for boolean vars
		if( strpos($theme_mod_name, 'enable-') !== false  ) $theme_mod_value = ($theme_mod_value == 1) ?  'true' : 'false'; 

		//skip empty values to prevent compiler error
		if($theme_mod_value == "" ) continue;
		
		//get the real sass variable name from theme_mod_name, getting rid of our custom prefix
		$variable_name = str_replace("SCSSvar_", "$", $theme_mod_name);
		
		//add to output array sass += `$${name}: ${els[i].value}; `;
		$sass .= $variable_name . ': '.$theme_mod_value . '; ';
		
	endforeach;
	
	return $sass . " @import 'sass/main'; "; 
}

//HANDLE AJAX ACTION FOR SAVING CSS BUNDLE
add_action("wp_ajax_picostrap_save_css_bundle", function (){
    
	//exit if unlogged or non admin
	if(!is_user_logged_in() OR !current_user_can("administrator")  ) return; 
	
    //check nonce
    check_ajax_referer('picostrap_save_css_bundle', 'nonce');

	//ADD SOME COMMENT
	$compiled_css = stripslashes($_POST['css']);

	//INIT WP FILESYSTEM 
	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once (ABSPATH . '/wp-admin/includes/file.php');
		WP_Filesystem();
	}

	//SAVE THE FILE
	$saving_operation = $wp_filesystem->put_contents( get_stylesheet_directory() . '/' . picostrap_get_css_optional_subfolder_name() . picostrap_get_complete_css_filename(), $compiled_css, FS_CHMOD_FILE ); // , 0644 ?
	
	if ($saving_operation) { // IF UPLOAD WAS SUCCESSFUL 

		//STORE CSS BUNDLE VERSION NUMBER
		$current_version_number = get_theme_mod ('css_bundle_version_number');
		if (!is_numeric($current_version_number)) $current_version_number=rand(1,1000);
		set_theme_mod ('css_bundle_version_number', $current_version_number+1);

		//GIVE POSITIVE FEEDBACK	
		echo "New CSS bundle successfully saved. ";

	} else {
		//GIVE NEGATIVE FEEDBACK
		echo  "Error writing CSS file";
	}
  
    wp_die();
});



// ADD RECOMPILE TRIGGER LINK TO ADMIN BAR
add_action('admin_bar_menu', 'ps_add_toolbar_items', 100);
function ps_add_toolbar_items($admin_bar) {

	//check if user has rights 
	if (!current_user_can("administrator")) return;
	
	if (is_admin())	return; //ALLOW ONLY ON FRONTEND
	
	//if (!is_child_theme()) return; // KEEP COMMENTED - UNCOMMENT TO TEST ON ORIG THEME FOR CONSISTENCY

	global $wp_admin_bar;

	$wp_admin_bar->add_node(array(
		'id' => 'ps-recompile-sass',
		'title' => __('Recompile SASS', 'picostrap'),
		'href' => add_query_arg(array(
			'compile_sass' => '1',
			'sass_nocache'=> '1',
		))
	));		 
	
	if (!isset($_GET['compile_sass'])) return;

	$wp_admin_bar->add_node(array(
		'id' => 'ps-recompile-sass-finish',
		'title' => __('Finish Editing', 'picostrap'),
		'href' => add_query_arg(array(
			'compile_sass' => false,
			'sass_nocache'=> false,
		))
	));		 
} 
