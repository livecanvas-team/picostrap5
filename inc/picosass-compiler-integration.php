<?php

////   PICOSASS JS INTEGRATION ////


//FOR THE CUSTOMIZER AND FRONTEND SCSS COMPILER: ADD TO HEADER SOME PRELOADING FOR SCSS FILES
 
add_action( 'wp_head', function  () {
	if (!current_user_can('administrator') ) return;
	if (!isset($_GET['customize_theme']) && !isset($_GET['compile_sass'])) return;
    
    //preload theme scss
    $theme_filenames_str = "_bootstrap-loader _custom _picostrap _theme_variables _woocommerce _wp_basic_styles main ninjabootstrap/_borders ninjabootstrap/_letter-spacing ninjabootstrap/_positioning ninjabootstrap/_sizing ninjabootstrap/_theme_colors_shades ninjabootstrap/_variables";
	$theme_filenames_arr = explode (' ',$theme_filenames_str);
	foreach($theme_filenames_arr as $theme_filename) {
		?> 
		 
        <link rel="prefetch" href="<?php echo get_stylesheet_directory_uri()."/sass/".$theme_filename ?>.scss" />
		<?php
	}
    //preload bs subfolder
	$bs_filenames_str = "bootstrap mixins/_banner _functions _variables _maps _mixins vendor/_rfs mixins/_deprecate mixins/_breakpoints mixins/_color-scheme mixins/_image mixins/_resize mixins/_visually-hidden mixins/_reset-text mixins/_text-truncate mixins/_utilities mixins/_alert mixins/_backdrop mixins/_buttons mixins/_caret mixins/_pagination mixins/_lists mixins/_list-group mixins/_forms mixins/_table-variants mixins/_border-radius mixins/_box-shadow mixins/_gradients mixins/_transition mixins/_clearfix mixins/_container mixins/_grid _tables _forms forms/_labels forms/_form-text forms/_form-control forms/_form-select forms/_form-check forms/_form-range forms/_floating-labels forms/_input-group forms/_validation _buttons _transitions _dropdown _button-group _nav _navbar _card _accordion _breadcrumb _pagination _badge _alert _progress _list-group _close _toasts _modal _tooltip _popover _carousel _spinners _offcanvas _placeholders _helpers helpers/_clearfix helpers/_color-bg helpers/_colored-links helpers/_ratio helpers/_position helpers/_stacks helpers/_visually-hidden helpers/_stretched-link helpers/_text-truncation helpers/_vr utilities/_api";
	$bs_filenames_arr = explode (' ',$bs_filenames_str);
	foreach($bs_filenames_arr as $bs_filename) {
		?> 
		<link rel="prefetch" href="<?php echo get_stylesheet_directory_uri()."/sass/bootstrap5/".$bs_filename ?>.scss" />
		<?php
	}
} );
 

//PREVENT WP's 404 in /sass/ folder: useless, kept only for messing around
/* 
add_action('template_redirect', 'custom_scss_handler');
function custom_scss_handler() {
    $uri = $_SERVER['REQUEST_URI'];
    if (preg_match('/\/sass\/.*\.scss/', $uri)) {
        if (!file_exists(get_stylesheet_directory() . $uri)) {
            status_header(404);  // Override the WordPress 404 header
            // Handle the request; possibly serve an alternative file or message.
            echo "File not found.";
            exit();
        }
    }
}
*/

//FOR THE CUSTOMIZER AND FRONTEND SCSS COMPILER: ADD TO HEADER 
add_action( 'wp_head', function  () {
	if (!current_user_can('administrator') ) return;
	if (!isset($_GET['customize_theme']) && !isset($_GET['compile_sass'])) return;
    ?>
		<!-- add picoSASS JS --> 
		<script type="module" src="<?php echo get_template_directory_uri() ?>/inc/picosass/picosass.js"></script>

		<!-- add the SCSS source code --> 
		<template id="the-scss" class="prevent-autocompile" baseurl="<?php echo get_stylesheet_directory_uri() ?>/sass/"
		<?php if (is_child_theme()): ?> fallback_baseurl="<?php echo get_template_directory_uri() ?>/sass/" <?php endif ?>
		>
			<?php echo ps_get_main_sass() ?>
		</template>
	<?php
} );

