<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// ADD CUSTOM JS & CSS TO CUSTOMIZER //////////////////////////////////////////////////////////////////////////////////////////////////////////
function picostrap_customize_enqueue() {
	wp_enqueue_script( 'custom-customize', get_template_directory_uri() . '/inc/customizer-assets/customizer.js', array( 'jquery', 'customize-controls' ), rand(0,1000), true );
	 
	wp_localize_script(
		'custom-customize',
		'picostrap_ajax_obj',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'picostrap_livereload' ),
		)
	);
	
	
	
	wp_enqueue_script( 'custom-customize-lib', get_template_directory_uri() . '/inc/customizer-assets/customizer-vars.js', array( 'jquery', 'customize-controls' ), rand(0,1000), true );
	wp_enqueue_style( 'custom-customize', get_template_directory_uri() . '/inc/customizer-assets/customizer.css', array(), rand(0,1000)   );
	
	//fontpicker
	wp_enqueue_script( 'fontpicker', get_template_directory_uri() . '/inc/customizer-assets/fontpicker/jquery.fontpicker.min.js', array( 'jquery', 'customize-controls' ), rand(0,1000), true );
	wp_enqueue_style( 'fontpicker', get_template_directory_uri() . '/inc/customizer-assets/fontpicker/jquery.fontpicker.min.css', array(), rand(0,1000) );
}
add_action( 'customize_controls_enqueue_scripts', 'picostrap_customize_enqueue' );

//one more file for live preview
add_action( 'customize_preview_init', function(){
	wp_enqueue_script( 
		  'picostrap-themecustomizer',			//Give the script an ID
		  get_template_directory_uri().'/inc/customizer-assets/customizer-live-preview.js',//Point to file
		  array( 'jquery','customize-preview' ),	//Define dependencies
		  '',						//Define a version (optional) 
		  true						//Put script in footer?
	);
});


