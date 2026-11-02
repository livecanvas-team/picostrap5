<?php
/**
 * Add Theme Options Page
 *
 * @package picostrap5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/child-theme.php';
require_once __DIR__ . '/options-performance.php';
require_once __DIR__ . '/options-resources.php';

//FOR EXPORTING JSON FILE FROM BACKEND
add_action('admin_init', 'pico_handle_theme_options_download');

function pico_handle_theme_options_download() {
	if (!isset($_GET['pico_download_theme_options'])) return;

	if (!current_user_can('manage_options')) {
		wp_die('Unauthorized');
	}

	$mods = get_theme_mods();
	if (!is_array($mods)) {
		wp_die('No theme mods found');
	}

	if (isset($mods[0])) {
		unset($mods[0]);
	}

	// ✅ Filter out empty values (but keep false, 0, '0')
	$filtered_mods = array_filter($mods, function($val) {
		if (is_array($val)) {
			return !empty($val); // keep non-empty arrays
		}
		return !($val === '' || $val === null); // skip empty strings and nulls
	});

	$json = json_encode($filtered_mods, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

	header('Content-Description: File Transfer');
	header('Content-Type: application/json');
	header('Content-Disposition: attachment; filename="theme-options.json"');
	header('Content-Length: ' . strlen($json));
	echo $json;
	exit;
}

//TO EXPORT MENUS 
add_action('admin_init', function () {
    if (!is_admin() || !current_user_can('manage_options')) return;

    if (!isset($_GET['pico_download_menus'])) return;

    $menus = wp_get_nav_menus();
    $all_data = [];

    foreach ($menus as $menu) {
        $menu_items = wp_get_nav_menu_items($menu->term_id, ['orderby' => 'menu_order']);
        $export_items = [];

        foreach ($menu_items as $item) {
            $export_items[] = [
                'title'      => $item->title,
                'url'        => $item->url,
                'menu_order' => $item->menu_order,
                'parent'     => $item->menu_item_parent,
                'type'       => $item->type,
                'object'     => $item->object,
                'object_id'  => $item->object_id,
                'classes'    => $item->classes,
                'attr_title' => $item->attr_title,
                'target'     => $item->target,
                'description'=> $item->description,
            ];
        }

        $all_data[] = [
            'menu_name'  => $menu->name,
            'menu_slug'  => $menu->slug,
            'items'      => $export_items,
        ];
    }

    $json = json_encode($all_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename=menus.json');
    header('Content-Length: ' . strlen($json));
    echo $json;
    exit;
});

//GET PARENT THEME VERSION
function pico_get_parent_theme_version(){
		
	//get active parent theme / framework information
	if (get_template_directory() === get_stylesheet_directory())  { $my_theme = wp_get_theme(); } else { $my_theme = wp_get_theme()->parent(); }
				
	//print theme version 
	return $my_theme->get( 'Version' );
}

//ADD THEME OPTIONS PAGE
function add_picostrap_theme_page() {
    add_theme_page( __( 'Picostrap Options', 'picostrap5' ), __( 'Picostrap Options', 'picostrap5' ), 'edit_theme_options', 'picostrap-theme-options', 'theme_option_page', 0 );
}
// Register after standard plugin menus so the theme workspace stays easy to find.
add_action( 'admin_menu', 'add_picostrap_theme_page', 99 );
 
// Apply the saved preference before paint, only on this admin screen.
add_action( 'admin_head-appearance_page_picostrap-theme-options', function () {
    ?>
    <script>
    try {
        if (localStorage.getItem('picostrap-options-theme') === 'dark') {
            document.documentElement.dataset.picoOptionsTheme = 'dark';
        }
    } catch (error) { /* Storage may be unavailable in private browser sessions. */ }
    </script>
    <?php
} );

// Load admin styles only on the theme options screen.
add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {
    if ( 'appearance_page_picostrap-theme-options' !== $hook_suffix ) {
        return;
    }
    $base = get_template_directory_uri() . '/inc/options-page/';
    wp_enqueue_style( 'picostrap-options', $base . 'options-page.css', array(), filemtime( __DIR__ . '/options-page.css' ) );
    wp_enqueue_script( 'picostrap-options', $base . 'options-page.js', array(), filemtime( __DIR__ . '/options-page.js' ), true );
    wp_localize_script( 'picostrap-options', 'picoOptions', array(
        'ajax' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'picostrap_css_preview' ),
        'home' => home_url( '/' ),
        'deliveryTargets' => array(
            array( 'id' => 'html', 'url' => home_url( '/' ), 'type' => 'text/html' ),
            array( 'id' => 'css', 'url' => picostrap_get_css_url(), 'type' => 'text/css' ),
            array( 'id' => 'js', 'url' => get_template_directory_uri() . '/js/bootstrap.bundle.min.js', 'type' => 'javascript' ),
        ),
        'delivery' => array(
            'checking' => __( 'Checking public responses…', 'picostrap5' ),
            'check' => __( 'Check again', 'picostrap5' ),
            'none' => __( 'No compression header', 'picostrap5' ),
            'unknown' => __( 'Could not verify', 'picostrap5' ),
            'complete' => __( 'Compression observed on every sampled response.', 'picostrap5' ),
            'partial' => __( 'Some responses have no compression header.', 'picostrap5' ),
            'failed' => __( 'Some responses could not be verified. Check the public site in your browser’s Network panel.', 'picostrap5' ),
            'missing' => __( 'Ask your host or CDN to enable gzip or Brotli for HTML, CSS and JavaScript. Compression is configured at the server or CDN, not by this theme. Recheck the public HTTPS site after changing it.', 'picostrap5' ),
            'gzip' => __( 'Gzip is working on these responses. Ask your host or CDN whether Brotli is available over HTTPS; compare actual transfer sizes before switching. Keep a gzip fallback for clients that need it.', 'picostrap5' ),
            'modern' => __( 'Brotli or Zstandard is being served on at least one response. Keep compatible fallbacks and verify HTML, CSS and JavaScript separately; a single encoding is not necessarily best for every resource.', 'picostrap5' ),
            'other' => __( 'A Content-Encoding header is present. Confirm the encoding and transferred size in your browser’s Network panel; server and CDN settings may vary by resource.', 'picostrap5' ),
            'insecure' => __( 'This page uses HTTP. Repeat the check on the production HTTPS URL: secure connections can negotiate additional compression methods.', 'picostrap5' ),
            'unavailable' => __( 'Unavailable', 'picostrap5' ),
        ),
        'saved' => __( 'All changes saved', 'picostrap5' ), 'unsaved' => __( 'Unsaved changes', 'picostrap5' ),
        'saving' => __( 'Saving changes…', 'picostrap5' ), 'analyzing' => __( 'Analyzing homepage…', 'picostrap5' ),
        'analyze' => __( 'Analyze homepage', 'picostrap5' ),
        'error' => __( 'Could not analyze the homepage. Check your connection and try again.', 'picostrap5' ),
        'previewDone' => __( 'Homepage preview complete. Raw bytes and gzip estimates include the fallback helper. No settings changed.', 'picostrap5' ),
        'leave' => __( 'You have unsaved changes.', 'picostrap5' ),
    ) );
} );

