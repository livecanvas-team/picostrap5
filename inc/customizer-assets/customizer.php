<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// ADD CUSTOM JS & CSS TO CUSTOMIZER //////////////////////////////////////////////////////////////////////////////////////////////////////////
function picostrap_customize_enqueue() {
	wp_enqueue_script( 'custom-customize', get_template_directory_uri() . '/inc/customizer-assets/customizer.js', array( 'jquery', 'customize-controls' ), '2.63', true );
	wp_enqueue_script( 'custom-customize-lib', get_template_directory_uri() . '/inc/customizer-assets/customizer-vars.js', array( 'jquery', 'customize-controls' ), '2.61', true );
	wp_enqueue_style( 'custom-customize', get_template_directory_uri() . '/inc/customizer-assets/customizer.css'  );
	
	//fontpicker
	wp_enqueue_script( 'fontpicker', get_template_directory_uri() . '/inc/customizer-assets/fontpicker/jquery.fontpicker.min.js', array( 'jquery', 'customize-controls' ), '2.61', true );
	wp_enqueue_style( 'fontpicker', get_template_directory_uri() . '/inc/customizer-assets/fontpicker/jquery.fontpicker.min.css', array(), '2.61' );
}
add_action( 'customize_controls_enqueue_scripts', 'picostrap_customize_enqueue' );


//ADD BODY CLASSES  //////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter( 'body_class', 'picostrap_config_body_classes' );
function picostrap_config_body_classes( $classes ) {
	$classes[]="picostrap_header_navbar_position_".get_theme_mod('picostrap_header_navbar_position');
	return $classes;
}

//REMOVE BODY MARGIN-TOP GIVEN BY WORDPRESS ADMIN BAR //////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('get_header', 'picostrap_filter_head');
function picostrap_filter_head() {
	if (get_theme_mod('picostrap_header_navbar_position')=="fixed-top") remove_action('wp_head', '_admin_bar_bump_cb');
}



