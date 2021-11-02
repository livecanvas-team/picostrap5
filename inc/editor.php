<?php
/**
 * picostrap modify editor
 *
 * @package picostrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


//add_action( 'admin_init', 'picostrap_wpdocs_theme_add_editor_styles' );

if ( ! function_exists( 'picostrap_wpdocs_theme_add_editor_styles' ) ) {
	/**
	 * Registers an editor stylesheet for the theme.
	 */
	function picostrap_wpdocs_theme_add_editor_styles() {
		add_editor_style( 'custom-editor-style.min.css' );
	}
}

add_filter( 'mce_buttons_2', 'picostrap_tiny_mce_style_formats' );

if ( ! function_exists( 'picostrap_tiny_mce_style_formats' ) ) {
	/**
	 * Reveals TinyMCE's hidden Style dropdown.
	 *
	 * @param array $buttons Array of Tiny MCE's button ids.
	 * @return array
	 */
	function picostrap_tiny_mce_style_formats( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}
}

add_filter( 'tiny_mce_before_init', 'picostrap_tiny_mce_before_init' );

if ( ! function_exists( 'picostrap_tiny_mce_before_init' ) ) {
	/**
	 * Adds style options to TinyMCE's Style dropdown.
	 *
	 * @param array $settings TinyMCE settings array.
	 * @return array
	 */
	function picostrap_tiny_mce_before_init( $settings ) {

		$style_formats = array(
			array(
				'title'    => 'Lead Paragraph',
				'selector' => 'p',
				'classes'  => 'lead',
				'wrapper'  => true,
			),
			array(
				'title'  => 'Small',
				'inline' => 'small',
			),
			array(
				'title'   => 'Blockquote',
				'block'   => 'blockquote',
				'classes' => 'blockquote',
				'wrapper' => true,
			),
			array(
				'title'   => 'Blockquote Footer',
				'block'   => 'footer',
				'classes' => 'blockquote-footer',
				'wrapper' => true,
			),
			array(
				'title'  => 'Cite',
				'inline' => 'cite',
			),
		);

		if ( isset( $settings['style_formats'] ) ) {
			$orig_style_formats = json_decode( $settings['style_formats'], true );
			$style_formats      = array_merge( $orig_style_formats, $style_formats );
		}

		$settings['style_formats'] = wp_json_encode( $style_formats );
		return $settings;
	}
}