/** Prominent design actions shared by Overview and Design & Sass. */
function picostrap_options_design_actions( $compile_url ) {
    $id = wp_unique_id( 'pico-design-actions-' );
    ?>
    <div class="pico-design-actions">
        <div class="pico-design-action">
            <a class="pico-button pico-primary pico-design-button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>" aria-describedby="<?php echo esc_attr( $id . '-customizer' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M4 17h16"/><circle cx="9" cy="7" r="3" fill="currentColor" stroke="none"/><circle cx="15" cy="17" r="3" fill="currentColor" stroke="none"/></svg>
                <span><?php esc_html_e( 'Open WordPress Customizer', 'picostrap5' ); ?></span>
            </a>
            <p id="<?php echo esc_attr( $id . '-customizer' ); ?>"><?php esc_html_e( 'Change colors, fonts, layouts and Bootstrap settings with a live preview. Publish your changes to save your design.', 'picostrap5' ); ?></p>
        </div>
        <div class="pico-design-action">
            <?php if ( current_user_can( 'administrator' ) ) : ?>
            <a class="pico-button pico-design-button" href="<?php echo esc_url( $compile_url ); ?>" aria-describedby="<?php echo esc_attr( $id . '-compile' ); ?>">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 7v5h-5M4 17v-5h5"/><path d="M6.1 6.1A8 8 0 0 1 20 12M4 12a8 8 0 0 0 13.9 5.9"/></svg>
                <span><?php esc_html_e( 'Recompile CSS', 'picostrap5' ); ?></span>
            </a>
            <?php else : ?>
            <span class="pico-button pico-design-button" aria-disabled="true"><?php esc_html_e( 'Recompile CSS', 'picostrap5' ); ?></span>
            <?php endif; ?>
            <p id="<?php echo esc_attr( $id . '-compile' ); ?>"><?php esc_html_e( 'Rebuild and save the theme stylesheet from your saved settings and Sass files. Opens the frontend compiler; administrator access and an internet connection are required.', 'picostrap5' ); ?></p>
        </div>
    </div>
    <?php
}