///MAIN SETTING: DECLARE ALL SCSS VARIABLES TO HANDLE IN THE CUSTOMIZER
if(!function_exists("picostrap_get_scss_variables_array")):
	function picostrap_get_scss_variables_array(){
		return array(
			"colors" => array( //  $variable_name => $variable_props
				'$body-bg' => array('type' => 'color'),
				'$body-color' => array('type' => 'color'),
				'$link-color' => array('type' => 'color'),
				//'$link-decoration' => array('type' => 'text'),
				'$link-hover-color' => array('type' => 'color'),
				//'$link-hover-decoration' => array('type' => 'text'),
				// STATUS COLORS
				'$primary'=> array('type' => 'color','newgroup' => 'Bootstrap Colors'),
				'$secondary' => array('type' => 'color'),
				'$success' => array('type' => 'color'),
				'$info' => array('type' => 'color'),
				'$warning' => array('type' => 'color'),
				'$danger' => array('type' => 'color'),
				'$light' => array('type' => 'color'),
				'$dark' => array('type' => 'color'),
				),
			

			//add another section
			"components" => array( // $variable_name => $variable_props
								
				'$enable-rounded' => array('type' => 'boolean', 'default' => 'true'),
				'$enable-shadows' => array('type' => 'boolean'),
				'$enable-gradients'=> array('type' => 'boolean'),
				
				'$spacer' => array('type' => 'text','placeholder' => '1rem'),
				
				'$border-width' => array('type' => 'text','placeholder' => '1px'),
				'$border-color' => array('type' => 'color' ),
				'$border-radius' => array('type' => 'text','placeholder' => '.25rem'),
				'$border-radius-lg' => array('type' => 'text','placeholder' => '.3rem'),
				'$border-radius-sm' => array('type' => 'text','placeholder' => '.2rem'),
				'$rounded-pill' => array('type' => 'text','placeholder' => '50rem'),
				

				),
			
			
			
			//add another section
			"typography" => array( // $variable_name => $variable_props
				
				
							
				'$font-family-base' => array('type' => 'text', 'placeholder' => '$font-family-sans-serif ', 'newgroup' => 'Font Families', ), 
				'$font-family-sans-serif' => array('type' => 'text', 'placeholder' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji" '),
				'$font-family-monospace' => array('type' => 'text', 'placeholder' => 'SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace '),
				
				'$font-size-base' => array('newgroup' => 'Font Sizes', 'type' => 'text', 'placeholder' => '1rem'),
				'$font-size-lg' => array('type' => 'text', 'placeholder' => '1.25rem'),
				'$font-size-sm' => array('type' => 'text', 'placeholder' => '.875rem '),

				'$enable-rfs' => array('type' => 'boolean','default' => 'true'),
				
				'$font-weight-lighter' => array('newgroup' => 'Font Weights', 'type' => 'text', 'placeholder' => 'lighter '),
				'$font-weight-light' => array('type' => 'text', 'placeholder' => '300'),
				'$font-weight-normal' => array('type' => 'text', 'placeholder' => '400'),
				'$font-weight-bold' => array('type' => 'text', 'placeholder' => '700'),
				'$font-weight-bolder' => array('type' => 'text', 'placeholder' => 'bolder'),
				
				'$font-weight-base' => array('type' => 'text', 'placeholder' => '400'),
				'$line-height-base' => array('type' => 'text', 'placeholder' => '1.5'),
			
				'$headings-font-family' => array('type' => 'text', 'placeholder' => 'null','newgroup' => 'Headings', ),
				'$headings-font-weight' => array('type' => 'text', 'placeholder' => '500 '),
				'$headings-line-height' => array('type' => 'text', 'placeholder' => '1.2'),
				'$headings-color' => array('type' => 'color'),
				
				'$headings-margin-bottom' => array('type' => 'text', 'placeholder' => '$spacer / 2 '),
				'$h1-font-size' => array('type' => 'text', 'placeholder' => '2.5rem'),
				'$h2-font-size' => array('type' => 'text', 'placeholder' => '2rem'),
				'$h3-font-size' => array('type' => 'text', 'placeholder' => '1.75rem'),
				'$h4-font-size' => array('type' => 'text', 'placeholder' => '1.5rem'),
				'$h5-font-size' => array('type' => 'text', 'placeholder' => '1.25rem'),
				'$h6-font-size' => array('type' => 'text', 'placeholder' => '1rem'),
				
				
				//'$display1-size' => array('newgroup' => 'Display Classes', 'type' => 'text', 'placeholder' => '6rem'),
				//'$display2-size' => array('type' => 'text', 'placeholder' => '5.5rem'),
				//'$display3-size' => array('type' => 'text', 'placeholder' => '4.5rem'),
				//'$display4-size' => array('type' => 'text', 'placeholder' => '3.5rem'),
				//
				//'$display1-weight' => array('type' => 'text', 'placeholder' => '300'),
				//'$display2-weight' => array('type' => 'text', 'placeholder' => '300'),
				//'$display3-weight' => array('type' => 'text', 'placeholder' => '300'),
				//'$display4-weight' => array('type' => 'text', 'placeholder' => '300'),
				//'$display-line-height' => array('type' => 'text', 'placeholder' => ' $headings-line-height '),
				
				'$lead-font-size' => array('newgroup' => 'Lead, Small and Muted', 'type' => 'text', 'placeholder' => '1.25rem'),
				'$lead-font-weight' => array('type' => 'text', 'placeholder' => '300'),
				
				'$small-font-size' => array('type' => 'text', 'placeholder' => '80%'),
				
				'$text-muted' => array('type' => 'color',  ),
				
				
				'$blockquote-small-font-size' => array('newgroup' => 'Blockquotes', 'type' => 'text', 'placeholder' => '$small-font-size '),
				'$blockquote-font-size' => array('type' => 'text', 'placeholder' => '1.25rem '),
				'$blockquote-footer-font-size' => array('type' => 'text', 'placeholder' => '$small-font-size'),
				'$blockquote-footer-color' => array('type' => 'color' ),
				
				
				'$hr-height' => array('newgroup' => 'HRs', 'type' => 'text', 'placeholder' => '$border-width'),
				'$hr-color' => array( 'type' => 'color'),
				
				'$mark-padding' => array('newgroup' => 'Miscellanea',  'type' => 'text', 'placeholder' => '.2em'),
				
				'$dt-font-weight' => array('type' => 'text', 'placeholder' => '700'),
				
				//'$kbd-box-shadow' => array('type' => 'text', 'placeholder' => 'inset 0 -.1rem 0 rgba($black, .25) '),
				'$nested-kbd-font-weight' => array('type' => 'text', 'placeholder' => '700'),
				
				'$list-inline-padding' => array('type' => 'text', 'placeholder' => '.5rem'),
				
				'$mark-bg' => array('type' => 'color', 'placeholder' => '#fcf8e3'),
				
				'$hr-margin-y' => array('type' => 'text', 'placeholder' => '$spacer'),
				
				
				'$paragraph-margin-bottom' => array('type' => 'text', 'placeholder' => '1rem'),
				
				),
			
			
			
			
			//add another section
			"buttons-forms" => array( // $variable_name => $variable_props
				
							
				'$input-btn-padding-y' => array('type' => 'text','placeholder' => '.375rem'),
				'$input-btn-padding-x' => array('type' => 'text','placeholder' => '.75rem'),
				'$input-btn-font-family' => array('type' => 'text','placeholder' => 'null'),
				'$input-btn-font-size' => array('type' => 'text','placeholder' => '$font-size-base'),
				'$input-btn-line-height' => array('type' => 'text','placeholder' => '$line-height-base'),
				
				'$input-btn-focus-width' => array('type' => 'text','placeholder' => '.2rem'),
				'$input-btn-focus-color' => array('type' => 'color','placeholder' => 'rgba($component-active-bg, .25)'),
				'$input-btn-focus-box-shadow' => array('type' => 'text','placeholder' => '0 0 0 $input-btn-focus-width $input-btn-focus-color'),
				
				'$input-btn-padding-y-sm' => array('type' => 'text','placeholder' => '.25rem'),
				'$input-btn-padding-x-sm' => array('type' => 'text','placeholder' => '.5rem'),
				'$input-btn-font-size-sm' => array('type' => 'text','placeholder' => '$font-size-sm'),
				'$input-btn-line-height-sm' => array('type' => 'text','placeholder' => '    $line-height-sm'),
				
				'$input-btn-padding-y-lg' => array('type' => 'text','placeholder' => '.5rem'),
				'$input-btn-padding-x-lg' => array('type' => 'text','placeholder' => '1rem'),
				'$input-btn-font-size-lg' => array('type' => 'text','placeholder' => '$font-size-lg'),
				'$input-btn-line-height-lg' => array('type' => 'text','placeholder' => '    $line-height-lg'),
				
				'$input-btn-border-width' => array('type' => 'text','placeholder' => '$border-width'),
				

				),
			
			
			//add another section
			"buttons" => array( // $variable_name => $variable_props
				
							
				'$btn-padding-y' => array('type' => 'text','placeholder' => '.375rem'),
				'$btn-padding-x' => array('type' => 'text','placeholder' => '.75rem'),
				'$btn-font-family' => array('type' => 'text','placeholder' => 'null'),
				'$btn-font-size' => array('type' => 'text','placeholder' => '$font-size-base'),
				'$btn-line-height' => array('type' => 'text','placeholder' => '$line-height-base'),
				'$btn-white-space' => array('type' => 'text','placeholder' => 'null (Set to `nowrap` to prevent text wrapping)'),

	
				'$btn-padding-y-sm' => array('type' => 'text','placeholder' => '.25rem'),
				'$btn-padding-x-sm' => array('type' => 'text','placeholder' => '.5rem'),
				'$btn-font-size-sm' => array('type' => 'text','placeholder' => '$font-size-sm'),
	
				'$btn-padding-y-lg' => array('type' => 'text','placeholder' => '.5rem'),
				'$btn-padding-x-lg' => array('type' => 'text','placeholder' => '1rem'),
				'$btn-font-size-lg' => array('type' => 'text','placeholder' => '$font-size-lg'),
	
				'$btn-border-width' => array('type' => 'text','placeholder' => '$border-width'),
				


				'$btn-font-weight' => array('type' => 'text','placeholder' => '             $font-weight-normal !default'),
				'$btn-box-shadow' => array('type' => 'text','placeholder' => '              inset 0 1px 0 rgba($white, .15), 0 1px 1px rgba($black, .075) !default'),
				'$btn-focus-width' => array('type' => 'text','placeholder' => '             $input-btn-focus-width !default'),
				'$btn-focus-box-shadow' => array('type' => 'text','placeholder' => '        $input-btn-focus-box-shadow !default'),
				'$btn-disabled-opacity' => array('type' => 'text','placeholder' => '        .65 !default'),
				'$btn-active-box-shadow' => array('type' => 'text','placeholder' => '       inset 0 3px 5px rgba($black, .125) !default'),

				'$btn-link-color' => array('type' => 'text','placeholder' => '              $link-color !default','newgroup' => 'Button Colors',),
				'$btn-link-hover-color' => array('type' => 'text','placeholder' => '        $link-hover-color !default'),
				'$btn-link-disabled-color' => array('type' => 'text','placeholder' => '     $gray-600 !default'),
				
				// Allows for customizing button radius independently from global border radius
				'$btn-border-radius' => array('type' => 'text','placeholder' => '           $border-radius !default','newgroup' => 'Buttons Border Radius',),
				'$btn-border-radius-sm' => array('type' => 'text','placeholder' => '        $border-radius-sm !default'),
				'$btn-border-radius-lg' => array('type' => 'text','placeholder' => '        $border-radius-lg !default'),
				
				'$btn-transition' => array( 'newgroup' => 'Buttons Extras', 'type' => 'text','placeholder' => '              color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !default'),
				
				'$btn-hover-bg-shade-amount' => array('type' => 'text','placeholder' => '       15% !default'),
				'$btn-hover-bg-tint-amount' => array('type' => 'text','placeholder' => '        15% !default'),
				'$btn-hover-border-shade-amount' => array('type' => 'text','placeholder' => '   20% !default'),
				'$btn-hover-border-tint-amount' => array('type' => 'text','placeholder' => '    10% !default'),
				'$btn-active-bg-shade-amount' => array('type' => 'text','placeholder' => '      20% !default'),
				'$btn-active-bg-tint-amount' => array('type' => 'text','placeholder' => '       20% !default'),
				'$btn-active-border-shade-amount' => array('type' => 'text','placeholder' => '  25% !default'),
				'$btn-active-border-tint-amount' => array('type' => 'text','placeholder' => '   10% !default'),




				),
			
			
			//add another section
			
			
			
			
		);	 
	}

endif;


//ENABLE SELECTIVE REFRESH 
add_theme_support( 'customize-selective-refresh-widgets' );

//ADD HELPER ICONS
function picostrap_register_main_partials( WP_Customize_Manager $wp_customize ) {
 
    // Abort if selective refresh is not available.
    if ( ! isset( $wp_customize->selective_refresh ) ) { return;}
 
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	//blogname
    $wp_customize->selective_refresh->add_partial( 'header_site_title', array(
        'selector' => 'a.navbar-brand',
        'settings' => array( 'blogname' ),
        'render_callback' => function() { return get_bloginfo( 'name', 'display' );  },
    ));
	
	//blog description
    $wp_customize->selective_refresh->add_partial( 'header_site_desc', array(
        'selector' => '#top-description',
        'settings' => array( 'blogdescription' ),
        'render_callback' => function() { return get_bloginfo( 'description', 'display' ); },
    ));
	
	//hide tagline
	$wp_customize->selective_refresh->add_partial( 'header_disable_tagline', array(
        'selector' => '#top-description',
        'settings' => array( 'header_disable_tagline' ),
        'render_callback' => function() {if (!get_theme_mod('header_disable_tagline')) return get_bloginfo( 'description', 'display' ); else return "";},
    ));
	
	//MENUS
	$wp_customize->selective_refresh->add_partial( 'header_menu_left', array(
        'selector' => '#navbar .menuwrap-left',
        'settings' => array( 'nav_menu_locations[navbar-left]' ),
          
    ) );
	
	/*
	$wp_customize->selective_refresh->add_partial( 'header_menu_right', array(
        'selector' => '#navbar .menuwrap-right',
        'settings' => array( 'nav_menu_locations[navbar-right]' ),     
    ));
	*/
	//topbar content
	$wp_customize->selective_refresh->add_partial( 'topbar_html_content', array(
        'selector' => '#topbar-content',
        'settings' => array( 'topbar_content' ),
		'render_callback' => function() {
             return get_theme_mod('topbar_content'); 
        },     
    )); 
	//footer text
	$wp_customize->selective_refresh->add_partial( 'footer_ending_text', array(
        'selector' => 'footer.site-footer',
        'settings' => array( 'picostrap_footer_text' ),
		'render_callback' => function() {
             return picostrap_site_info();
        },     
    ));
	/*
	//inline css
	$wp_customize->selective_refresh->add_partial( 'picostrap_inline_css', array(
        'selector' => '#picostrap-inline-style',
        'settings' => array( 'picostrap_footer_bgcolor','picostrap_menubar_bgcolor' , 'picostrap_links_color','picostrap_hover_links_color','picostrap_headings_font','picostrap_body_font'  ),
		'render_callback' => function() {
             return picostrap_footer_add_inline_css();
        },
    ));
	*/
	


	
	//SINGLE: categories
	$wp_customize->selective_refresh->add_partial( 'singlepost_entry_footer', array(
        'selector' => '.entry-categories',
        'settings' => array( 'singlepost_disable_entry_cats' ),
		'render_callback' => '__return_false'    
	));
	
	//SINGLE: meta: date and author
	$wp_customize->selective_refresh->add_partial( 'singlepost_entry_meta', array(
		'selector' => '#single-post-meta',
		'settings' => array( 'singlepost_disable_entry_meta' ),
		'render_callback' => '__return_false'    
	));


	/*
	//SINGLE: postnavi
	$wp_customize->selective_refresh->add_partial( 'singlepost_posts_nav', array(
        'selector' => 'nav.post-navigation',
        'settings' => array( 'singlepost_disable_posts_nav' ),
		'render_callback' => '__return_false' 
    ));
	
	//SINGLE: comments
	$wp_customize->selective_refresh->add_partial( 'singlepost_comments', array(
        'selector' => '#comments',
        'settings' => array( 'singlepost_disable_comments' ),
		'render_callback' => '__return_false'    
	)); */

	//SINGLE: sharing buttons
	$wp_customize->selective_refresh->add_partial( 'enable_sharing_buttons', array(
        'selector' => '.picostrap-sharing-buttons',
        'settings' => array( 'enable_sharing_buttons' ),
		'render_callback' => '__return_false'    
	));

	
	//GLOBAL: enable_detect_page_scroll
	$wp_customize->selective_refresh->add_partial( 'enable_detect_page_scroll', array(
		'selector' => 'body',
		'settings' => array( 'enable_detect_page_scroll' ),
		'render_callback' => '__return_false'    
	));
	

     
}
add_action( 'customize_register', 'picostrap_register_main_partials' );

 
//CUSTOM BACKGROUND
//$defaults_bg = array(
//	'default-color'          => '',	'default-image'          => '',	'default-repeat'         => '',	'default-position-x'     => '',	'default-attachment'     => '',
//	'wp-head-callback'       => '_custom_background_cb',	'admin-head-callback'    => '',	'admin-preview-callback' => '');
//add_theme_support( 'custom-background' );


//CUSTOM BACKGROUND SIZING OPTIONS

function custom_background_size( $wp_customize ) {
 
	// Add your setting.
	$wp_customize->add_setting( 'background-image-size', array(
		'default' => 'cover',
	) );

	// Add your control box.
	$wp_customize->add_control( 'background-image-size', array(
		'label'      => __( 'Background Image Size',"picostrap" ),
		'section'    => 'background_image', 
		'priority'   => 200,
		'type' => 'radio',
		'choices' => array(
			'cover' => __( 'Cover',"picostrap" ),
			'contain' => __( 'Contain' ,"picostrap"),
			'inherit' => __( 'Inherit' ,"picostrap"),
		)
	) );
}

add_action( 'customize_register', 'custom_background_size' );

function custom_background_size_css() {
	if ( ! get_theme_mod( 'background_image' ) )  return;
	$background_size = get_theme_mod( 'background-image-size', 'inherit' );
	echo '<style> body.custom-background { background-size: '.$background_size.'; } </style>';
}

add_action( 'wp_head', 'custom_background_size_css', 999 );


//END CUSTOM BACKGROUND SIZING OPTIONS


	
////////DECLARE ALL THE WIDGETS WE NEED	FOR THE SCSS OPTIONS////////////////////////////////////////////////

add_action("customize_register","picostrap_theme_customize_register_extras");
	
function picostrap_theme_customize_register_extras($wp_customize) {
	
	///ADDITIONAL SECTIONS:
	//COLORS is already default
	
	 
	$wp_customize->add_section("typography", array(
        "title" => __("Typography", "picostrap"),
        "priority" => 50,
    ));
	
 
	$wp_customize->add_section("components", array(
        "title" => __("Components", "picostrap"),
        "priority" => 50,
    ));
	
	 
	$wp_customize->add_section("buttons-forms", array(
        "title" => __("Buttons + Forms", "picostrap"),
        "priority" => 50,
    ));

	$wp_customize->add_section("buttons", array(
        "title" => __("Buttons", "picostrap"),
        "priority" => 50,
    ));
	
	
	
	
	//istantiate  all controls needed for controlling the SCSS variables
	foreach(picostrap_get_scss_variables_array() as $section_slug => $section_data):
	
		foreach($section_data as $variable_name => $variable_props):
			 
			$variable_slug=str_replace("$","SCSSvar_",$variable_name);
			$variable_pretty_format_name=ucwords(str_replace("-",' ',str_replace("$","",$variable_name)));		
			$variable_type=$variable_props['type'];
			if (array_key_exists('default',$variable_props)) $default=$variable_props['default']; else $default="";
			
			
			if($variable_type=="color"):
			
				$wp_customize->add_setting(  $variable_slug,  array(
					'default' => $default,
					'sanitize_callback' => 'sanitize_hex_color',
					"transport" => "postMessage",
					));
				$wp_customize->add_control(
					new WP_Customize_Color_Control(
					$wp_customize,
					$variable_slug, //give it an ID
					array(
						'label' => __( $variable_pretty_format_name, 'picostrap' ), //set the label to appear in the Customizer
						'description' =>  "(".$variable_name.")",
						'section' => $section_slug, //select the section for it to appear under  
						)
					));	
			endif;
			
			if($variable_type=="boolean"):
 
				$wp_customize->add_setting($variable_slug, array(
					"default" => $default,
					"transport" => "postMessage",
				));
				$wp_customize->add_control(new WP_Customize_Control(
					$wp_customize,
					$variable_slug,
					array(
						'label' => __( $variable_pretty_format_name, 'picostrap' ), //set the label to appear in the Customizer
						'description' =>  "(".$variable_name.")",
						'section' => $section_slug, //select the section for it to appear under
						'type' => 'checkbox'
						)
				));
			endif;
			
			if($variable_type=="text"):
			
				if(array_key_exists('placeholder',$variable_props)) $placeholder_html="<b>Default:</b> ".$variable_props['placeholder']; else $placeholder_html="";
				if (array_key_exists('newgroup',$variable_props)) $optional_grouptitle=" <span hidden class='cs-option-group-title'>".$variable_props['newgroup']."</span>"; else $optional_grouptitle="";
			
				$wp_customize->add_setting($variable_slug, array(
					"default" => $default,
					"transport" => "postMessage",
					//"default" => "1rem",
					//'sanitize_callback' => 'picostrap_sanitize_rem'
				));
				$wp_customize->add_control(new WP_Customize_Control(
					$wp_customize,
					$variable_slug,
					array(
						'label' => __( $variable_pretty_format_name, 'picostrap' ), //set the label to appear in the Customizer
						'description' => $optional_grouptitle. " <!-- (".$variable_name.") -->".$placeholder_html, //ADD COMMENT HERE IF NECESSARY
						'section' => $section_slug, //select the section for it to appear under
						'type' => 'text', 
						)
				));
			endif;
			
		endforeach;
	endforeach;

	//SANITIZE CHECKBOX
	function picostrap_sanitize_checkbox( $input ) {		return ( ( isset( $input ) && true == $input ) ? true : false ); }

	//COLORS: ANDROID CHROME HEADER COLOR
	$wp_customize->add_setting(  'picostrap_header_chrome_color',  array(
		'default' => '', // Give it a default
		'transport" => "postMessage',
		));
		$wp_customize->add_control(
		new WP_Customize_Color_Control(
		$wp_customize,
		'picostrap_header_chrome_color', //give it an ID
		array(
		'label' => __( 'Header Color in Android Chrome', 'picostrap' ), //set the label to appear in the Customizer
		'section' => 'colors', //select the section for it to appear under 
		'description' =>" <span hidden class='cs-option-group-title'>Extra</span>" //to implement a divisor
		)
	));
 
    //TAGLINE: SHOW / HIDE SWITCH
	$wp_customize->add_setting('header_disable_tagline', array(
        'default' => '',
        'transport' => 'postMessage',
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        'header_disable_tagline',
        array(
            'label' => __('Hide Tagline', 'picostrap'),
            'section' => 'title_tagline',  
            'type'     => 'checkbox',
			)
    ));
	
    //   NAVBAR SECTION //////////////////////////////////////////////////////////////////////////////////////////////////////////
	$wp_customize->add_section("nav", array(
        "title" => __("Main Navigation Bar", "picostrap"),
        "priority" => 60,
    ));
	
	// HEADER NAVBAR EXPAND ON BREAKPOINT
	$wp_customize->add_setting("picostrap_header_navbar_expand", array(
        "default" => "navbar-expand-md",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_header_navbar_expand",
        array(
            'label' => __('Navbar Expansion', 'picostrap'),
            'section' => 'nav',
            'type'     => 'radio',
			'description' => __('Navbar is Collapsed on mobile, and expands to a full blown menubar on chosen breakpoint', 'picostrap'),
			'choices'  => array(
				'navbar-expand-none'  => 'Never expand, keep always collapsed', 
				'navbar-expand-sm'  => 'Expand on SM and upper',
				'navbar-expand-md'  => 'Expand on MD and upper',
				'navbar-expand-lg'  => 'Expand on LG and upper',
				'navbar-expand-xl'  => 'Expand on XL and upper',
				'navbar-expand-xxl'  => 'Expand on XXL and upper',
				)
        )
    ));

	// HEADER NAVBAR POSITION
	$wp_customize->add_setting("picostrap_header_navbar_position", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_header_navbar_position",
        array(
            'label' => __('Navbar Position', 'picostrap'),
            'section' => 'nav',
            'type'     => 'radio',
			'choices'  => array(
				''  => 'Standard Static Top',
				'fixed-top' => 'Fixed on Top',
				'fixed-bottom'  => 'Fixed on Bottom',
				'd-none'  => 'No Navbar', 
				)
        )
    ));

	
	//DETECT PAGE SCROLL
	$wp_customize->add_setting("enable_detect_page_scroll", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "enable_detect_page_scroll",
        array(
            "label" => __("Enable Page Scrolling Detection", "picostrap"),
			"description" => __("Publish and exit the Customizer to see the effect. Adds a scroll-position-at-top / scroll-position-not-at-top class to the BODY element according to scroll position. Customize via CSS. Use with Navbar Position set to Fixed for best results. <!--  <a target='_blank' href='#'>Tutorial Coming Soon</a> --> ", "picostrap"),
            "section" => "nav", 
            'type'     => 'checkbox',
			)
	));


	//HEADERNAVBAR COLOR CHOICE
	$wp_customize->add_setting("picostrap_header_navbar_color_choice", array(
        'default' => 'bg-dark',
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_header_navbar_color_choice",
        array(
            'label' => __('Navbar Background Color', 'picostrap'),
            'section' => 'nav',
            'type'     => 'radio',
			'choices'  => array(
				'bg-primary'	=> 'Primary',	
				'bg-secondary'	=> 'Secondary',	
				'bg-success' 	=> 'Success', 	
				'bg-info' 		=> 'Info', 		
				'bg-warning' 	=> 'Warning', 	
				'bg-danger' 	=> 'Danger', 	
				'bg-light' 	=> 'Light', 	
				'bg-dark' 		=> 'Dark', 		
				'bg-transparent' 		=> 'Transparent' 
				
				
				)
        )
    ));
	
	//HEADERNAVBAR COLOR SCHEME
	$wp_customize->add_setting("picostrap_header_navbar_color_scheme", array(
        'default' => 'navbar-dark',
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_header_navbar_color_scheme",
        array(
            'label' => __('Color Scheme (Menubar links)', 'picostrap'),
            'section' => 'nav',
			'type'     => 'radio',
			'choices'  => array(
				''  => 'Default',
				'navbar-light' => 'Light (Dark links)',
				'navbar-dark' => 'Dark (Light links)', 
				)
        )
    ));
	
	

	



	//SEARCH FORM
	$wp_customize->add_setting("enable_search_form", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "enable_search_form",
        array(
            "label" => __("Enable Search Form", "picostrap"),
            "section" => "nav", 
            'type'     => 'checkbox',
			)
	));





	//  TOPBAR SECTION //////////////////////////////////////////////////////////////////////////////////////////////////////////
	$wp_customize->add_section("topbar", array(
        "title" => __("Optional Topbar", "picostrap"),
        "priority" => 60,
    ));
	
	//ENABLE TOPBAR
	$wp_customize->add_setting("enable_topbar", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "enable_topbar",
        array(
            "label" => __("Enable Topbar", "picostrap"),
			"description" => __("Adds before all, at body start", "picostrap"),
            "section" => "topbar", 
            'type'     => 'checkbox',
			)
    ));
	
	//TOPBAR TEXT
	$wp_customize->add_setting("topbar_content", array(
        "default" => "",
        "transport" => "postMessage",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "topbar_content",
        array(
            "label" => __("Topbar Text / HTML", "picostrap"),
            "section" => "topbar",
            'type'     => 'textarea',
        )
    ));
	
	//TOPBAR BG COLOR CHOICE
	$wp_customize->add_setting("topbar_bg_color_choice", array(
        'default' => 'bg-light',
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "topbar_bg_color_choice",
        array(
            'label' => __('Topbar Background Color', 'picostrap'),
            'section' => 'topbar',
            'type'     => 'radio',
			'choices'  => array(
				'bg-primary'	=> 'Primary',	
				'bg-secondary'	=> 'Secondary',	
				'bg-success' 	=> 'Success', 	
				'bg-info' 		=> 'Info', 		
				'bg-warning' 	=> 'Warning', 	
				'bg-danger' 	=> 'Danger', 	
				'bg-light' 	=> 'Light', 	
				'bg-dark' 		=> 'Dark', 		
				'bg-transparent' 		=> 'Transparent'
				)
        )
    ));
	
	//TOPBAR TEXT COLOR CHOICE
	$wp_customize->add_setting("topbar_text_color_choice", array(
        'default' => 'text-dark',
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "topbar_text_color_choice",
        array(
            'label' => __('Topbar Text Color', 'picostrap'),
            'section' => 'topbar',
            'type'     => 'radio',
			'choices'  => array(
				'text-primary'	=> 'Primary',	
				'text-secondary'	=> 'Secondary',	
				'text-success' 	=> 'Success', 	
				'text-info' 		=> 'Info', 		
				'text-warning' 	=> 'Warning', 	
				'text-danger' 	=> 'Danger', 	
				'text-light' 	=> 'Light', 	
				'text-dark' 		=> 'Dark', 		
				)
        )
    ));
	
	
	//ADD SECTION FOR FOOTER  //////////////////////////////////////////////////////////////////////////////////////////////////////////
	$wp_customize->add_section("footer", array(
        "title" => __("Footer", "picostrap"),
        "priority" => 100,
    ));
	
	//FOOTER TEXT
	$wp_customize->add_setting("picostrap_footer_text", array(
        "default" => "",
        "transport" => "postMessage",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_footer_text",
        array(
			"label" => __("Footer Text", "picostrap"),
			"description"  => "THIS SIMPLE FIELD can contain HTML and is displayed into the 'colophon', the very bottom of the site. <br><br>TO BUILD A MORE COMPLEX FOOTER, USE THE WIDGETED AREA. <br>To enable it, populate it from the backend's <a target='_blank' href='".admin_url('widgets.php')."'>Widgets page</a>",
            "section" => "footer",
            'type'     => 'textarea',
			 
        )
    ));
	
		
	// ADD A SECTION FOR HEADER & FOOTER CODE -- to fix
	$wp_customize->add_section("addcode", array(
        "title" => __("Header / Footer Code", "picostrap"),
        "priority" => 180,
    ));
	
	//ADD HEADER CODE  
	$wp_customize->add_setting("picostrap_header_code", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_header_code",
        array(
            "label" => __("Add code to Header", "picostrap"),
            "section" => "addcode",
            'type'     => 'textarea',
			'description' =>'Placed inside the HEAD of the page'
			)
    ));
	
	//ADD FOOTER CODE 
	$wp_customize->add_setting("picostrap_footer_code", array(
        "default" => "",
        "transport" => "refresh",
    ));


	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_footer_code",
        array(
            "label" => __("Add code to Footer", "picostrap"),
            "section" => "addcode",
            'type'     => 'textarea',
			'description' =>'Placed before closing the BODY of the page'
			)
    ));

	//ADD FONTLOADING HEADER CODE  
	$wp_customize->add_setting("picostrap_fonts_header_code", array(
        "default" => "",
		"transport" => "refresh",
        //"transport" => "postMessage", // and no custom js is added: so no live page update is done, how it should be - but causes unstable behavoiur
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_fonts_header_code",
        array(
            "label" => __("Font Loading Header code", "picostrap"),
            "section" => "addcode",
            'type'     => 'textarea',
			'description' =>'<b>Not editable</b> - Reading purpose only. Automatically generated upon publishing'
			)
    ));
	
	//DISABLE FONTLOADING HEADER CODE  
	$wp_customize->add_setting("picostrap_fonts_header_code_disable", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_fonts_header_code_disable",
        array(
            "label" => __("Disable the Font Loading in Header", "picostrap"),
			"description" =>  __("<b>Keep this unchecked, unless you really want. </b>").__("Disables serving the code in the textarea above. Can be relevant if you want to self-host Google Fonts. Refer to this <a target='_blank' href='https://google-webfonts-helper.herokuapp.com/fonts/abeezee?subsets=latin'>tool</a> to get started. ", "picostrap"),
            "section" => "addcode", 
            'type'     => 'checkbox',
			)
    ));
	
	
	// ADD A SECTION FOR EXTRAS /////////////////////////////////////////////////////////////////////////////
	$wp_customize->add_section("extras", array(
        "title" => __("Global Options & Utilities", "picostrap"),
        "priority" => 190,
    ));
	/*
	//USE BOOTSTRAP NATIVE
	$wp_customize->add_setting("bootstrap_native", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "bootstrap_native",
        array(
            "label" => __("Use Bootstrap Native JS", "picostrap"),
			"description" => __("Will completely disable the jQuery-based BootStrap JS, and enqueue a similar version written in Vanilla (plain) JS. Publish and exit the Customizer to see the effect", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
    ));
	*/
	
	//DISABLE LIVERELOAD
	$wp_customize->add_setting("picostrap_disable_livereload", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_disable_livereload",
        array(
            "label" => __("Disable  SCSS  LiveReload  ", "picostrap"),
			"description" => __("Will completely disable the entire livereload feature. If you're not editing the SCSS files, you can do so. Makes a difference for site admins only.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
	));
	

	//DISABLE COMMENTS
	$wp_customize->add_setting("singlepost_disable_comments", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "singlepost_disable_comments",
        array(
            "label" => __("Disable the WordPress comments system", "picostrap"),
			"description" => __("Will completely disable the entire WP comments feature.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
    ));


	/*
	//DISABLE FONTAWESOME
	$wp_customize->add_setting("picostrap_fontawesome_disable", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_fontawesome_disable",
        array(
            "label" => __("Disable FontAwesome", "picostrap"),
			"description" => __("<b>Keep this unchecked, unless you really know what you're doing.</b>").__("This will prevent the compiler to pick the FontAwesome icon font from the UnderStrap folder and add it to the CSS bundle.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
    ));
	*/

	//BACK TO TOP
	$wp_customize->add_setting("enable_back_to_top", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "enable_back_to_top",
        array(
            "label" => __("Add a 'Back to Top' button to site", "picostrap"),
			"description" => __("Very light implementation. To see the button, you will also need to Publish, exit the Customizer, and scroll down a long page", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
    ));
	
	
	
	
	//LIGHTBOX
	$wp_customize->add_setting("enable_lightbox", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "enable_lightbox",
        array(
            "label" => __("Enable Lightbox", "picostrap"),
			"description" => __("Will lazily add a JS and a CSS file from cdn.jsdelivr.net before closing the BODY of the page, to use   <a target='_blank' href='https://github.com/biati-digital/glightbox'>gLightBox</a>: a very lightweight lightbox implementation. <br><br>The lightbox will be enabled on all images matching the selector: main#theme-main a:not(.nolightbox) img<br>To prevent the lightbox on an image, add the <b>nolightbox</b> class to it.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
	));
	

	// SINGLE POST & ARCHIVES SECTION //////////////////////////////////////////////////////////////////////////////////////////////////////////
	$wp_customize->add_section("singleposts", array(
        "title" => __("Single Post & Archives", "picostrap"),
        "priority" => 160,
    ));
		
	//ENTRY META: CATEGORIES  
	$wp_customize->add_setting("singlepost_disable_entry_cats", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "singlepost_disable_entry_cats",
        array(
            "label" => __("Hide Categories", "picostrap"),
			//"description" => __("Publish and exit the Customizer to see the effect", "picostrap"),
            "section" => "singleposts", 
            'type'     => 'checkbox',
			)
	));

	//ENTRY META: AUTHOR  & DATE  
	$wp_customize->add_setting("singlepost_disable_entry_meta", array(
		"default" => "",
		"transport" => "refresh",
	));
	$wp_customize->add_control(new WP_Customize_Control(
		$wp_customize,
		"singlepost_disable_entry_meta",
		array(
			"label" => __("Hide Post Date and Author", "picostrap"),
			//"description" => __("Publish and exit the Customizer to see the effect", "picostrap"),
			"section" => "singleposts", 
			'type'     => 'checkbox',
			)
	));



	/*
	//PAGES NAVIGATION: NEXT / PREV ARTICLE
	$wp_customize->add_setting("singlepost_disable_posts_nav", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "singlepost_disable_posts_nav",
        array(
            "label" => __("Hide Next and Prev Post Links (Single Post Template)", "picostrap"),
			"description" => __("Publish and exit the Customizer to see the effect", "picostrap"),
            "section" => "singleposts", 
            'type'     => 'checkbox',
			)
    ));
	*/
 	//SHARING BUTTONS
	$wp_customize->add_setting("enable_sharing_buttons", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "enable_sharing_buttons",
        array(
            "label" => __("Enable Sharing Buttons after the Content", "picostrap"),
			"description" => __("Pure HTML only, SVG inline icons, zero bloat", "picostrap"),
            "section" => "singleposts", 
            'type'     => 'checkbox',
			)
    ));
	//end single posts ////////////////////////////////////

	/*  .php
	// ADD A SECTION FOR ARCHIVES ///////////////////////////////
	$wp_customize->add_section("archives", array(
        "title" => __("Archive Templates", "picostrap"),
        "priority" => 160,
    ));
	
	//FIELDS
	
	//ARCHIVES_TEMPLATE
	$wp_customize->add_setting("archives_template", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "archives_template",
        array(
            "label" => __("Template", "picostrap"),
            "section" => "archives",
            "settings" => "archives_template",
            'type'     => 'select',
			'choices'  => array(
				''  => 'Standard Blog: List With Sidebar',
				'v2' => 'v2 : Horizontal split with Featured Image',
				'v3' => 'v3 : Simple 3 Columns Grid ',
				'v4' => 'v4 : Masonry Grid',
				 				)
			)
    ));
	
	*/
	
}
 
