<?php
/**
 * Add Theme Options Page
 *
 * @package picostrap5
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

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
    add_theme_page( 'Picostrap Theme Options Page', 'Picostrap Options', 'edit_theme_options', 'picostrap-theme-options', 'theme_option_page' );
}
add_action( 'admin_menu', 'add_picostrap_theme_page' );
 
// Load admin styles only on the theme options screen.
add_action( 'admin_enqueue_scripts', function ( $hook_suffix ) {
    if ( 'appearance_page_picostrap-theme-options' !== $hook_suffix ) {
        return;
    }
    wp_enqueue_style( 'picostrap-options', get_template_directory_uri() . '/inc/options-page.css', array(), pico_get_parent_theme_version() );
} );

function theme_option_page() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        return;
    }

    $child_active = is_child_theme();
    $compile_url = add_query_arg( array( 'compile_sass' => '1', 'sass_nocache' => '1' ), home_url( '/' ) );
    $has_custom_bundle = (bool) get_theme_mod( 'css_bundle_version_number', 0 );
    $bundle_name = $has_custom_bundle ? picostrap_get_complete_css_filename() : picostrap_get_base_css_filename();
    $bundle_relative = picostrap_get_css_optional_subfolder_name() . $bundle_name;
    $bundle_path = get_stylesheet_directory() . '/' . $bundle_relative;
    $bundle_exists = is_file( $bundle_path );
    $main_path = locate_template( 'sass/main.scss' );
    $main_source = $main_path && is_readable( $main_path ) ? file_get_contents( $main_path ) : false;
    $sass_source = ps_get_main_sass();
    ?>
    <div class="wrap pico-options">
        <header class="pico-header">
            <h1><?php esc_html_e( 'Picostrap theme options', 'picostrap5' ); ?></h1>
            <span class="pico-version">v<?php echo esc_html( pico_get_parent_theme_version() ); ?></span>
            <p class="pico-intro"><?php esc_html_e( 'Your design, from Sass to screen.', 'picostrap5' ); ?></p>
            <p><?php esc_html_e( 'Powered by NinjaBootstrap and Bootstrap 5. Shape your design in the Customizer, extend it in your child theme, and let PicoSASS build the CSS in your browser.', 'picostrap5' ); ?></p>
        </header>

        <div class="pico-layout">
            <main class="pico-workspace">
                <section class="pico-panel" aria-labelledby="pico-build-title">
                    <h2 id="pico-build-title"><?php esc_html_e( 'Make it yours', 'picostrap5' ); ?></h2>
                    <p><?php esc_html_e( 'Set project colors, typography and Bootstrap options in the WordPress Customizer. Preview your changes, then publish to save your settings and rebuild the theme CSS.', 'picostrap5' ); ?></p>
                    <div class="pico-actions">
                        <a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Open Customizer', 'picostrap5' ); ?></a>
                        <?php if ( current_user_can( 'administrator' ) ) : ?>
                            <a class="button button-secondary" href="<?php echo esc_url( $compile_url ); ?>"><?php esc_html_e( 'Recompile Sass', 'picostrap5' ); ?></a>
                        <?php endif; ?>
                    </div>
                    <p class="pico-help"><?php esc_html_e( 'Changed a Sass file? Recompile Sass opens your site and runs the compiler. You can also launch it from the WordPress toolbar. An internet connection is required to load the compiler.', 'picostrap5' ); ?></p>
                    <div class="pico-build-info">
                        <h3><?php esc_html_e( 'Current setup', 'picostrap5' ); ?></h3>
                        <dl>
                            <div><dt><?php esc_html_e( 'Active theme', 'picostrap5' ); ?></dt><dd><?php echo esc_html( wp_get_theme()->get( 'Name' ) ); ?> — <?php echo esc_html( $child_active ? __( 'child theme', 'picostrap5' ) : __( 'parent theme', 'picostrap5' ) ); ?></dd></div>
                            <div><dt><?php esc_html_e( 'CSS bundle', 'picostrap5' ); ?></dt><dd><?php echo esc_html( $bundle_exists ? ( $has_custom_bundle ? __( 'Custom build saved', 'picostrap5' ) : __( 'Default bundle', 'picostrap5' ) ) : __( 'Bundle missing — recompile Sass to generate it', 'picostrap5' ) ); ?></dd></div>
                            <div><dt><?php esc_html_e( 'Output file', 'picostrap5' ); ?></dt><dd><code><?php echo esc_html( $bundle_relative ); ?></code><?php if ( $bundle_exists ) : ?> · <a href="<?php echo esc_url( picostrap_get_css_url() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View CSS (new tab)', 'picostrap5' ); ?></a><?php endif; ?></dd></div>
                        </dl>
                        <p class="pico-help"><?php esc_html_e( 'This reports the saved bundle, not whether it includes your latest Sass edits. Recompile after editing source files.', 'picostrap5' ); ?></p>
                    </div>
                </section>

                <section class="pico-section" aria-labelledby="pico-custom-title">
                    <h2 id="pico-custom-title"><?php esc_html_e( 'Add your own Sass', 'picostrap5' ); ?></h2>
                    <?php if ( $child_active ) : ?>
                        <p><?php esc_html_e( 'Your child theme is active. Keep project changes in its Sass files so parent theme updates leave your work intact.', 'picostrap5' ); ?></p>
                    <?php else : ?>
                        <p><?php esc_html_e( 'Start by installing and activating a child theme to keep your custom code safe when Picostrap updates.', 'picostrap5' ); ?> <a href="https://picostrap.com/#downloads" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Get the child theme starter (new tab)', 'picostrap5' ); ?></a></p>
                    <?php endif; ?>
                    <dl class="pico-file-guide">
                        <div><dt><code>sass/_theme_variables.scss</code></dt><dd><?php esc_html_e( 'Override Sass defaults before Bootstrap is built. Use !default for values that should remain adjustable in the Customizer.', 'picostrap5' ); ?></dd></div>
                        <div><dt><code>sass/_custom.scss</code></dt><dd><?php esc_html_e( 'Add your own selectors and component styles. This file is imported last, after the framework and theme styles.', 'picostrap5' ); ?></dd></div>
                    </dl>
                    <p><?php esc_html_e( 'Save your files, recompile Sass, then check the result on your site.', 'picostrap5' ); ?></p>
                </section>

                <section class="pico-section" aria-labelledby="pico-pipeline-title">
                    <h2 id="pico-pipeline-title"><?php esc_html_e( 'Follow the Sass pipeline', 'picostrap5' ); ?></h2>
                    <p><?php esc_html_e( 'One build brings your settings, frameworks and custom styles together.', 'picostrap5' ); ?></p>
                    <ol class="pico-pipeline">
                        <li><strong><?php esc_html_e( 'Saved settings become Sass variables', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'PicoSASS starts with the Customizer values, then imports main.scss.', 'picostrap5' ); ?></p></li>
                        <li><strong><?php esc_html_e( 'NinjaBootstrap configures and extends Bootstrap', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'The Bootstrap loader imports functions, NinjaBootstrap defaults and theme variables, then Bootstrap components and the extended utility maps before generating utility classes.', 'picostrap5' ); ?></p></li>
                        <li><strong><?php esc_html_e( 'Theme styles and your code complete the build', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'WordPress, Picostrap and WooCommerce styles load next, followed by custom.scss. PicoSASS compiles the result into the theme CSS bundle.', 'picostrap5' ); ?></p></li>
                    </ol>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'Preview the compiler entry code', 'picostrap5' ); ?></summary>
                        <p><?php esc_html_e( 'Read-only output of ps_get_main_sass(), using saved settings and the ps_main_sass filter. Unpublished Customizer changes are not shown here.', 'picostrap5' ); ?></p>
                        <pre tabindex="0" aria-label="<?php esc_attr_e( 'Compiler entry Sass source', 'picostrap5' ); ?>"><code><?php echo esc_html( $sass_source ); ?></code></pre>
                    </details>
                    <details class="pico-details">
                        <summary><?php esc_html_e( 'Explore the main.scss source', 'picostrap5' ); ?></summary>
                        <p><?php esc_html_e( 'The active child theme file is shown when present; otherwise this is the parent theme source. Imports resolve from the child theme first, with a parent theme fallback.', 'picostrap5' ); ?></p>
                        <?php if ( false !== $main_source ) : ?>
                            <pre tabindex="0" aria-label="<?php esc_attr_e( 'Main Sass source', 'picostrap5' ); ?>"><code><?php echo esc_html( $main_source ); ?></code></pre>
                        <?php else : ?>
                            <p><?php esc_html_e( 'The main.scss source could not be read. Check that sass/main.scss exists and is readable in the theme.', 'picostrap5' ); ?></p>
                        <?php endif; ?>
                    </details>
                </section>

                <?php if ( current_user_can( 'manage_options' ) ) : ?>
                    <section class="pico-section" aria-labelledby="pico-export-title">
                        <h2 id="pico-export-title"><?php esc_html_e( 'Export your setup', 'picostrap5' ); ?></h2>
                        <p><?php esc_html_e( 'Download your saved configuration for reference or backup. These exports contain settings and menu data; they do not include theme files or site content.', 'picostrap5' ); ?></p>
                        <div class="pico-export-row"><div><h3><?php esc_html_e( 'Customizer settings', 'picostrap5' ); ?></h3><p><code>theme-options.json</code> · <?php esc_html_e( 'Saved theme modifications, including Sass settings.', 'picostrap5' ); ?></p></div><a class="button" href="<?php echo esc_url( admin_url( '?pico_download_theme_options=1' ) ); ?>"><?php esc_html_e( 'Export settings', 'picostrap5' ); ?></a></div>
                        <div class="pico-export-row"><div><h3><?php esc_html_e( 'Navigation menus', 'picostrap5' ); ?></h3><p><code>menus.json</code> · <?php esc_html_e( 'Menu names, items and their hierarchy.', 'picostrap5' ); ?></p></div><a class="button" href="<?php echo esc_url( admin_url( '?pico_download_menus=1' ) ); ?>"><?php esc_html_e( 'Export menus', 'picostrap5' ); ?></a></div>
                    </section>
                <?php endif; ?>
            </main>

            <aside class="pico-reference" aria-labelledby="pico-framework-title">
                <h2 id="pico-framework-title"><?php esc_html_e( 'Meet your frameworks', 'picostrap5' ); ?></h2>
                <p><?php esc_html_e( 'NinjaBootstrap is built into Picostrap. It configures and extends Bootstrap 5 through Sass, adding utilities to the same CSS build.', 'picostrap5' ); ?></p>
                <h3>NinjaBootstrap</h3>
                <p><?php esc_html_e( 'More room to design: extended spacing, responsive positioning and sizing, letter spacing, borders and color shades.', 'picostrap5' ); ?></p>
                <p><?php esc_html_e( 'For example, position-relative position-lg-absolute switches an element to absolute positioning on large screens.', 'picostrap5' ); ?></p>
                <a href="https://bootstrap.ninja/ninjabootstrap/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'NinjaBootstrap documentation (new tab)', 'picostrap5' ); ?></a>
                <h3>Bootstrap 5</h3>
                <p><?php esc_html_e( 'The foundation: responsive grids, components, utilities and Sass variables. Bootstrap documentation covers the standard classes and component behavior.', 'picostrap5' ); ?></p>
                <ul>
                    <li><a href="https://getbootstrap.com/docs/5.3/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Bootstrap documentation (new tab)', 'picostrap5' ); ?></a></li>
                    <li><a href="https://getbootstrap.com/docs/5.3/customize/sass/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Bootstrap Sass guide (new tab)', 'picostrap5' ); ?></a></li>
                </ul>
                <h3><?php esc_html_e( 'Go further with Picostrap', 'picostrap5' ); ?></h3>
                <p><?php esc_html_e( 'Explore theme setup, child themes and the customization workflow.', 'picostrap5' ); ?></p>
                <a href="https://picostrap.com/docs/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Picostrap documentation (new tab)', 'picostrap5' ); ?></a>
            </aside>
        </div>
    </div>
    <?php
}