function theme_option_page() {
    if ( ! current_user_can( 'edit_theme_options' ) ) return;
    $show_livecanvas = ! picostrap_options_livecanvas_installed();
    $child_active = is_child_theme();
    $compile_url = add_query_arg( array( 'compile_sass' => '1', 'sass_nocache' => '1' ), home_url( '/' ) );
    $has_custom_bundle = (bool) get_theme_mod( 'css_bundle_version_number', 0 );
    $bundle_name = $has_custom_bundle ? picostrap_get_complete_css_filename() : picostrap_get_base_css_filename();
    $bundle_relative = picostrap_get_css_optional_subfolder_name() . $bundle_name;
    $bundle_path = picostrap_options_bundle_path();
    $bundle_exists = is_file( $bundle_path );
    $css_bytes = picostrap_options_asset_bytes( $bundle_path );
    $js_path = get_template_directory() . '/js/bootstrap.bundle.min.js';
    $js_bytes = picostrap_options_asset_bytes( $js_path );
    $css_gzip = picostrap_options_asset_gzip_bytes( $bundle_path );
    $js_gzip = picostrap_options_asset_gzip_bytes( $js_path );
    $emoji_bytes = picostrap_options_asset_bytes( ABSPATH . WPINC . '/js/wp-emoji-release.min.js' );
    $embed_bytes = picostrap_options_asset_bytes( ABSPATH . WPINC . '/js/wp-embed.min.js' );
    $main_path = locate_template( 'sass/main.scss' );
    $main_source = $main_path && is_readable( $main_path ) ? file_get_contents( $main_path ) : false;
    $sass_source = ps_get_main_sass();
    $settings = picostrap_get_extra_settings();
    $enabled = 0;
    foreach ( $settings as $key => $definition ) if ( get_theme_mod( $key, false ) ) ++$enabled;
    $groups = array(
        array( 'title' => __( 'CSS & frontend assets', 'picostrap5' ), 'description' => __( 'Choose what your frontend actually needs.', 'picostrap5' ), 'keys' => array( 'disable_gutenberg', 'picostrap_inline_css', 'disable_bootstrap' ) ),
        array( 'title' => __( 'Optional interactions', 'picostrap5' ), 'description' => __( 'Load extra interactions only when you use them.', 'picostrap5' ), 'inline_help' => true, 'keys' => array( 'enable_back_to_top', 'enable_open_menu_on_hover', 'enable_lightbox', 'enable_tooltips' ) ),
        array( 'title' => __( 'WordPress features', 'picostrap5' ), 'description' => __( 'Keep the tools and services your project uses.', 'picostrap5' ), 'keys' => array( 'disable_widgets_block_editor', 'singlepost_disable_comments', 'disable_xml_rpc' ) ),
    );
    $hints = array(
        'disable_gutenberg' => __( 'Switches posts and pages to the classic editor and removes WordPress core block CSS, global styles and duotone assets from the frontend. Use for sites built without blocks: existing block content may lose its styling. Block styles are preserved when LiveCanvas Gutenberg compatibility is enabled.', 'picostrap5' ),
        'picostrap_inline_css' => __( 'Replaces loading the full theme CSS bundle file with reduced, page-specific CSS embedded directly in the page HTML. The bundle file is kept as the source and fallback. Experimental: compare PageSpeed with this on and off, test layouts and interactions on desktop and mobile, and clear your page cache after changing it.', 'picostrap5' ),
        'disable_bootstrap' => __( 'Enable only if you know what you are doing. Removes the entire theme stylesheet and Bootstrap JavaScript. You must add your own CSS; layouts and interactive components may stop working until you provide replacements.', 'picostrap5' ),
        'enable_back_to_top' => __( 'A lightweight return-to-top control for longer pages.', 'picostrap5' ),
        'enable_open_menu_on_hover' => __( 'Open desktop navigation dropdowns on hover.', 'picostrap5' ),
        'enable_lightbox' => __( 'Load gLightBox for linked images and gallery interactions.', 'picostrap5' ),
        'enable_tooltips' => __( 'Initialize Bootstrap tooltips and popovers.', 'picostrap5' ),
        'disable_widgets_block_editor' => __( 'Use the classic widgets editor instead of blocks.', 'picostrap5' ),
        'singlepost_disable_comments' => __( 'Turn off WordPress comments across the site.', 'picostrap5' ),
        'disable_xml_rpc' => __( 'Disable the legacy XML-RPC interface.', 'picostrap5' ),
    );
    ?>
    <hr class="wp-header-end">
    <div class="wrap pico-options" id="pico-options">
        <header class="pico-brandbar">
            <img src="<?php echo esc_url( get_template_directory_uri() . '/inc/options-page/assets/picostrap-logo.svg' ); ?>" width="166" height="34" alt="Picostrap">
            <h1><?php esc_html_e( 'Picostrap Options', 'picostrap5' ); ?></h1>
            <span class="pico-version">v<?php echo esc_html( pico_get_parent_theme_version() ); ?></span>
            <label class="pico-theme-toggle" id="pico-theme-control" hidden>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 14a8 8 0 0 1-10-10 8 8 0 1 0 10 10Z"/></svg>
                <span><?php esc_html_e( 'Dark mode', 'picostrap5' ); ?></span>
                <input class="pico-switch" id="pico-theme-toggle" type="checkbox" role="switch" aria-label="<?php esc_attr_e( 'Dark mode for theme options', 'picostrap5' ); ?>">
            </label>
        </header>
        <nav class="pico-tabs" aria-label="<?php esc_attr_e( 'Theme options sections', 'picostrap5' ); ?>">
            <a href="#pico-overview"><?php esc_html_e( 'Overview', 'picostrap5' ); ?></a>
            <a href="#pico-extras-title"><?php esc_html_e( 'Optimizations', 'picostrap5' ); ?> <span><?php echo esc_html( $enabled ); ?>/<?php echo esc_html( count( $settings ) ); ?></span></a>
            <a href="#pico-design"><?php esc_html_e( 'Design & Sass', 'picostrap5' ); ?></a>
            <a href="#pico-tools"><?php esc_html_e( 'Tools & updates', 'picostrap5' ); ?></a>
            <a href="#pico-resources"><?php esc_html_e( 'Useful resources', 'picostrap5' ); ?></a>
            <?php if ( $show_livecanvas ) : ?><a href="#pico-livecanvas">LiveCanvas</a><?php endif; ?>
        </nav>

        <section class="pico-tabpanel" id="pico-overview" aria-labelledby="pico-overview-heading">
            <div class="pico-overview-hero">
                <div>
                    <h2 id="pico-overview-heading"><?php esc_html_e( 'Your Picostrap workspace', 'picostrap5' ); ?></h2>
                    <p><?php esc_html_e( 'Customize your design, rebuild your stylesheet and choose what your site loads. Start with a design action below or review your saved theme assets.', 'picostrap5' ); ?></p>
                    <div class="pico-actions"><a class="pico-text-link" href="#pico-extras-title" data-pico-link><?php esc_html_e( 'Tune your optimizations', 'picostrap5' ); ?></a></div>
                </div>
                <div class="pico-payload">
                    <h3><?php esc_html_e( 'Your theme payload', 'picostrap5' ); ?></h3>
                    <p><?php esc_html_e( 'Saved files, before and after compression.', 'picostrap5' ); ?></p>
                    <table class="pico-payload-table"><caption class="screen-reader-text"><?php esc_html_e( 'Theme asset sizes', 'picostrap5' ); ?></caption><thead><tr><th scope="col"><?php esc_html_e( 'Asset', 'picostrap5' ); ?></th><th scope="col"><?php esc_html_e( 'Raw', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Raw size', 'picostrap5' ), __( 'The saved file size before HTTP compression. It describes this file only, not the total page weight or transferred bytes.', 'picostrap5' ) ); ?></th><th scope="col"><?php esc_html_e( 'Gzip estimate', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Gzip estimate', 'picostrap5' ), __( 'Calculated locally with gzip level 6. This is a reproducible estimate, not proof that the server compresses responses. Actual bytes vary with compression level, Brotli or Zstandard, CDN settings and response headers. Inline CSS is compressed as part of its HTML document.', 'picostrap5' ) ); ?></th></tr></thead><tbody>
                        <tr><th scope="row"><span class="pico-dot"></span><?php esc_html_e( 'Theme CSS', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Theme CSS', 'picostrap5' ), __( 'The saved bundle combines framework, theme and custom Sass styles. The normal frontend loads it as a stylesheet. The experimental page-specific CSS option can replace that request with inline styles; plugin styles are separate.', 'picostrap5' ) ); ?></th><td><?php echo esc_html( $bundle_exists ? size_format( $css_bytes, 1 ) : __( 'Not built', 'picostrap5' ) ); ?></td><td><?php echo esc_html( null !== $css_gzip ? size_format( $css_gzip, 1 ) : __( 'Unavailable', 'picostrap5' ) ); ?></td></tr>
                        <tr><th scope="row"><span class="pico-dot pico-dot-pink"></span><?php esc_html_e( 'Bootstrap JS', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Bootstrap JS: deferred', 'picostrap5' ), __( 'Picostrap requests WordPress’s defer strategy and places Bootstrap JS in the footer. A deferred script downloads without blocking HTML parsing and executes after parsing, before DOMContentLoaded. This is deferred loading, not lazy loading triggered by a click or scroll. WordPress may adjust the strategy for dependencies; plugins can also change the final tag.', 'picostrap5' ) ); ?><span class="pico-asset-strategy"><?php echo esc_html( get_theme_mod( 'disable_bootstrap' ) ? __( 'Disabled', 'picostrap5' ) : __( 'Deferred · footer', 'picostrap5' ) ); ?></span></th><td><?php echo esc_html( $js_bytes ? size_format( $js_bytes, 1 ) : __( 'Not found', 'picostrap5' ) ); ?></td><td><?php echo esc_html( null !== $js_gzip ? size_format( $js_gzip, 1 ) : __( 'Unavailable', 'picostrap5' ) ); ?></td></tr>
                    </tbody></table>
                    <div class="pico-payload-bar" aria-hidden="true"><span style="flex:<?php echo esc_attr( max( 1, $css_bytes ) ); ?>"></span><span style="flex:<?php echo esc_attr( max( 1, $js_bytes ) ); ?>"></span></div>
                    <p class="pico-help"><?php esc_html_e( 'Gzip values are estimates. Check actual delivery below. Fonts, plugin assets and images are additional.', 'picostrap5' ); ?><?php if ( get_theme_mod( 'disable_bootstrap' ) ) : ?> <?php esc_html_e( 'Both theme assets are currently disabled.', 'picostrap5' ); ?><?php endif; ?></p>
                </div>
            </div>

            <?php picostrap_options_design_actions( $compile_url ); ?>

            <details class="pico-delivery" open>
                <summary><span><?php esc_html_e( 'Compression & delivery', 'picostrap5' ); ?></span><span class="pico-help" id="pico-delivery-summary"><?php esc_html_e( 'Not checked yet', 'picostrap5' ); ?></span></summary>
                <div class="pico-delivery-content">
                    <p><?php esc_html_e( 'What your browser receives from the public homepage and saved asset URLs.', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Observed HTTP compression', 'picostrap5' ), __( 'Checks Content-Encoding on fresh GET responses, without login cookies. gzip, br (Brotli) or zstd (Zstandard) identifies the encoding chosen for this browser. No header means no encoding was observed on that response. A failed, redirected or cross-origin response may be unverifiable. This does not certify the entire server or all visitors. File URLs are sampled even if that asset is disabled or replaced by inline CSS on the page.', 'picostrap5' ) ); ?></p>
                    <dl class="pico-delivery-resources">
                        <?php foreach ( array( 'html' => __( 'Homepage HTML', 'picostrap5' ), 'css' => __( 'Theme CSS file', 'picostrap5' ), 'js' => __( 'Bootstrap JS file', 'picostrap5' ) ) as $resource => $label ) : ?><div><dt><?php echo esc_html( $label ); ?></dt><dd id="pico-encoding-<?php echo esc_attr( $resource ); ?>"><?php esc_html_e( 'Not checked', 'picostrap5' ); ?></dd></div><?php endforeach; ?>
                    </dl>
                    <p class="pico-help" id="pico-delivery-advice" role="status" aria-live="polite"><?php esc_html_e( 'Use Check compression to inspect these responses. This does not change server settings.', 'picostrap5' ); ?></p>
                    <p class="pico-help" id="pico-delivery-https" hidden></p>
                    <button type="button" class="pico-button" id="pico-check-delivery"><?php esc_html_e( 'Check compression', 'picostrap5' ); ?></button>
                    <noscript><p><?php esc_html_e( 'Enable JavaScript to inspect response headers. You can also check Content-Encoding in your browser’s Network panel.', 'picostrap5' ); ?></p></noscript>
                </div>
            </details>
            <div class="pico-overview-grid">
                <section class="pico-panel pico-built-in" aria-labelledby="pico-clean-title">
                    <div class="pico-section-heading"><h3 id="pico-clean-title"><?php esc_html_e( 'Lighter from the start', 'picostrap5' ); ?></h3><span class="pico-badge"><?php esc_html_e( 'Built in', 'picostrap5' ); ?></span></div>
                    <p><?php esc_html_e( 'A concrete head start, before you touch a switch.', 'picostrap5' ); ?></p>
                    <?php foreach ( array(
                        array( __( 'A cleaner document head', 'picostrap5' ), __( 'Removes generator, feed, RSD, shortlink and adjacent-post discovery markup. Your feeds and REST API remain available.', 'picostrap5' ) ),
                        array( __( 'No automatic emoji or post-embed scripts', 'picostrap5' ), __( 'Disables WordPress emoji detection and automatic post-embed host JS. Supported video embeds keep working; plugins can still request dependencies.', 'picostrap5' ) ),
                        array( __( 'One compiled theme stylesheet', 'picostrap5' ), __( 'PicoSASS combines Bootstrap, NinjaBootstrap, theme styles and your Sass into one CSS bundle. Browser-based compilation needs no local build setup.', 'picostrap5' ) ),
                        array( __( 'Load deliberately', 'picostrap5' ), __( 'The normal theme bundle gets a preload hint; Bootstrap JS is deferred. Extra interactions stay opt-in, and block CSS can be disabled for block-free sites.', 'picostrap5' ) ),
                    ) as $item ) : ?><details class="pico-feature"><summary><?php echo esc_html( $item[0] ); ?></summary><p><?php echo esc_html( $item[1] ); ?></p></details><?php endforeach; ?>
                    <details class="pico-measure-note"><summary><?php printf( esc_html__( '%s of core script files kept out of automatic loading', 'picostrap5' ), esc_html( size_format( $emoji_bytes + $embed_bytes, 1 ) ) ); ?></summary><p><?php esc_html_e( 'Reference sizes from this WordPress installation, before compression. These are excluded files, not a measured per-visit saving: WordPress loads emoji conditionally, and plugins may request embed dependencies.', 'picostrap5' ); ?></p><dl class="pico-file-sizes"><div><dt>wp-emoji-release.min.js</dt><dd><?php echo esc_html( size_format( $emoji_bytes, 1 ) ); ?></dd></div><div><dt>wp-embed.min.js</dt><dd><?php echo esc_html( size_format( $embed_bytes, 1 ) ); ?></dd></div></dl></details>
                </section>
                <section class="pico-panel pico-lab" aria-labelledby="pico-lab-heading">
                    <div class="pico-section-heading"><h3 id="pico-lab-heading"><?php esc_html_e( 'How much CSS could you leave behind?', 'picostrap5' ); ?></h3><span class="pico-badge pico-experimental"><?php esc_html_e( 'CSS lab', 'picostrap5' ); ?></span></div>
                    <p><?php esc_html_e( 'Try a conservative reduction against your public homepage. See the potential before enabling page-specific CSS.', 'picostrap5' ); ?></p>
                    <div class="pico-lab-result" id="pico-lab-result" hidden>
                        <div class="pico-result-value"><strong id="pico-saved-bytes"></strong><span><?php esc_html_e( 'potentially removed', 'picostrap5' ); ?></span><?php picostrap_options_help( __( 'Potential reduction', 'picostrap5' ), __( 'The raw-byte difference between the full bundle and the inline candidate, including its helper. This is a preview for this homepage only, not measured network savings or a speed score. If the candidate is larger, the full bundle is kept.', 'picostrap5' ) ); ?></div>
                        <dl class="pico-file-sizes"><div><dt><?php esc_html_e( 'Original CSS', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Original CSS', 'picostrap5' ), __( 'The saved theme stylesheet before reduction. This raw-byte baseline excludes other stylesheets, fonts, images, and HTTP headers.', 'picostrap5' ) ); ?></dt><dd id="pico-original-bytes"></dd></div><div><dt><?php esc_html_e( 'Inline CSS + fallback helper', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Inline CSS + fallback helper', 'picostrap5' ), __( 'The CSS retained for this page, its inline style tag, and a small JavaScript guard with its token manifest. The guard restores the full theme bundle when dynamic content introduces unknown classes or IDs. On that fallback path, the external stylesheet is downloaded too. This preview includes helper overhead, so the comparison does not hide its cost.', 'picostrap5' ) ); ?></dt><dd id="pico-candidate-bytes"></dd></div><div><dt><?php esc_html_e( 'Original · gzip estimate', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Gzip comparison', 'picostrap5' ), __( 'Each payload is compressed independently with gzip level 6. The real inline payload is compressed together with its HTML document, while a stylesheet is compressed separately and can be cached across pages. These estimates are not actual transfer savings and need not improve by the same percentage as raw bytes.', 'picostrap5' ) ); ?></dt><dd id="pico-original-gzip"></dd></div><div><dt><?php esc_html_e( 'Inline + helper · gzip estimate', 'picostrap5' ); ?></dt><dd id="pico-candidate-gzip"></dd></div></dl>
                        <div class="pico-comparison" aria-hidden="true"><span id="pico-candidate-bar"></span></div>
                    </div>
                    <button type="button" class="pico-button" id="pico-analyze"><?php esc_html_e( 'Analyze homepage', 'picostrap5' ); ?> <span aria-hidden="true">→</span></button>
                    <p class="pico-lab-status pico-help" id="pico-lab-status" role="status" aria-live="polite"><?php esc_html_e( 'A preview only. Your settings stay as they are.', 'picostrap5' ); ?></p>
                    <noscript><p><?php esc_html_e( 'Enable JavaScript to run the CSS preview.', 'picostrap5' ); ?></p></noscript>
                    <details class="pico-measure-note"><summary><?php esc_html_e( 'What this measurement means', 'picostrap5' ); ?></summary><p><?php esc_html_e( 'Uses the public homepage HTML and your saved CSS bundle. Includes the runtime fallback helper and its markup. Raw values and gzip estimates are not actual transfer sizes or a speed score. Dynamic content may restore the original bundle. Other pages, cache layers and plugins can produce different results.', 'picostrap5' ); ?></p></details>
                </section>
            </div>
            <div class="pico-setup-strip"><span><span class="pico-dot"></span> <?php echo esc_html( wp_get_theme()->get( 'Name' ) ); ?></span><span><?php echo esc_html( $child_active ? __( 'Child theme active', 'picostrap5' ) : __( 'Parent theme active', 'picostrap5' ) ); ?></span><a href="#pico-design" data-pico-link><?php esc_html_e( 'Inspect your build', 'picostrap5' ); ?> <span aria-hidden="true">→</span></a></div>
            <section class="pico-sass-section" aria-labelledby="pico-capabilities-heading">
                <h2 id="pico-capabilities-heading"><?php esc_html_e( 'More than a lighter frontend.', 'picostrap5' ); ?></h2>
                <p><?php esc_html_e( 'Your starter toolkit, at a glance. Open a topic to see what is already in the theme.', 'picostrap5' ); ?></p>
                <?php foreach ( array(
                    array( __( 'Colors, typography & your design system', 'picostrap5' ), __( 'Customize Bootstrap variables, responsive typography and font choices. Use the palette generator for color ideas, then compile a consistent design system with PicoSASS.', 'picostrap5' ) ),
                    array( __( 'Navigation, dark mode & site structure', 'picostrap5' ), __( 'Configure the responsive navbar, its position and colors, an optional top bar and Bootstrap dark-mode switch. Use WordPress menus and widget areas, or let LiveCanvas handle custom headers and footers.', 'picostrap5' ) ),
                    array( __( 'Pages, publishing & WooCommerce', 'picostrap5' ), __( 'Start with blank, full-width and sidebar page templates. Blog and archive templates, pagination, featured images and optional sharing buttons support publishing. WooCommerce integration provides a starting point for shop layouts.', 'picostrap5' ) ),
                    array( __( 'LiveCanvas & WindPress integration', 'picostrap5' ), __( 'Theme configuration supports LiveCanvas visual editing and its reusable content. WindPress integration exposes theme sources for projects using its Tailwind workflow. These plugins are installed separately.', 'picostrap5' ) ),
                    array( __( 'Sass without a local build setup', 'picostrap5' ), __( 'Compile in the browser with Dart Sass. NinjaBootstrap extends the utility system; child-theme Sass files let you override defaults and add project styles. Inspect the pipeline in Design & Sass.', 'picostrap5' ) ),
                    array( __( 'Maintenance that stays in your hands', 'picostrap5' ), __( 'Use a child theme to keep custom code separate. Export Customizer settings and menus, and receive stable GitHub Releases through the native WordPress updater. Optional utilities remain under your control.', 'picostrap5' ) ),
                ) as $capability ) : ?><details class="pico-feature"><summary><?php echo esc_html( $capability[0] ); ?></summary><p><?php echo esc_html( $capability[1] ); ?></p></details><?php endforeach; ?>
            </section>
        </section>

        <section class="pico-tabpanel" id="pico-extras-title" aria-labelledby="pico-optimization-heading">
            <div class="pico-page-heading"><h2 id="pico-optimization-heading"><?php esc_html_e( 'Frontend & WordPress settings', 'picostrap5' ); ?></h2><p><?php esc_html_e( 'A few deliberate choices can make WordPress leaner. These switches use the same settings as Global Utilities in the Customizer.', 'picostrap5' ); ?></p></div>
            <?php if ( isset( $_GET['extras-saved'] ) ) : ?><p class="pico-feedback" role="status"><?php esc_html_e( 'Settings saved. Clear your page cache so cached pages receive the changes.', 'picostrap5' ); ?></p><?php endif; ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="pico-extras-form">
                <input type="hidden" name="action" value="picostrap_save_extras"><?php wp_nonce_field( 'picostrap_save_extras' ); ?>
                <?php foreach ( $groups as $group ) : ?>
                    <fieldset class="pico-settings-group"><legend><?php echo wp_kses( $group['title'], array( 'em' => array() ) ); ?></legend><p class="pico-group-description"><?php echo esc_html( $group['description'] ); ?></p>
                        <?php foreach ( $group['keys'] as $key ) : $definition = $settings[ $key ]; ?>
                            <div class="pico-setting">
                                <div class="pico-setting-copy"><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( 'picostrap_inline_css' === $key ? __( 'Page-specific CSS', 'picostrap5' ) : $definition['label'] ); ?></label><?php if ( 'picostrap_inline_css' === $key ) : ?><span class="pico-badge pico-experimental" id="pico-inline-css-status"><?php esc_html_e( 'Experimental', 'picostrap5' ); ?></span><?php endif; ?><?php if ( empty( $group['inline_help'] ) ) picostrap_options_help( $definition['label'], $definition['description'] ); ?><p id="<?php echo esc_attr( $key . '-hint' ); ?>"><?php echo esc_html( $hints[ $key ] ); ?></p>
                                    <?php if ( ! empty( $group['inline_help'] ) ) : ?>
                                        <details class="pico-setting-details">
                                            <summary><?php esc_html_e( 'Setup and usage', 'picostrap5' ); ?></summary>
                                            <div><?php echo wp_kses_post( $definition['description'] ); ?></div>
                                        </details>
                                    <?php endif; ?>
                                </div>
                                <input class="pico-switch" type="checkbox" role="switch" id="<?php echo esc_attr( $key ); ?>" name="picostrap_extras[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( (bool) get_theme_mod( $key, false ) ); ?> aria-describedby="<?php echo esc_attr( $key . '-hint' . ( 'picostrap_inline_css' === $key ? ' pico-inline-css-status' : '' ) ); ?>">
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>
                <div class="pico-savebar"><div><strong id="pico-save-state" role="status"><?php esc_html_e( 'All changes saved', 'picostrap5' ); ?></strong><span><?php esc_html_e( 'Changes apply after saving.', 'picostrap5' ); ?></span></div><button class="pico-button pico-primary" type="submit" id="pico-save"><?php esc_html_e( 'Save changes', 'picostrap5' ); ?></button></div>
            </form>
        </section>

        <section class="pico-tabpanel" id="pico-design" aria-labelledby="pico-design-heading">
            <div class="pico-page-heading"><h2 id="pico-design-heading"><?php esc_html_e( 'Design & CSS compilation', 'picostrap5' ); ?></h2><p><?php esc_html_e( 'Set colors, typography and Bootstrap variables in the Customizer. PicoSASS compiles your settings and Sass into the theme stylesheet.', 'picostrap5' ); ?></p></div>
            <?php picostrap_options_design_actions( $compile_url ); ?>
            <?php if ( picostrap_wordpress_fonts_available() ) : ?>
                <p class="pico-help"><?php esc_html_e( 'Prefer locally hosted fonts? Install or upload fonts in WordPress, enable their variants, then select them for body text or headings in Customizer > Typography.', 'picostrap5' ); ?> <a href="<?php echo esc_url( admin_url( 'font-library.php' ) ); ?>"><?php esc_html_e( 'Manage WordPress fonts', 'picostrap5' ); ?></a></p>
            <?php endif; ?>
            <div class="pico-build-info"><dl class="pico-file-sizes"><div><dt><?php esc_html_e( 'Saved bundle', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Saved bundle', 'picostrap5' ), __( 'Default bundle means the theme’s shipped stylesheet is selected. Custom build means a compiler save has been recorded for this active theme. This status does not detect edits made to Sass since the last compilation.', 'picostrap5' ) ); ?></dt><dd><?php echo esc_html( $bundle_exists ? ( $has_custom_bundle ? __( 'Custom build', 'picostrap5' ) : __( 'Default bundle', 'picostrap5' ) ) : __( 'Missing — recompile Sass', 'picostrap5' ) ); ?></dd></div><div><dt><?php esc_html_e( 'Output file', 'picostrap5' ); ?><?php picostrap_options_help( __( 'Output file', 'picostrap5' ), __( 'Path relative to the active theme directory. PicoSASS saves the compiled stylesheet here; on multisite the filename may include the current site ID. Editing Sass does not update this file until you recompile.', 'picostrap5' ) ); ?></dt><dd><code><?php echo esc_html( $bundle_relative ); ?></code><?php if ( $bundle_exists ) : ?> · <a href="<?php echo esc_url( picostrap_get_css_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View CSS', 'picostrap5' ); ?></a><?php endif; ?></dd></div></dl><p class="pico-help"><?php esc_html_e( 'This describes the saved file. Recompile after changing Sass source files.', 'picostrap5' ); ?></p></div>
            <div class="pico-sass-grid">
                <section class="pico-sass-section" aria-labelledby="pico-custom-title">
                    <div class="pico-sass-art" aria-hidden="true">
                        <svg viewBox="0 0 360 132" fill="none" focusable="false">
                            <rect class="pico-art-surface" x="47" y="12" width="254" height="108" rx="8"/>
                            <path class="pico-art-line" d="M47 37h254"/>
                            <circle class="pico-art-dot" cx="61" cy="25" r="2.5"/><circle class="pico-art-dot" cx="72" cy="25" r="2.5"/><circle class="pico-art-dot" cx="83" cy="25" r="2.5"/>
                            <text class="pico-art-label" x="104" y="29">_custom.scss</text>
                            <path class="pico-art-accent" d="m82 61-12 12 12 12m33-24 12 12-12 12m-16-28-7 32"/>
                            <path class="pico-art-line" d="M151 61h91m-91 14h64m-64 14h78"/>
                            <path class="pico-art-secondary" d="M70 105h30m10 0h57"/>
                            <rect class="pico-art-surface" x="272" y="64" width="42" height="42" rx="7"/>
                            <path class="pico-art-accent" d="m284 93 3-9 13-13 6 6-13 13-9 3Zm13-19 6 6"/>
                        </svg>
                    </div>
                    <h2 id="pico-custom-title"><?php esc_html_e( 'Where to add your custom styles', 'picostrap5' ); ?></h2>
                    <p><?php esc_html_e( 'Use the Customizer for visual settings. When you need your own CSS or Sass, edit the files in an active child theme.', 'picostrap5' ); ?></p>
                    <?php if ( $child_active ) : ?>
                        <p class="pico-doc-state"><?php esc_html_e( 'Child theme active:', 'picostrap5' ); ?> <code><?php echo esc_html( get_stylesheet() ); ?></code></p>
                    <?php else : ?>
                        <p class="pico-doc-state"><?php esc_html_e( 'The parent theme is active. Install and activate a child theme before adding project code so parent updates can preserve your work.', 'picostrap5' ); ?></p>
                        <?php picostrap_options_child_theme_button(); ?>
                    <?php endif; ?>
                    <dl class="pico-file-guide">
                        <div><dt><code>sass/_custom.scss</code></dt><dd><?php esc_html_e( 'Your own CSS rules and Sass. Imported after the framework and theme styles.', 'picostrap5' ); ?></dd></div>
                        <div><dt><code>sass/_theme_variables.scss</code></dt><dd><?php esc_html_e( 'Bootstrap variable overrides. Imported before Bootstrap generates its styles.', 'picostrap5' ); ?></dd></div>
                    </dl>
                    <p class="pico-doc-next"><strong><?php esc_html_e( 'After editing:', 'picostrap5' ); ?></strong> <?php esc_html_e( 'save the files, click Recompile CSS above, then check the site and clear any page or CDN cache.', 'picostrap5' ); ?></p>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'Add CSS or Sass: a small example', 'picostrap5' ); ?></summary>
                        <div class="pico-doc-content">
                            <p><?php esc_html_e( 'Regular CSS works in a .scss file too. Add project selectors to your child theme’s sass/_custom.scss. It is already part of the default build; you do not need to import Bootstrap again.', 'picostrap5' ); ?></p>
                            <pre tabindex="0" aria-label="<?php esc_attr_e( 'Example custom Sass rules', 'picostrap5' ); ?>"><code>.project-intro {
  padding-block: 3rem;

  @include media-breakpoint-up(lg) {
    padding-block: 6rem;
  }
}</code></pre>
                            <p><?php esc_html_e( 'This example adds more vertical space on large screens. Bootstrap mixins and variables are available because the framework has already been imported.', 'picostrap5' ); ?></p>
                        </div>
                    </details>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'Set variables and choose Customizer precedence', 'picostrap5' ); ?></summary>
                        <div class="pico-doc-content">
                            <p><?php esc_html_e( 'Put variable declarations in sass/_theme_variables.scss. A declaration with !default fills in a value only if it has not already been set. This lets a previously declared Customizer value take priority.', 'picostrap5' ); ?></p>
                            <pre tabindex="0" aria-label="<?php esc_attr_e( 'Example Sass defaults', 'picostrap5' ); ?>"><code>$primary: #176b87 !default;
$border-radius: .375rem !default;</code></pre>
                            <p><?php esc_html_e( 'To deliberately enforce a value in code, assign it without !default. That assignment can override the Customizer value declared earlier, so later changes in the Customizer may no longer affect that variable.', 'picostrap5' ); ?></p>
                            <p><?php esc_html_e( 'NinjaBootstrap defaults load before this file. Variables it already defines, such as $spacers, will also be preserved by !default. Replacing those values requires an explicit assignment or a carefully merged map.', 'picostrap5' ); ?></p>
                            <p><?php esc_html_e( 'Changing a Bootstrap variable in _custom.scss is too late to regenerate components built earlier. Keep component variables in _theme_variables.scss and your selectors in _custom.scss.', 'picostrap5' ); ?></p>
                        </div>
                    </details>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'Keep your changes safe during updates', 'picostrap5' ); ?></summary>
                        <div class="pico-doc-content">
                            <p><?php esc_html_e( 'Work inside the active child theme, not the Picostrap parent folder. Installing a child theme alone does not activate it; use the activation action in WordPress Themes when you are ready.', 'picostrap5' ); ?></p>
                            <p><?php esc_html_e( 'Keep project edits in the child theme’s Sass and template files. Parent updates replace parent files. The starter installer preserves an existing child-theme installation instead of overwriting it.', 'picostrap5' ); ?></p>
                            <p><?php esc_html_e( 'The bundle in css-output is generated output. Direct edits there can be lost on the next compilation; make lasting changes in the source files and rebuild.', 'picostrap5' ); ?></p>
                        </div>
                    </details>
                </section>

                <section class="pico-sass-section" aria-labelledby="pico-pipeline-title">
                    <div class="pico-sass-art" aria-hidden="true">
                        <svg viewBox="0 0 360 132" fill="none" focusable="false">
                            <path class="pico-art-line" d="M88 59h62m60 0h62"/>
                            <path class="pico-art-accent" d="m137 54 6 5-6 5m122-10 6 5-6 5"/>
                            <rect class="pico-art-surface" x="28" y="29" width="60" height="60" rx="8"/>
                            <path class="pico-art-accent" d="M43 48h30M43 69h30"/>
                            <circle class="pico-art-knob" cx="52" cy="48" r="4"/><circle class="pico-art-knob" cx="65" cy="69" r="4"/>
                            <rect class="pico-art-surface" x="150" y="29" width="60" height="60" rx="8"/>
                            <path class="pico-art-secondary" d="m180 43 19 10-19 10-19-10 19-10Zm-19 19 19 10 19-10m-38 9 19 10 19-10"/>
                            <path class="pico-art-surface" d="M279 29h31l15 15v45h-46V29Z"/>
                            <path class="pico-art-line" d="M310 29v15h15"/>
                            <text class="pico-art-output" x="287" y="69">CSS</text>
                            <text class="pico-art-label" x="58" y="112" text-anchor="middle"><?php esc_html_e( 'Settings', 'picostrap5' ); ?></text>
                            <text class="pico-art-label" x="180" y="112" text-anchor="middle">Sass</text>
                            <text class="pico-art-label" x="302" y="112" text-anchor="middle"><?php esc_html_e( 'Stylesheet', 'picostrap5' ); ?></text>
                        </svg>
                    </div>
                    <h2 id="pico-pipeline-title"><?php esc_html_e( 'How your changes become live CSS', 'picostrap5' ); ?></h2>
                    <p><?php esc_html_e( 'PicoSASS compiles in your browser. WordPress saves the result as the stylesheet your visitors load.', 'picostrap5' ); ?></p>
                    <ol class="pico-pipeline">
                        <li><strong><?php esc_html_e( 'Start with your settings', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'Customizer values become Sass variables, followed by the active theme’s main.scss.', 'picostrap5' ); ?></p></li>
                        <li><strong><?php esc_html_e( 'Build the framework and your styles', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'Bootstrap, NinjaBootstrap and the theme styles are compiled together with your custom Sass.', 'picostrap5' ); ?></p></li>
                        <li><strong><?php esc_html_e( 'Save the stylesheet', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'An authenticated request writes the bundle into the active theme and updates its CSS version.', 'picostrap5' ); ?></p></li>
                    </ol>
                    <p class="pico-doc-next"><strong><?php esc_html_e( 'Visitors use saved CSS.', 'picostrap5' ); ?></strong> <?php esc_html_e( 'They do not run the compiler. File edits need a rebuild before they appear on the site.', 'picostrap5' ); ?></p>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'When to Publish, Recompile or compile continuously', 'picostrap5' ); ?></summary>
                        <div class="pico-doc-content">
                            <dl class="pico-file-guide">
                                <div><dt><?php esc_html_e( 'Changing settings in the Customizer', 'picostrap5' ); ?></dt><dd><?php esc_html_e( 'Use the live preview, then Publish to save the settings and send the preview CSS to WordPress.', 'picostrap5' ); ?></dd></div>
                                <div><dt><?php esc_html_e( 'Editing Sass files', 'picostrap5' ); ?></dt><dd><?php esc_html_e( 'Save your files and use Recompile CSS above. This opens the frontend compiler and builds from saved settings. It does not publish pending Customizer changes.', 'picostrap5' ); ?></dd></div>
                                <div><dt><?php esc_html_e( 'Working through several Sass edits', 'picostrap5' ); ?></dt><dd><?php esc_html_e( 'Choose Recompile Continuously in the WordPress toolbar. Keep the compiler page open while you edit; it schedules another build after compilation. Use Stop Compiler when you finish.', 'picostrap5' ); ?></dd></div>
                            </dl>
                            <p><?php esc_html_e( 'All compilation modes need an administrator session, JavaScript and network access to the browser compiler’s dependencies.', 'picostrap5' ); ?></p>
                        </div>
                    </details>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'Import order and child-theme file lookup', 'picostrap5' ); ?></summary>
                        <div class="pico-doc-content">
                            <p><?php esc_html_e( 'The parent theme’s default pipeline follows this order. A child theme can provide its own main.scss or _bootstrap-loader.scss and change the imports.', 'picostrap5' ); ?></p>
                            <ol class="pico-doc-order">
                                <li><?php esc_html_e( 'Customizer variable declarations, then main.scss.', 'picostrap5' ); ?></li>
                                <li><?php esc_html_e( 'Bootstrap functions → NinjaBootstrap defaults → _theme_variables.scss.', 'picostrap5' ); ?></li>
                                <li><?php esc_html_e( 'Bootstrap variables, maps, mixins and components → NinjaBootstrap utility extensions → utility class generation.', 'picostrap5' ); ?></li>
                                <li><code>_wp_basic_styles.scss → _picostrap.scss → _woocommerce.scss → _custom.scss</code></li>
                            </ol>
                            <p><?php esc_html_e( 'The compiler first requests Sass files from the active theme’s sass directory. With a child theme active, an unsuccessful request is retried at the corresponding path in the parent theme. You can override individual files without copying every framework source.', 'picostrap5' ); ?></p>
                            <p><?php esc_html_e( 'The bundle combines framework, theme and custom Sass styles. Other plugin stylesheets remain separate. The output path above shows the file used by this active theme; multisite builds can include the site ID in the filename.', 'picostrap5' ); ?></p>
                        </div>
                    </details>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'Inspect this site’s compiler input and main.scss', 'picostrap5' ); ?></summary>
                        <div class="pico-doc-content">
                            <h3><?php esc_html_e( 'Generated entry source', 'picostrap5' ); ?></h3>
                            <p><?php esc_html_e( 'Read-only output of ps_get_main_sass(): saved SCSSvar_ theme settings become variable declarations before main.scss is imported. The ps_main_sass filter can change this input. Unpublished Customizer edits are not shown here.', 'picostrap5' ); ?></p>
                            <pre tabindex="0" aria-label="<?php esc_attr_e( 'Compiler entry Sass source', 'picostrap5' ); ?>"><code><?php echo esc_html( $sass_source ); ?></code></pre>
                            <h3><?php esc_html_e( 'Resolved main.scss', 'picostrap5' ); ?></h3>
                            <p><?php esc_html_e( 'Shows the child theme’s sass/main.scss when present, otherwise the parent theme’s file. This is source code, not the compiled stylesheet.', 'picostrap5' ); ?></p>
                            <?php if ( false !== $main_source ) : ?>
                                <pre tabindex="0" aria-label="<?php esc_attr_e( 'Main Sass source', 'picostrap5' ); ?>"><code><?php echo esc_html( $main_source ); ?></code></pre>
                            <?php else : ?>
                                <p><?php esc_html_e( 'The main.scss source could not be read. Check that sass/main.scss exists and is readable in the theme.', 'picostrap5' ); ?></p>
                            <?php endif; ?>
                        </div>
                    </details>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'A change is missing or compilation fails', 'picostrap5' ); ?></summary>
                        <div class="pico-doc-content">
                            <ul class="pico-doc-checks">
                                <li><?php esc_html_e( 'Check the active theme. Editing an inactive child theme does not change the current site.', 'picostrap5' ); ?></li>
                                <li><?php esc_html_e( 'Confirm that your file is imported. _custom.scss is included by default; a new Sass file needs an import in the build.', 'picostrap5' ); ?></li>
                                <li><?php esc_html_e( 'If a variable change has no effect, check import order and !default precedence before adding more overrides.', 'picostrap5' ); ?></li>
                                <li><?php esc_html_e( 'Read the compiler feedback and browser console. A failed source request or blocked compiler dependency can prevent compilation.', 'picostrap5' ); ?></li>
                                <li><?php esc_html_e( 'If preview styles appear but do not persist, inspect the save request in the browser’s Network panel and check that WordPress can write to the active theme’s CSS output directory.', 'picostrap5' ); ?></li>
                                <li><?php esc_html_e( 'After a successful save, clear page and CDN caches, reload the public page and confirm that the theme stylesheet is enabled.', 'picostrap5' ); ?></li>
                            </ul>
                        </div>
                    </details>
                </section>
            </div>

            <details class="pico-details"><summary><?php esc_html_e( 'What powers your design system', 'picostrap5' ); ?></summary><div class="pico-frameworks"><h3>NinjaBootstrap</h3><p><?php esc_html_e( 'Configures and extends Bootstrap with responsive spacing, positioning, sizing, letter spacing and color shades.', 'picostrap5' ); ?></p><a href="https://bootstrap.ninja/ninjabootstrap/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'NinjaBootstrap documentation', 'picostrap5' ); ?></a><h3>Bootstrap 5</h3><p><?php esc_html_e( 'Responsive grids, components, utilities and Sass variables, combined with Picostrap and your custom styles.', 'picostrap5' ); ?></p><a href="https://getbootstrap.com/docs/5.3/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Bootstrap documentation', 'picostrap5' ); ?></a> · <a href="https://getbootstrap.com/docs/5.3/customize/sass/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Sass guide', 'picostrap5' ); ?></a></div></details>
        </section>

        <section class="pico-tabpanel" id="pico-tools" aria-labelledby="pico-tools-heading">
            <div class="pico-page-heading"><h2 id="pico-tools-heading"><?php esc_html_e( 'Exports & updates', 'picostrap5' ); ?></h2><p><?php esc_html_e( 'Export your configuration, check stable releases, and find the documentation when you need it.', 'picostrap5' ); ?></p></div>
            <?php if ( current_user_can( 'manage_options' ) ) : ?><section class="pico-tool-section"><h3><?php esc_html_e( 'Export your setup', 'picostrap5' ); ?></h3><p><?php esc_html_e( 'Settings and menus only. Theme files and site content are not included.', 'picostrap5' ); ?></p><div class="pico-export-row"><div><h4><?php esc_html_e( 'Customizer settings', 'picostrap5' ); ?></h4><code>theme-options.json</code></div><a class="pico-button" href="<?php echo esc_url( admin_url( '?pico_download_theme_options=1' ) ); ?>"><?php esc_html_e( 'Export settings', 'picostrap5' ); ?></a></div><div class="pico-export-row"><div><h4><?php esc_html_e( 'Navigation menus', 'picostrap5' ); ?></h4><code>menus.json</code></div><a class="pico-button" href="<?php echo esc_url( admin_url( '?pico_download_menus=1' ) ); ?>"><?php esc_html_e( 'Export menus', 'picostrap5' ); ?></a></div></section><?php endif; ?>
            <section class="pico-tool-section"><h3><?php esc_html_e( 'Stable releases. Native updates.', 'picostrap5' ); ?></h3><p><?php printf( esc_html__( 'You’re running Picostrap %s. Theme updates use stable GitHub Releases through the standard WordPress updater.', 'picostrap5' ), esc_html( pico_get_parent_theme_version() ) ); ?></p><div class="pico-actions"><?php if ( current_user_can( 'update_themes' ) ) : ?><a class="pico-button" href="<?php echo esc_url( self_admin_url( 'update-core.php?force-check=1' ) ); ?>"><?php esc_html_e( 'Check for updates', 'picostrap5' ); ?></a><?php endif; ?><a href="https://github.com/livecanvas-team/picostrap5/releases" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Release notes', 'picostrap5' ); ?></a></div></section>
            <section class="pico-tool-section"><h3><?php esc_html_e( 'Build with a clear head.', 'picostrap5' ); ?></h3><p><?php esc_html_e( 'Child themes, customization, compilation and more: the Picostrap docs cover the complete workflow.', 'picostrap5' ); ?></p><a class="pico-button" href="https://picostrap.com/docs/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open documentation', 'picostrap5' ); ?></a></section>
        </section>
        <?php picostrap_options_resources_panel(); ?>
        <?php if ( $show_livecanvas ) picostrap_options_livecanvas_panel(); ?>
        <footer class="pico-footer"><span><?php esc_html_e( 'Less overhead. More room to build.', 'picostrap5' ); ?></span><a href="https://picostrap.com" target="_blank" rel="noopener noreferrer">picostrap.com <span aria-hidden="true">↗</span></a></footer>
    </div>
    <?php
}

