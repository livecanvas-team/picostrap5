<?php
/**
 * Theme basic setup
 *
 * @package picostrap5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

add_action( 'after_setup_theme', 'picostrap_setup' );

if ( ! function_exists( 'picostrap_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function picostrap_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on picostrap, use a find and replace
		 * to change 'picostrap' to the name of your theme in all the template files
		 */
		load_theme_textdomain( 'picostrap', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'picostrap' ),
				'secondary' => __( 'Secondary Menu', 'picostrap' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		/*
		 * Adding Thumbnail basic support
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Adding support for Widget edit icons in customizer
		 */
		add_theme_support( 'customize-selective-refresh-widgets' );

		/*
		 * Enable support for Post Formats.
		 * See http://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
			)
		);

		// Set up the WordPress core custom background feature.
		/*
		add_theme_support(
			'custom-background',
			apply_filters(
				'picostrap_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);
		*/
		
		// Set up the WordPress Theme logo feature.
		add_theme_support( 'custom-logo' );

		// Add support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );

		// Check and setup theme default settings.
		picostrap_setup_theme_default_settings();

	}
}

add_filter("excerpt_length",function($in){
	return 22;
});



add_filter( 'excerpt_more', 'picostrap_custom_excerpt_more' );

if ( ! function_exists( 'picostrap_custom_excerpt_more' ) ) {
	/**
	 * Removes the ... from the excerpt read more link
	 *
	 * @param string $more The excerpt.
	 *
	 * @return string
	 */
	function picostrap_custom_excerpt_more( $more ) {
		if ( ! is_admin() ) {
			$more = '';
		}
		return $more;
	}
}

add_filter( 'wp_trim_excerpt', 'picostrap_all_excerpts_get_more_link' );

if ( ! function_exists( 'picostrap_all_excerpts_get_more_link' ) ) {
	/**
	 * Adds a custom read more link to all excerpts, manually or automatically generated
	 *
	 * @param string $post_excerpt Posts's excerpt.
	 *
	 * @return string
	 */
	function picostrap_all_excerpts_get_more_link( $post_excerpt ) {
		if ( ! is_admin() ) {
			$post_excerpt = $post_excerpt . '...<p class="text-right"><a class="btn btn-outline-secondary picostrap-read-more-link " href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __(
				'Read More...',
				'picostrap'
			) . '</a></p>';
		}
		return $post_excerpt;
	}
}




// FIX WORDPRESS CATEGORY / ARCHIVE TITLES /////////
add_filter( 'get_the_archive_title', function ($title) {
    if ( is_category() ) {
            $title = single_cat_title( '', false );
        } elseif ( is_tag() ) {
            $title = single_tag_title( '', false );
        } elseif ( is_author() ) {
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;
    }
    return $title;
});



//RESPONSIVE VIDEO EMBEDS
function bootstrap_wrap_oembed( $html ){
	$html = preg_replace( '/(width|height)="\d*"\s/', "", $html ); // Strip width and height #1
	return'<div class="ratio ratio-16x9">'.$html.'</div>'; // Wrap in div element and return #3 and #4
  }
  add_filter( 'embed_oembed_html','bootstrap_wrap_oembed',10,1);