//FOR THE CUSTOMIZER AND FRONTEND SCSS COMPILER: ADD TO FOOTER 
add_action( 'wp_footer', function  () {
	if (!current_user_can('administrator') ) return;
	if (!isset($_GET['customize_theme']) && !isset($_GET['compile_sass'])) return;
    ?>
		<script>
			//mark the static theme CSS as provisional 
			document.querySelector("#picostrap-styles-css").classList.add("picostrap-provisional-css");

			//check there is a connection to the internet
			if (!navigator.onLine) {alert("You need to be online to be able to use the frontend SCSS compiler"); throw new Error("No network");}

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

				<?php if (isset($_GET['autorecompile'])){ ?>

				//Recompile in a few seconds
				setTimeout(function () {
					window.Picosass.Compile({}, compilingSassFinishedCallback);
				}, 7000);

				<?php } else { ?>
				
				//Done. Redirect away in a while to exit
				setTimeout(function () {
					// Create a URL object from the current URL
					const url = new URL(window.location.href);

					// Remove all query parameters
					url.search = "";

					// Redirect to the new URL
					window.location.href = url.href;
				}, 3000);
 
				<?php } ?>
			} //end function compilingSassFinishedCallback


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
 


// USEFUL FOR INSPECTING: SHOW THEME MODS WHEN  ?ps_show_mods
add_action("init", function (){
	if (!current_user_can("administrator")) return; //ADMINS ONLY
	if (isset($_GET['ps_show_mods'])){ print_r(get_theme_mods()); wp_die();	}
});


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
		
		//add to output array. In JS that is sass += `$${name}: ${els[i].value}; `;
		$sass .= $variable_name . ': '.$theme_mod_value . '; ';
		
	endforeach;
	
	return $sass . " @import 'main'; "; 
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
	
	//if (!is_child_theme()) return; // COMMENT TO TEST ON ORIG THEME FOR CONSISTENCY

	global $wp_admin_bar;

	if (!isset($_GET['autorecompile'])) {

		//MAIN MENU ELEMENT
		$wp_admin_bar->add_node(array(
			'id' => 'ps-recompile-sass', 
			'title' => '<span id="icon-picostrap-sass"></span>' . __(' Compiler', 'picostrap'),
			'href' => add_query_arg(array(
					'compile_sass' => '1',
					'sass_nocache'=> '1',
					'autorecompile'=> FALSE,
				)),
		));		 

		//ADD CHILDREN
		$wp_admin_bar->add_node(array(
			'id' => 'ps-recompile-sass-once',
			'parent' => 'ps-recompile-sass',
			'title' =>  __('Recompile Once', 'livecanvas'),
			'href' => add_query_arg(array(
				'compile_sass' => '1',
				'sass_nocache'=> '1',
				'autorecompile'=> FALSE,
				)),
		));

		$wp_admin_bar->add_node(array(
			'id' => 'ps-recompile-sass-automatic',
			'parent' => 'ps-recompile-sass',
			'title' =>  __('Recompile Continuously', 'livecanvas'),
			'href' => add_query_arg(array(
				'compile_sass' => '1',
				'sass_nocache'=> '1',
				'autorecompile'=> '1',
				)),
		));

	} else {
		//sass autorecompile is active, print button to shutdown
		$wp_admin_bar->add_node(array(
			'id' => 'ps-recompile-sass', 
			'title' => '<span id="icon-picostrap-sass"></span>' . __('Stop Compiler', 'picostrap'),
			'href' => add_query_arg(array(
					'compile_sass' => FALSE,
					'sass_nocache'=> FALSE,
					'autorecompile'=> FALSE,
				)),
		));	
	}
} 

// ADD RECOMPILE TRIGGER LINK TO ADMIN BAR IN BACKEND
add_action('admin_bar_menu', 'ps_add_backend_toolbar_items', 100);
function ps_add_backend_toolbar_items($admin_bar) {

    // Check if user is an administrator
    if (!current_user_can("administrator")) return;

    // Allow only in the backend
    if (!is_admin()) return;

    //if (!is_child_theme()) return; // COMMENT TO TEST ON ORIG THEME FOR CONSISTENCY

    // Base query args for recompiling SASS
    $base_query_args = [
        'compile_sass' => '1',
        'sass_nocache' => '1'
    ];

    // MAIN MENU ELEMENT - Link to Recompile SASS on the homepage
    $admin_bar->add_node([
        'id'    => 'ps-recompile-sass-backend',
        'title' => '<span id="icon-picostrap-sass"></span>' . __('Recompile SASS', 'your-textdomain'),
        'href'  => add_query_arg($base_query_args, home_url()),
    ]);

    // CHILD MENU ITEM - Recompile Once
    $admin_bar->add_node([
        'id'     => 'ps-recompile-sass-once-backend',
        'parent' => 'ps-recompile-sass-backend',
        'title'  => __('Recompile Once', 'your-textdomain'),
        'href'   => add_query_arg($base_query_args, home_url()),
    ]);

    // CHILD MENU ITEM - Recompile Continuously
    $admin_bar->add_node([
        'id'     => 'ps-recompile-sass-automatic-backend',
        'parent' => 'ps-recompile-sass-backend',
        'title'  => __('Recompile Continuously', 'your-textdomain'),
        'href'   => add_query_arg(array_merge($base_query_args, ['autorecompile' => '1']), home_url()),
    ]);
}


/////// ICON IN TOOLBAR STYLING ///////////////////////////////////////////////////
add_action('admin_head', 'ps_print_launch_icon_styles'); // on backend area
add_action('wp_head', 'ps_print_launch_icon_styles'); // on frontend area
function ps_print_launch_icon_styles() {
	if (!current_user_can("administrator")) return;
?>
	<style> 
		#icon-picostrap-sass:after {
			display:inline-block; margin: 0 2px 0 0; vertical-align:middle; position: relative; content: ' ';  width: 24px;    height: 24px; background-size: contain; background-repeat: no-repeat; background-image: url('<?php echo get_template_directory_uri() ?>/inc/picosass/sass-logo.svg'); }	
	</style>
	<?php
}