//ADD BODY CLASSES  //////////////////////////////////////////////////////////////////////////////////////////////////////////
add_filter( 'body_class', 'picostrap_config_body_classes' );
function picostrap_config_body_classes( $classes ) {

	//if we are using LC's custom header, don't add anything
	if (function_exists('lc_custom_header')) return $classes; 
	
	$classes[]="picostrap_header_navbar_position_".get_theme_mod('picostrap_header_navbar_position');
	$classes[]="picostrap_header_navbar_color_choice_".get_theme_mod('picostrap_header_navbar_color_choice');
	
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
		$live_preview_message = '
		<span class="lpa">
			<svg viewBox="0 0 24 24"> <path fill="currentColor" d="M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,4.5C17,4.5 21.27,7.61 23,12C21.27,16.39 17,19.5 12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5M3.18,12C4.83,15.36 8.24,17.5 12,17.5C15.76,17.5 19.17,15.36 20.82,12C19.17,8.64 15.76,6.5 12,6.5C8.24,6.5 4.83,8.64 3.18,12Z" /></svg>
			Live Preview
		</span>';
		return array(
			"colors" => array( //  $variable_name => $variable_props
				'$body-bg' => array('type' => 'color', 'comment' => $live_preview_message),
				'$body-color' => array('type' => 'color', 'comment' => $live_preview_message),
				'$link-color' => array('type' => 'color', 'comment' => $live_preview_message),
				//'$link-decoration' => array('type' => 'text'),
				'$link-hover-color' => array('type' => 'color', 'comment' => $live_preview_message),
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
				
				//ADDITIONAL COLOR CLASSES
				'$enable-text-shades'=> array('type' => 'boolean', 'default' => 'true', 'newgroup' => 'Color Shades', 'comment' => 'Generates text shade classes: from <b>.text-primary-100</b> to <b>.text-primary-900</b>'),
				'$enable-bg-shades' => array('type' => 'boolean', 'default' => 'true', 'comment' => 'Generates background shade classes: from <b>.bg-primary-100</b> to <b>.bg-primary-900</b>'),
				'$enable-text-bg-shades' => array('type' => 'boolean', 'comment' => 'Generates text & background combination shade classes: from <b>.text-bg-primary-100</b> to <b>.text-bg-primary-900</b>'),
				),	
			//add another section
			"components" => array( // $variable_name => $variable_props
								
				'$enable-rounded' => array('type' => 'boolean', 'default' => 'true'),
				'$enable-shadows' => array('type' => 'boolean'),
				'$enable-gradients'=> array('type' => 'boolean'),
				
				'$spacer' => array('type' => 'text','placeholder' => '1rem'),
				
				'$border-width' => array( 'newgroup' => 'Global Borders','type' => 'text','placeholder' => '1px', 'comment' => $live_preview_message),
				'$border-style' => array('type' => 'text','placeholder' => 'solid', 'comment' => $live_preview_message),
				'$border-color' => array('type' => 'color', 'comment' => $live_preview_message ),
				'$border-radius' => array('type' => 'text','placeholder' => '.375rem'),
				
				'$border-radius-sm' => array('newgroup' => 'Rounded Helper Classes', 'type' => 'text','placeholder' => '.25rem', 'comment' => $live_preview_message),
				'$border-radius-lg' => array('type' => 'text','placeholder' => '.5rem', 'comment' => $live_preview_message),
				'$border-radius-xl' => array('type' => 'text','placeholder' => '1rem', 'comment' => $live_preview_message),
				'$border-radius-2xl' => array('type' => 'text','placeholder' => '2rem', 'comment' => $live_preview_message),
				'$border-radius-pill' => array('type' => 'text','placeholder' => '50rem', 'comment' => $live_preview_message),
				

				),
			
			
			
			//add another section
			"typography" => array( // $variable_name => $variable_props
				
				
							
				'$font-family-base' => array('type' => 'text', 'placeholder' => '$font-family-sans-serif ', 'newgroup' => 'Font Families', 'comment' => $live_preview_message ), 
				'$font-family-sans-serif' => array('type' => 'text', ),
				'$font-family-monospace' => array('type' => 'text' ),
				
				'$font-size-base' => array('newgroup' => 'Font Sizes', 'type' => 'text', 'placeholder' => '1rem'),
				
				'$font-size-sm' => array('type' => 'text', 'placeholder' => '.875rem '),
				'$font-size-lg' => array('type' => 'text', 'placeholder' => '1.25rem'),

				'$enable-rfs' => array('type' => 'boolean','default' => 'true'),
				
				'$font-weight-base' => array('newgroup' => 'Font Weights', 'type' => 'text', 'placeholder' => '400', 'comment' => $live_preview_message),
				'$line-height-base' => array('type' => 'text', 'placeholder' => '1.5', 'comment' => $live_preview_message),

				
				
				'$font-weight-lighter' => array('type' => 'text', 'placeholder' => 'lighter '),
				'$font-weight-light' => array('type' => 'text', 'placeholder' => '300'),
				'$font-weight-normal' => array('type' => 'text', 'placeholder' => '400'),
				'$font-weight-semibold' => array('type' => 'text', 'placeholder' => '600'),
				'$font-weight-bold' => array('type' => 'text', 'placeholder' => '700'),
				'$font-weight-bolder' => array('type' => 'text', 'placeholder' => 'bolder'),
				
				
			
				'$headings-font-family' => array('type' => 'text', 'placeholder' => 'null','newgroup' => 'Headings', 'comment' => $live_preview_message ),
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
				//'$display-font-weight' => array('type' => 'text', 'placeholder' => '300'),
				//'$display-line-height' => array('type' => 'text', 'placeholder' => ' $headings-line-height '),
				
				'$lead-font-size' => array('newgroup' => 'Lead, Small and Muted', 'type' => 'text', 'placeholder' => '1.25rem'),
				'$lead-font-weight' => array('type' => 'text', 'placeholder' => '300'),
				
				'$small-font-size' => array('type' => 'text', 'placeholder' => '80%'),
				
				'$text-muted' => array('type' => 'color',  ),
				
				
				'$blockquote-margin-y' => array('newgroup' => 'Blockquotes', 'type' => 'text', 'placeholder' => '$spacer'),
				'$blockquote-font-size' => array('type' => 'text', 'placeholder' => '1.25rem '),
				'$blockquote-footer-color' => array('type' => 'color' ),
				'$blockquote-footer-font-size' => array('type' => 'text', 'placeholder' => '$small-font-size'),

				
				
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
			
			
			
			
			//add another section for FORMS
			"buttons-forms" => array( // $variable_name => $variable_props
				
							
				'$input-btn-padding-y' => array('type' => 'text','placeholder' => '.375rem'),
				'$input-btn-padding-x' => array('type' => 'text','placeholder' => '.75rem'),
				'$input-btn-font-family' => array('type' => 'text','placeholder' => 'null'),
				'$input-btn-font-size' => array('type' => 'text','placeholder' => '$font-size-base'),
				'$input-btn-line-height' => array('type' => 'text','placeholder' => '$line-height-base'),
				
				'$input-btn-focus-width' => array('type' => 'text','placeholder' => '.2rem'),
				'$input-btn-focus-color-opacity' => array('type' => 'text','placeholder' => '.25'),
				'$input-btn-focus-color' => array('type' => 'color','placeholder' => 'rgba($component-active-bg, .25)'),
				'$input-btn-focus-blur' => array('type' => 'text','placeholder' => '0'),
				'$input-btn-focus-box-shadow' => array('type' => 'text','placeholder' => '0 0 0 $input-btn-focus-width $input-btn-focus-color'),
				
				'$input-btn-padding-y-sm' => array('type' => 'text','placeholder' => '.25rem'),
				'$input-btn-padding-x-sm' => array('type' => 'text','placeholder' => '.5rem'),
				'$input-btn-font-size-sm' => array('type' => 'text','placeholder' => '$font-size-sm'),
				 
				'$input-btn-padding-y-lg' => array('type' => 'text','placeholder' => '.5rem'),
				'$input-btn-padding-x-lg' => array('type' => 'text','placeholder' => '1rem'),
				'$input-btn-font-size-lg' => array('type' => 'text','placeholder' => '$font-size-lg'),
 
				'$input-btn-border-width' => array('type' => 'text','placeholder' => '$border-width'),
				

				),
			
			
			//add another section for BUTTONS
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
	} //end function

endif;


//ENABLE SELECTIVE REFRESH 
add_theme_support( 'customize-selective-refresh-widgets' );

//ADD CUSTOMIZATION HELPER ICONS & CONFIGURE CUSTOMIZATION LIVE PREVIEWS
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
	
	//SINGLE: metas: date and author
	/*
	$wp_customize->selective_refresh->add_partial( 'singlepost_entry_meta', array(
		'selector' => '#single-post-meta',
		'settings' => array( 'singlepost_disable_entry_meta' ),
		'render_callback' => '__return_false'    
	));
	*/

	//SINGLE: meta date  
	$wp_customize->selective_refresh->add_partial( 'singlepost_date', array(
		'selector' => '.post-date',
		'settings' => array( 'singlepost_disable_date' ),
		'render_callback' => '__return_false'    
	));

	//SINGLE: meta author
	$wp_customize->selective_refresh->add_partial( 'singlepost_author', array(
		'selector' => '.post-author',
		'settings' => array( 'singlepost_disable_author' ),
		'render_callback' => '__return_false'    
	));

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
	//COLORS section is already built, so lets define the other ones
		 
	$wp_customize->add_section("typography", array(
        "title" => __("Typography", "picostrap"),
        "priority" => 50,
    ));
	 
	$wp_customize->add_section("components", array(
        "title" => __("Global Options", "picostrap"),
        "priority" => 50,
    ));
	
	$wp_customize->add_section("buttons-forms", array(
        "title" => __("Forms", "picostrap"),
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
			if (array_key_exists('default',$variable_props)) $default = $variable_props['default']; else $default="";
			if (array_key_exists('newgroup',$variable_props)) $optional_grouptitle = " <span hidden class='cs-option-group-title'>".$variable_props['newgroup']."</span> "; else $optional_grouptitle="";
			if (array_key_exists('comment',$variable_props)) $optional_comment = " <span class='cs-optional-comment'>".$variable_props['comment']."</span> "; else $optional_comment="";
			
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
						'description' => $optional_grouptitle. " (<span class='variable-name'>".$variable_name."</span>) ".$optional_comment, 
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
						'description' => $optional_grouptitle.  " (<span class='variable-name'>".$variable_name."</span>) " .$optional_comment, 
						'section' => $section_slug, //select the section for it to appear under
						'type' => 'checkbox'
						)
				));
			endif;
			
			if($variable_type=="text"):
			
				if(array_key_exists('placeholder',$variable_props)) $placeholder_html="<b>Default:</b> ".$variable_props['placeholder']; else $placeholder_html="";

				$wp_customize->add_setting($variable_slug, array(
					"default" => $default,
					"transport" => (in_array($variable_slug, array('SCSSvar_font-family-base','SCSSvar_headings-font-family')) )  ? "refresh" : "postMessage",
					//"default" => "1rem",
					//'sanitize_callback' => 'picostrap_sanitize_rem'
				));
				$wp_customize->add_control(new WP_Customize_Control(
					$wp_customize,
					$variable_slug,
					array(
						'label' => __( $variable_pretty_format_name, 'picostrap' ), //set the label to appear in the Customizer
						'description' => $optional_grouptitle. " <!-- (".$variable_name.") -->".$placeholder_html." ". $optional_comment,  
						'section' => $section_slug, //select the section for it to appear under
						'type' => 'text',
						 'input_attrs' => array(
							//'placeholder' => strip_tags($placeholder_html),
							'title' => esc_attr($variable_name)
							)
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
			'description' =>" <span hidden class='cs-option-group-title'>Extra</span>", //to implement a divisor
			'type' => 'color'  
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
			"description" => __("Requires Navbar position set to 'Standard static top'", "picostrap"),
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
	
	// ADD SECTION FOR SINGLE POST & ARCHIVES //////////////////////////////////////////////////////////////////////////////////////////////////////////
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

	//ENTRY METAS: AUTHOR & DATE  
	/*
	$wp_customize->add_setting("singlepost_disable_entry_meta", array(
		"default" => "",
		"transport" => "refresh",
	));
	$wp_customize->add_control(new WP_Customize_Control(
		$wp_customize,
		"singlepost_disable_entry_meta",
		array(
			"label" => __("Hide Post Metas: Date and Author", "picostrap"),
			//"description" => __("Publish and exit the Customizer to see the effect", "picostrap"),
			"section" => "singleposts", 
			'type'     => 'checkbox',
			)
	));
	*/

	//ENTRY META: AUTHOR   
	$wp_customize->add_setting("singlepost_disable_author", array(
		"default" => "",
		"transport" => "refresh",
	));
	$wp_customize->add_control(new WP_Customize_Control(
		$wp_customize,
		"singlepost_disable_author",
		array(
			"label" => __("Hide Post Author", "picostrap"),
			//"description" => __("Publish and exit the Customizer to see the effect", "picostrap"),
			"section" => "singleposts", 
			'type'     => 'checkbox',
			)
	));

	//ENTRY META: DATE  
	$wp_customize->add_setting("singlepost_disable_date", array(
		"default" => "",
		"transport" => "refresh",
	));
	$wp_customize->add_control(new WP_Customize_Control(
		$wp_customize,
		"singlepost_disable_date",
		array(
			"label" => __("Hide Post Date", "picostrap"),
			//"description" => __("Publish and exit the Customizer to see the effect", "picostrap"),
			"section" => "singleposts", 
			'type'     => 'checkbox',
			)
	));

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

	/* 
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
	
	// ADD A SECTION FOR HEADER & FOOTER CODE /////////////////////////////////////
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
			'description' =>'Will be added to the &lt;HEAD&gt; of all site pages'
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
			'description' =>'Will be added before closing the &lt;BODY&gt;  of all site pages'
			)
    ));

	//ADD BODY FONT OBJECT - hidden by CSS
	$wp_customize->add_setting("body_font_object", array(
        "default" => "",
		"transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "body_font_object",
        array(
            "label" => __("body_font_object", "picostrap"),
            "section" => "addcode",
            'type'     => 'textarea',
			'description' =>'<b>Not editable</b> - Internal purpose only.'
			)
    ));

	//ADD HEADINGS FONT OBJECT - hidden by CSS
	$wp_customize->add_setting("headings_font_object", array(
        "default" => "",
		"transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "headings_font_object",
        array(
            "label" => __("headings_font_object", "picostrap"),
            "section" => "addcode",
            'type'     => 'textarea',
			'description' =>'<b>Not editable</b> - Internal purpose only.'
			)
    ));

	//ADD FONT LOADING HEADER CODE  
	$wp_customize->add_setting("picostrap_fonts_header_code", array(
        "default" => "",
		"transport" => "refresh",
        //"transport" => "postMessage", // and no custom js is added: so no live page update is done, how it should be - but causes unstable behaviour
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_fonts_header_code",
        array(
            "label" => __("Font Loading Header code", "picostrap"),
            "section" => "addcode",
            'type'     => 'textarea',
			'description' =>__('
						The code in the field below is generated each time you set a new font family for body or headings, and is served in the site\'s &lt;head&gt;.
						<br><br>
						You can customize this code, for example to add multiple font weights, but please mind that if you choose new fonts, your customizations will be lost.
						<br><br>
						In case you break things up while editing, you can manually regenerate the code <a href="#" id="regenerate-font-loading-code">clicking here</a>.
						<br><br>
						For further information, and to understand how to enable multiple font weights,  please refer to our <a target="_blank" href="https://www.youtube.com/watch?v=dmsUpFJwDW8&t=200s">video documentation</a> and to the <a target="_blank" href="https://fonts.google.com/">Google Fonts website</a>.
						')
			)
    ));
	
	//USE ALTERNATIVE FONT SOURCE FOR GDPR COMPLIANCE
	$wp_customize->add_setting("picostrap_fonts_use_alternative_font_source", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_fonts_use_alternative_font_source",
        array(
            "label" => __("Load Google Fonts anonymously", "picostrap"),
			"description" =>  __("<b>Google Fonts can be an issue for GDPR compliance in Europe. </b>").
			__("Checking this option, Google fonts will be loaded from the privacy compliant <a target='_blank' href='https://fonts.coollabs.io/'>Coollabs Font repository</a>. ", "picostrap"),
            "section" => "addcode", 
            'type'     => 'checkbox',
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
			"description" =>  __("<b>Keep this unchecked, unless you really want. </b>").__("Prevents the code of 
			the textarea above from being served in the site's &lt;head&gt;. ", "picostrap"),
            "section" => "addcode", 
            'type'     => 'checkbox',
			)
    ));
	

	// ADD A SECTION FOR EXTRAS /////////////////////////////////////////////////////////////////////////////
	$wp_customize->add_section("extras", array(
        "title" => __("Global Utilities", "picostrap"),
        "priority" => 190,
    ));
	
	//DISABLE GUTENBERG
	$wp_customize->add_setting("disable_gutenberg", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "disable_gutenberg",
        array(
            "label" => __("Disable the Gutenberg Content Editor", "picostrap"),
			"description" => __("Disables the Gutenberg content editor on all post types. De-enqueues its CSS styles as well.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
    ));

	//DISABLE WIDGETS BLOCK EDITOR
	$wp_customize->add_setting("disable_widgets_block_editor", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "disable_widgets_block_editor",
        array(
            "label" => __("Disable the Block-based Widgets Editor", "picostrap"),
			"description" => __("Disables the Block-based Widgets Editor and restores the classic widgets editor.", "picostrap"),
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
            "label" => __("Disable WordPress Comments", "picostrap"),
			"description" => __("Will completely disable the entire WP comments feature.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
    ));

	//DISABLE XML-RPC
	$wp_customize->add_setting("disable_xml_rpc", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "disable_xml_rpc",
        array(
            "label" => __("Disable XML - RPC", "picostrap"),
			"description" => __("Disabling XML-RPC will close one more door that a potential hacker may try to exploit to hack your website.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
    ));

	//DISABLE LIVERELOAD
	$wp_customize->add_setting("picostrap_disable_livereload", array(
        "default" => "",
        "transport" => "refresh",
    ));
	$wp_customize->add_control(new WP_Customize_Control(
        $wp_customize,
        "picostrap_disable_livereload",
        array(
            "label" => __("Disable  SCSS Autocompile / LiveReload ", "picostrap"),
			"description" => __("If you're not editing the SCSS files, you can check this option. Makes a difference for site admins only.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
	));

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
			"description" => __("Will lazily add a JS and a CSS file from cdn.jsdelivr.net before closing the BODY of the page, to use   <a target='_blank' href='https://github.com/biati-digital/glightbox'>gLightBox</a>: a very lightweight lightbox implementation. <br><br>The lightbox will be enabled on all images matching the selector: main#theme-main a:not(.nolightbox) img<br><br>This means that any image linked to something will open a lightbox.<br><br>To force the lightbox on an element, add the <b>glightbox</b> class to it.<br><br> To prevent the lightbox on a linked image, add the <b>nolightbox</b> class to it.", "picostrap"),
            "section" => "extras", 
            'type'     => 'checkbox',
			)
	));
	


} //end function
 





/////////// LIVE CUSTOMIZER HELPER FOR CSS  VARIABLES ///////////

// if we are in customizer preview iframe,
// add some CSS that alters the bootstrap css variables,
// so live preview is possible
// check out also customizer-live-preview.js

//Please remember that for the input widgets down below to work,
// we have to have the controls "transport" setting set to "refresh"

add_action( 'wp_head', function  () {
	if (!current_user_can('administrator') OR !isset($_GET['customize_theme'])) return;
    ?>
	<style>
		:root {
			<?php if (get_theme_mod("SCSSvar_font-family-base")): ?>
				--bs-body-font-family: "<?php echo get_theme_mod("SCSSvar_font-family-base") ?>" !important;
			<?php endif ?>

		} /* close :root */
		
		h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
			<?php if (get_theme_mod("SCSSvar_headings-font-family")): ?>
				font-family:"<?php echo get_theme_mod("SCSSvar_headings-font-family") ?>" !important; 
			<?php endif ?>
		}
		
	</style>
	<?php
} );
 