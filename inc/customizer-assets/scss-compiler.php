<?php
/*
SCSS Compiler interface 
*/

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//CHECK URL PARAMETERS AND REACT ACCORDINGLY
add_action("admin_init", function (){
	if (!current_user_can("administrator")) return; //ADMINS ONLY
	if (isset($_GET['ps_show_mods'])){ print_r(get_theme_mods()); wp_die();	}
});

// USE LEAFO's SCSSPHP LIBRARY
use ScssPhp\ScssPhp\Compiler; //https://scssphp.github.io/scssphp/docs/
use ScssPhp\ScssPhp\ValueConverter;


/////FUNCTION TO GET ACTIVE SCSS CODE FROM FILE ///////
function picostrap_get_active_scss_code(){
	
	//INIT WP FILESYSTEM 
	global $wp_filesystem;
	if (empty($wp_filesystem)) {
		require_once (ABSPATH . '/wp-admin/includes/file.php');
		WP_Filesystem();
	}
	
	//READ THE FILE
	$the_scss_code = $wp_filesystem->get_contents( get_stylesheet_directory().'/sass/main.scss');  

	//FOR STYLE PACKAGES
	if(function_exists("picostrap_alter_scss")) $the_scss_code = picostrap_alter_scss ($the_scss_code);	 
	
	//EXAMPLE FOR OPTIONAL SASS EXTRAS
	//if (get_theme_mod("picostrap_additional_color_shades") )  $the_scss_code.=' @import "optional-extras/theme_color_shades"; ';

	return $the_scss_code;
}


 
/////FUNCTION TO RECOMPILE THE CSS ///////
function picostrap_generate_css(){
	
	//SET TIMESTAMP
	set_theme_mod("picostrap_scss_last_filesmod_timestamp_v2", picostrap_get_scss_last_filesmod_timestamp());
		
	//INITIALIZE COMPILER
	require_once "scssphp/scss.inc.php";
	$scss = new Compiler();
	
	try {
		//SET IMPORT PATH: CURRENTLY ACTIVE THEME's SASS FOLDER
		$scss->setImportPaths(get_stylesheet_directory().'/sass/');

		//IF USING A CHILD THEME, add parent theme sass folder: picostrap
		if (is_child_theme()) $scss->addImportPath(get_template_directory().'/sass/');
		
		//add extra path for style packages
		if(function_exists("picostrap_add_scss_import_path")) $scss->addImportPath(picostrap_add_scss_import_path());
		
		//SET OUTPUT FORMATTING 
		$scss->setOutputStyle(ScssPhp\ScssPhp\OutputStyle::COMPRESSED);
		
		// ENABLE SOURCE MAP // ADD OPTION
		
		//SET SCSS VARIABLES 
		$scss->replaceVariables(picostrap_get_active_scss_variables_array());

		//NOW COMPILE
		$compiled_css = $scss->compileString(picostrap_get_active_scss_code())->getCss();
	
	} catch (Exception $e) {
		//COMPILER ERROR: TYPICALLY INVALID SCSS CODE
		echo "<compiler-error>";
		echo  "<h1>SCSS Compile Error</h1>". $e->getMessage();
		echo "</compiler-error>";
		return FALSE;
   	}
	
	//CHECK CSS IS REALLY THERE
	if ($compiled_css=="") {
		//COMPILER ERROR: NO OUTPUT
		echo "<compiler-error>";
		echo  "<h1>SCSS Compile Error</h1>";
		echo "<p>Compiled CSS is empty, aborting.</p>";
		echo "</compiler-error>";
		return FALSE;
   	}
	
	//ADD SOME COMMENT
	$compiled_css .= " /* DO NOT ADD YOUR CSS HERE. ADD IT TO SASS/_CUSTOM.SCSS */ ";

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
		echo "<compiler-success>";
		echo "<h1>New CSS bundle successfully generated</h1>";
		echo "<a href='".picostrap_get_css_url()."' target='new'>View File</a>";
		echo "<br><br><b>Size: </b><br>".round(mb_strlen($compiled_css, '8bit')/1000)." kB - ".round(mb_strlen(gzcompress($compiled_css), '8bit')/1000)." kB gzipped";
		echo "</compiler-success>";
		return TRUE;

	} else {
		//GIVE NEGATIVE FEEDBACK
		echo "<compiler-error>";
		echo  "<h1>Error writing CSS file</h1>";
		echo "</compiler-error>";
		return FALSE;
	}
  
} ///end function



/////FUNCTION TO GET VARIABLES USED IN CUSTOMIZER /////
function picostrap_get_active_scss_variables_array(){

	$output_array=array();
	if (get_theme_mods()) foreach(get_theme_mods() as $theme_mod_name => $theme_mod_value):
		
		//check we are treating a scss variable, or skip
		if(substr($theme_mod_name,0,8) != "SCSSvar_") continue;
		
		//handle empty boolean values as false in flags
		if( strpos($theme_mod_name, 'enable-') !== false  && $theme_mod_value=="" ) $theme_mod_value="false"; 

		//skip empty values to prevent compiler error
		if($theme_mod_value=="" ) continue;
		
		//rename variable name to suit syntax
		$variable_name=str_replace("SCSSvar_","$",$theme_mod_name);
		
		//add to output array
		$output_array[$variable_name] = ValueConverter::parseValue($theme_mod_value);
		
	endforeach;
	
	return $output_array; 
}


// FORCE CSS REBUILD UPON ENABLING CHILD THEME 
add_action( 'after_switch_theme', 'picostrap_force_css_rebuilding', 10, 2 ); 
function picostrap_force_css_rebuilding() {   
    remove_theme_mod("picostrap_scss_last_filesmod_timestamp_v2");
}