/** Native dashboard news: visible only to people who can configure the theme. */
add_action( 'wp_dashboard_setup', function () {
    if ( current_user_can( 'edit_theme_options' ) ) {
        wp_add_dashboard_widget( 'picostrap_theme_options_news', __( 'Picostrap theme options', 'picostrap5' ), 'picostrap_dashboard_options_news' );
    }
} );

function picostrap_dashboard_options_news() {
    ?>
    <p><?php esc_html_e( 'Congratulations on building your site with Picostrap: a solid foundation designed to help you make WordPress faster, something your visitors and search engines can appreciate.', 'picostrap5' ); ?></p>
    <h3><?php esc_html_e( 'New: your theme options workspace', 'picostrap5' ); ?></h3>
    <p><?php esc_html_e( 'Explore what the theme does, tune its optimizations, inspect asset sizes and compression, and manage your Sass workflow in one clear panel.', 'picostrap5' ); ?></p>
    <h3><?php esc_html_e( 'Try experimental page-specific CSS', 'picostrap5' ); ?></h3>
    <p><?php esc_html_e( 'Preview replacing the full theme stylesheet request with only the CSS retained for a page, inlined in its HTML. A runtime guard can restore the full bundle for dynamic content. Plugin stylesheets remain separate: this does not remove all CSS from your site.', 'picostrap5' ); ?></p>
    <p><?php esc_html_e( 'This feature is experimental and does not guarantee a better PageSpeed score. Compare results with it on and off. If you use it, check that your site looks and works correctly on desktop and mobile, including dynamic interactions. It is opt-in and does not change your settings automatically.', 'picostrap5' ); ?></p>
    <p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'themes.php?page=picostrap-theme-options#pico-overview' ) ); ?>"><?php esc_html_e( 'Explore Picostrap options', 'picostrap5' ); ?></a> <a class="button" href="<?php echo esc_url( admin_url( 'themes.php?page=picostrap-theme-options#pico-extras-title' ) ); ?>"><?php esc_html_e( 'View optimizations', 'picostrap5' ); ?></a></p>
    <?php
}
