<?php
defined('ABSPATH') || exit;

// ADD CUSTOM JS & CSS TO CUSTOMIZER
add_action('customize_controls_enqueue_scripts', 'picostrap_customize_enqueue');
function picostrap_customize_enqueue() {
    $rand = rand(0, 1000);
    wp_enqueue_script('custom-customize', get_template_directory_uri() . '/inc/customizer-assets/customizer.js', ['jquery', 'customize-controls'], $rand, true);
    wp_localize_script('custom-customize', 'picostrap_ajax_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('picostrap_save_css_bundle'),
    ]);
    wp_enqueue_script('custom-customize-lib', get_template_directory_uri() . '/inc/customizer-assets/customizer-constants.js', ['jquery', 'customize-controls'], $rand, true);
    wp_enqueue_style('custom-customize', get_template_directory_uri() . '/inc/customizer-assets/customizer.css', [], $rand);
    wp_enqueue_script('palette-generator', get_template_directory_uri() . '/inc/customizer-assets/palette-generator.js', ['jquery', 'customize-controls'], $rand, true);
    wp_enqueue_style('palette-generator', get_template_directory_uri() . '/inc/customizer-assets/palette-generator.css', [], $rand);
    wp_enqueue_script('style-guide', get_template_directory_uri() . '/inc/customizer-assets/style-guide.js', ['jquery'], $rand, true);
    wp_enqueue_script('fontpicker', get_template_directory_uri() . '/inc/customizer-assets/font-picker-web-component/font-picker.js', ['customize-controls'], $rand, true);
}

// ADD BODY CLASSES
add_filter('body_class', 'picostrap_config_body_classes');
function picostrap_config_body_classes($classes) {
    if (function_exists('lc_custom_header')) return $classes;
    $classes[] = "picostrap_header_navbar_position_" . get_theme_mod('picostrap_header_navbar_position');
    $classes[] = "picostrap_header_navbar_color_choice_" . get_theme_mod('picostrap_header_navbar_color_choice');
    if (get_theme_mod("enable_topbar")) $classes[] = "picostrap_topbar_enabled";
    return $classes;
}

// REMOVE BODY MARGIN-TOP GIVEN BY WORDPRESS ADMIN BAR
add_action('get_header', 'picostrap_filter_head');
function picostrap_filter_head() {
    if (get_theme_mod('picostrap_header_navbar_position') == "fixed-top") remove_action('wp_head', '_admin_bar_bump_cb');
}

// ADD CUSTOMIZER SETTINGS AND CONTROLS
add_action('customize_register', 'picostrap_customize_register');
function picostrap_customize_register($wp_customize) {
    add_theme_support('customize-selective-refresh-widgets');

    // Sections
    $sections = [
        "typography" => __("Typography", 'picostrap5'),
        "components" => __("Global Options", 'picostrap5'),
        "buttons-forms" => __("Forms", 'picostrap5'),
        "buttons" => __("Buttons", 'picostrap5'),
        "navbars" => __("Navbars", 'picostrap5')
    ];

    foreach ($sections as $slug => $title) {
        $wp_customize->add_section($slug, ["title" => $title, "priority" => 50]);
    }

    // Add SCSS variables controls
    foreach (picostrap_get_scss_variables_array() as $section_slug => $section_data) {
        foreach ($section_data as $variable_name => $variable_props) {
            $variable_slug = str_replace("$", "SCSSvar_", $variable_name);
            $variable_pretty_name = ucwords(str_replace("-", ' ', str_replace("$", "", $variable_name)));
            $default = $variable_props['default'] ?? "";
            $optional_grouptitle = isset($variable_props['newgroup']) ? " <span hidden class='cs-option-group-title'>" . $variable_props['newgroup'] . "</span> " : "";
            $optional_comment = isset($variable_props['comment']) ? " <span class='cs-optional-comment'>" . $variable_props['comment'] . "</span> " : "";

            $wp_customize->add_setting($variable_slug, ["default" => $default, "transport" => "postMessage"]);

            switch ($variable_props['type']) {
                case 'color':
                    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $variable_slug, [
                        'label' => __($variable_pretty_name, 'picostrap5'),
                        'description' => $optional_grouptitle . " (<span class='variable-name'>" . $variable_name . "</span>) " . $optional_comment,
                        'section' => $section_slug,
                    ]));
                    break;
                case 'boolean':
                    $wp_customize->add_control($variable_slug, [
                        'label' => __($variable_pretty_name, 'picostrap5'),
                        'description' => $optional_grouptitle . " (<span class='variable-name'>" . $variable_name . "</span>) " . $optional_comment,
                        'section' => $section_slug,
                        'type' => 'checkbox'
                    ]);
                    break;
                case 'text':
                    $placeholder_html = isset($variable_props['placeholder']) ? "<b>Default:</b> " . $variable_props['placeholder'] : "";
                    $placeholder = isset($variable_props['placeholder']) ? $variable_props['placeholder'] : '';
                    $wp_customize->add_control($variable_slug, [
                        'label' => __($variable_pretty_name, 'picostrap5'),
                        'description' => $optional_grouptitle . " <!-- (" . $variable_name . ") -->" . $placeholder . " " . $optional_comment,
                        'section' => $section_slug,
                        'type' => 'text',
                        'input_attrs' => ['title' => esc_attr($variable_name)]
                    ]);
                    break;
            }
        }
    }

    // Additional settings and controls
    $wp_customize->add_section("nav", ["title" => __("Main Navigation Bar", 'picostrap5'), "priority" => 60]);
    $wp_customize->add_section("topbar", ["title" => __("Optional Topbar", 'picostrap5'), "priority" => 60]);
    $wp_customize->add_section("footer", ["title" => __("Footer", 'picostrap5'), "priority" => 100]);
    $wp_customize->add_section("singleposts", ["title" => __("Single Post & Archives", 'picostrap5'), "priority" => 160]);
    $wp_customize->add_section("addcode", ["title" => __("Header / Footer Code", 'picostrap5'), "priority" => 180]);
    $wp_customize->add_section("extras", ["title" => __("Global Utilities", 'picostrap5'), "priority" => 190]);

    // Sample setting and control
    $wp_customize->add_setting("sample_setting", ["default" => "", "transport" => "postMessage"]);
    $wp_customize->add_control("sample_setting", ["label" => __("Sample Setting", 'picostrap5'), "section" => "extras", "type" => "text"]);

    // ADD SETTINGS AND CONTROLS FOR OTHER SECTIONS
    // TAGLINE: SHOW / HIDE SWITCH
    $wp_customize->add_setting('header_disable_tagline', ['default' => '', 'transport' => 'postMessage']);
    $wp_customize->add_control('header_disable_tagline', ['label' => __('Hide Tagline', 'picostrap5'), 'section' => 'title_tagline', 'type' => 'checkbox']);

    // NAVBAR SECTION
    if (function_exists('lc_custom_header')) {
        $wp_customize->add_section("nav", ["title" => __("Main Navigation Bar [Disabled]", 'picostrap5'), "priority" => 60]);
        $wp_customize->add_setting("picostrap_header_navbar_disabled", ["default" => "", "transport" => "refresh"]);
        $wp_customize->add_control("picostrap_header_navbar_disabled", ['label' => __('You have enabled the LiveCanvas option to handle header, so these options are disabled.', 'picostrap5'), 'section' => 'nav']);
    } else {
        $wp_customize->add_section("nav", ["title" => __("Main Navigation Bar", 'picostrap5'), "priority" => 60]);
        $wp_customize->add_setting("picostrap_header_navbar_expand", ["default" => "navbar-expand-md", "transport" => "refresh"]);
        $wp_customize->add_control("picostrap_header_navbar_expand", [
            'label' => __('Navbar Expansion', 'picostrap5'),
            'section' => 'nav',
            'type' => 'radio',
            'description' => __('Navbar is Collapsed on mobile, and expands to a full blown menubar on chosen breakpoint', 'picostrap5'),
            'choices' => [
                'navbar-expand-none' => 'Never expand, keep always collapsed',
                'navbar-expand-sm' => 'Expand on SM and upper',
                'navbar-expand-md' => 'Expand on MD and upper',
                'navbar-expand-lg' => 'Expand on LG and upper',
                'navbar-expand-xl' => 'Expand on XL and upper',
                'navbar-expand-xxl' => 'Expand on XXL and upper',
            ]
        ]);
        $wp_customize->add_setting("picostrap_header_navbar_position", ["default" => "", "transport" => "refresh"]);
        $wp_customize->add_control("picostrap_header_navbar_position", [
            'label' => __('Navbar Position', 'picostrap5'),
            'section' => 'nav',
            'type' => 'radio',
            'choices' => [
                '' => 'Standard Static Top',
                'fixed-top' => 'Fixed on Top',
                'fixed-bottom' => 'Fixed on Bottom',
                'd-none' => 'No Navbar',
            ]
        ]);
        $wp_customize->add_setting("enable_detect_page_scroll", ["default" => "", "transport" => "refresh"]);
        $wp_customize->add_control("enable_detect_page_scroll", [
            "label" => __("Enable Page Scrolling Detection", 'picostrap5'),
            "description" => __("Publish and exit the Customizer to see the effect. Adds a scroll-position-at-top / scroll-position-not-at-top class to the BODY element according to scroll position. Customize via CSS. Use with Navbar Position set to Fixed for best results.", 'picostrap5'),
            "section" => "nav",
            'type' => 'checkbox',
        ]);
        $wp_customize->add_setting("picostrap_header_navbar_color_choice", ['default' => 'bg-dark', "transport" => "refresh"]);
        $wp_customize->add_control("picostrap_header_navbar_color_choice", [
            'label' => __('Navbar Background Color', 'picostrap5'),
            'section' => 'nav',
            'type' => 'select',
            'choices' => [
                'bg-primary' => 'Primary',
                'bg-primary-subtle' => 'Primary Subtle',
                'bg-secondary' => 'Secondary',
                'bg-secondary-subtle' => 'Secondary Subtle',
                'bg-success' => 'Success',
                'bg-success-subtle' => 'Success Subtle',
                'bg-info' => 'Info',
                'bg-info-subtle' => 'Info Subtle',
                'bg-warning' => 'Warning',
                'bg-warning-subtle' => 'Warning Subtle',
                'bg-danger' => 'Danger',
                'bg-danger-subtle' => 'Danger Subtle',
                'bg-light' => 'Light',
                'bg-light-subtle' => 'Light Subtle',
                'bg-dark' => 'Dark',
                'bg-dark-subtle' => 'Dark Subtle',
                'bg-transparent' => 'Transparent'
            ]
        ]);
        $wp_customize->add_setting("picostrap_header_navbar_color_scheme_attr", ['default' => 'dark', "transport" => "refresh"]);
        $wp_customize->add_control("picostrap_header_navbar_color_scheme_attr", [
            'label' => __('Color Scheme (Menubar links)', 'picostrap5'),
            'section' => 'nav',
            'type' => 'radio',
            'choices' => [
                '' => 'None (Body color links)',
                'light' => 'Light (Dark links)',
                'dark' => 'Dark (Light links)',
            ]
        ]);
        $wp_customize->add_setting("enable_search_form", ["default" => "", "transport" => "refresh"]);
        $wp_customize->add_control("enable_search_form", ["label" => __("Enable Search Form", 'picostrap5'), "section" => "nav", 'type' => 'checkbox']);
        $wp_customize->add_setting("enable_dark_mode_switch", ["default" => "", "transport" => "refresh"]);
        $wp_customize->add_control("enable_dark_mode_switch", ["label" => __("Enable Dark Mode Switch", 'picostrap5'), "section" => "nav", 'type' => 'checkbox']);
    }

    // TOPBAR SECTION
    $wp_customize->add_section("topbar", ["title" => __("Optional Topbar", 'picostrap5'), "priority" => 60]);
    $wp_customize->add_setting("enable_topbar", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("enable_topbar", ["label" => __("Enable Topbar", 'picostrap5'), "description" => __("Requires Navbar position set to 'Standard static top'", 'picostrap5'), "section" => "topbar", 'type' => 'checkbox']);
    $wp_customize->add_setting("topbar_content", ["default" => "", "transport" => "postMessage"]);
    $wp_customize->add_control("topbar_content", ["label" => __("Topbar Text / HTML", 'picostrap5'), "section" => "topbar", 'type' => 'textarea']);
    $wp_customize->add_setting("topbar_bg_color_choice", ['default' => 'bg-light', "transport" => "refresh"]);
    $wp_customize->add_control("topbar_bg_color_choice", ['label' => __('Topbar Background Color', 'picostrap5'), 'section' => 'topbar', 'type' => 'radio', 'choices' => [
        'bg-primary' => 'Primary',
        'bg-secondary' => 'Secondary',
        'bg-success' => 'Success',
        'bg-info' => 'Info',
        'bg-warning' => 'Warning',
        'bg-danger' => 'Danger',
        'bg-light' => 'Light',
        'bg-dark' => 'Dark',
        'bg-transparent' => 'Transparent'
    ]]);
    $wp_customize->add_setting("topbar_text_color_choice", ['default' => 'text-dark', "transport" => "refresh"]);
    $wp_customize->add_control("topbar_text_color_choice", ['label' => __('Topbar Text Color', 'picostrap5'), 'section' => 'topbar', 'type' => 'radio', 'choices' => [
        'text-primary' => 'Primary',
        'text-secondary' => 'Secondary',
        'text-success' => 'Success',
        'text-info' => 'Info',
        'text-warning' => 'Warning',
        'text-danger' => 'Danger',
        'text-light' => 'Light',
        'text-dark' => 'Dark',
    ]]);

    // FOOTER SECTION
    if (function_exists('lc_custom_footer')) {
        $wp_customize->add_section("footer", ["title" => __("Footer [Disabled]", 'picostrap5'), "priority" => 100]);
        $wp_customize->add_setting("picostrap_footer_disabled", ["default" => "", "transport" => "refresh"]);
        $wp_customize->add_control("picostrap_footer_disabled", ['label' => __('You have enabled the LiveCanvas option to handle footer, so these options are disabled.', 'picostrap5'), 'section' => 'footer']);
    } else {
        $wp_customize->add_section("footer", ["title" => __("Footer", 'picostrap5'), "priority" => 100]);
        $wp_customize->add_setting("picostrap_footer_text", ["default" => "", "transport" => "postMessage"]);
        $wp_customize->add_control("picostrap_footer_text", ["label" => __("Footer Text", 'picostrap5'), "description" => "THIS SIMPLE FIELD can contain HTML and is displayed into the 'colophon', the very bottom of the site. <br><br>TO BUILD A MORE COMPLEX FOOTER, USE THE WIDGETED AREA. <br>To enable it, populate it from the backend's <a target='_blank' href='" . admin_url('widgets.php') . "'>Widgets page</a>", "section" => "footer", 'type' => 'textarea']);
    }

    // SINGLE POST & ARCHIVES SECTION
    $wp_customize->add_section("singleposts", ["title" => __("Single Post & Archives", 'picostrap5'), "priority" => 160]);
    $wp_customize->add_setting("singlepost_disable_entry_cats", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("singlepost_disable_entry_cats", ["label" => __("Hide Categories", 'picostrap5'), "section" => "singleposts", 'type' => 'checkbox']);
    $wp_customize->add_setting("singlepost_disable_author", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("singlepost_disable_author", ["label" => __("Hide Post Author", 'picostrap5'), "section" => "singleposts", 'type' => 'checkbox']);
    $wp_customize->add_setting("singlepost_disable_date", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("singlepost_disable_date", ["label" => __("Hide Post Date", 'picostrap5'), "section" => "singleposts", 'type' => 'checkbox']);
    $wp_customize->add_setting("enable_sharing_buttons", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("enable_sharing_buttons", ["label" => __("Enable Sharing Buttons after the Content", 'picostrap5'), "description" => __("Pure HTML only, SVG inline icons, zero bloat", 'picostrap5'), "section" => "singleposts", 'type' => 'checkbox']);

    // HEADER & FOOTER CODE SECTION
    $wp_customize->add_section("addcode", ["title" => __("Header / Footer Code", 'picostrap5'), "priority" => 180]);
    $wp_customize->add_setting("picostrap_header_code", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("picostrap_header_code", ["label" => __("Add code to Header", 'picostrap5'), "section" => "addcode", 'type' => 'textarea', 'description' => 'Will be added to the &lt;HEAD&gt; of all site pages']);
    $wp_customize->add_setting("picostrap_footer_code", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("picostrap_footer_code", ["label" => __("Add code to Footer", 'picostrap5'), "section" => "addcode", 'type' => 'textarea', 'description' => 'Will be added before closing the &lt;BODY&gt; of all site pages']);
    $wp_customize->add_setting("picostrap_fonts_header_code", ["default" => "", "transport" => "postMessage"]);
    $wp_customize->add_control("picostrap_fonts_header_code", ["label" => __("Font Loading Header code", 'picostrap5'), "section" => "addcode", 'type' => 'textarea', 'description' => __('The code in the field below is generated each time you set a new font family for body or headings, and is served in the site\'s &lt;head&gt;. <br><br> You can customize this code, but please mind that if you edit font family settings your customizations will be lost. <br><br> In case you break things up while editing, you can manually regenerate the code <a href="#" id="regenerate-font-loading-code">clicking here</a>. <br><br> Using the JSDelivr CDN is known to be GDPR compliant.')]);
    $wp_customize->add_setting("picostrap_fonts_header_code_disable", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("picostrap_fonts_header_code_disable", ["label" => __("Disable the Font Loading in Header", 'picostrap5'), "description" => __("<b>Keep this unchecked, unless you really want. </b>").__("Prevents the code of the textarea above from being served in the site's &lt;head&gt;."), "section" => "addcode", 'type' => 'checkbox']);
    $wp_customize->add_setting("body_font_object", ["default" => "", "transport" => "postMessage"]);
    $wp_customize->add_control("body_font_object", ["label" => __("body_font_object", 'picostrap5'), "section" => "addcode", 'type' => 'textarea', 'description' => '<b>Not editable</b> - Internal purpose only.']);
    $wp_customize->add_setting("headings_font_object", ["default" => "", "transport" => "postMessage"]);
    $wp_customize->add_control("headings_font_object", ["label" => __("headings_font_object", 'picostrap5'), "section" => "addcode", 'type' => 'textarea', 'description' => '<b>Not editable</b> - Internal purpose only.']);

    // GLOBAL UTILITIES SECTION
    $wp_customize->add_section("extras", ["title" => __("Global Utilities", 'picostrap5'), "priority" => 190]);
    $wp_customize->add_setting("disable_gutenberg", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("disable_gutenberg", ["label" => __("Disable the Gutenberg Content Editor", 'picostrap5'), "description" => __("Disables the Gutenberg content editor on all post types. De-enqueues its CSS styles as well.", 'picostrap5'), "section" => "extras", 'type' => 'checkbox']);
    $wp_customize->add_setting("disable_widgets_block_editor", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("disable_widgets_block_editor", ["label" => __("Disable the Block-based Widgets Editor", 'picostrap5'), "description" => __("Disables the Block-based Widgets Editor and restores the classic widgets editor.", 'picostrap5'), "section" => "extras", 'type' => 'checkbox']);
    $wp_customize->add_setting("singlepost_disable_comments", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("singlepost_disable_comments", ["label" => __("Disable WordPress Comments", 'picostrap5'), "description" => __("Will completely disable the entire WP comments feature.", 'picostrap5'), "section" => "extras", 'type' => 'checkbox']);
    $wp_customize->add_setting("disable_xml_rpc", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("disable_xml_rpc", ["label" => __("Disable XML - RPC", 'picostrap5'), "description" => __("Disabling XML-RPC will close one more door that a potential hacker may try to exploit to hack your website.", 'picostrap5'), "section" => "extras", 'type' => 'checkbox']);
    $wp_customize->add_setting("enable_back_to_top", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("enable_back_to_top", ["label" => __("Add a 'Back to Top' button to site", 'picostrap5'), "description" => __("Very light implementation. To see the button, you will also need to Publish, exit the Customizer, and scroll down a long page", 'picostrap5'), "section" => "extras", 'type' => 'checkbox']);
    $wp_customize->add_setting("enable_lightbox", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("enable_lightbox", ["label" => __("Enable Lightbox", 'picostrap5'), "description" => __("Will lazily add one JS and one CSS file from cdn.jsdelivr.net before closing the BODY of the page, to let you use <a target='_blank' href='https://github.com/biati-digital/glightbox'>gLightBox</a>: a very lightweight lightbox implementation. <br><br>The lightbox will be triggered on single posts and pages, automatically adding the <b>glightbox</b> class to each IMG in an A element.<br><br>To add the lightbox to other elements arbitrarily, add the <b>glightbox</b> class.<br><br> To prevent the lightbox on a linked image, add the <b>nolightbox</b> class.<br><br>To lightbox all images inside a DIV, add the <b>autolightbox</b> class.", 'picostrap5'), "section" => "extras", 'type' => 'checkbox']);
    $wp_customize->add_setting("enable_tooltips", ["default" => "", "transport" => "refresh"]);
    $wp_customize->add_control("enable_tooltips", ["label" => __("Enable Tooltips & Popovers", 'picostrap5'), "description" => __("Adds inline <a target='_blank' href='https://getbootstrap.com/docs/5.2/components/tooltips/#enable-tooltips'>two rows of JavaScript</a> to enable Boostrap 5 tooltips and popovers. Publish and exit the Customizer to see the change.", 'picostrap5'), "section" => "extras", 'type' => 'checkbox']);
}

// GET SCSS VARIABLES ARRAY
function picostrap_get_scss_variables_array() {
    return [
        "colors" => [
            '$body-bg' => ['type' => 'color', 'newgroup' => 'Base Colors'],
            '$body-color' => ['type' => 'color'],
            '$link-color' => ['type' => 'color'],
            '$link-hover-color' => ['type' => 'color'],
            '$primary' => ['type' => 'color', 'newgroup' => 'Bootstrap Colors'],
            '$secondary' => ['type' => 'color'],
            '$success' => ['type' => 'color'],
            '$info' => ['type' => 'color'],
            '$warning' => ['type' => 'color'],
            '$danger' => ['type' => 'color'],
            '$light' => ['type' => 'color'],
            '$dark' => ['type' => 'color'],
            '$enable-text-shades' => ['type' => 'boolean', 'default' => 'true', 'newgroup' => 'Color Shades', 'comment' => 'Generates text shade classes'],
            '$enable-bg-shades' => ['type' => 'boolean', 'default' => 'true', 'comment' => 'Generates background shade classes'],
            '$enable-text-bg-shades' => ['type' => 'boolean', 'comment' => 'Generates text & background combination shade classes'],
        ],
        "components" => [
            '$enable-rounded' => ['type' => 'boolean', 'default' => 'true'],
            '$enable-shadows' => ['type' => 'boolean'],
            '$enable-gradients' => ['type' => 'boolean'],
            '$spacer' => ['type' => 'text', 'placeholder' => '1rem'],
            '$border-width' => ['newgroup' => 'Global Borders', 'type' => 'text', 'placeholder' => '1px'],
            '$border-style' => ['type' => 'text', 'placeholder' => 'solid'],
            '$border-color' => ['type' => 'color'],
            '$border-radius' => ['type' => 'text', 'placeholder' => '.375rem'],
            '$border-radius-sm' => ['newgroup' => 'Rounded Helper Classes', 'type' => 'text', 'placeholder' => '.25rem'],
            '$border-radius-lg' => ['type' => 'text', 'placeholder' => '.5rem'],
            '$border-radius-xl' => ['type' => 'text', 'placeholder' => '1rem'],
            '$border-radius-2xl' => ['type' => 'text', 'placeholder' => '2rem'],
            '$border-radius-pill' => ['type' => 'text', 'placeholder' => '50rem'],
        ],
        "typography" => [
            '$font-family-base' => ['type' => 'text', 'placeholder' => '$font-family-sans-serif', 'newgroup' => 'Font Families'],
            '$font-family-sans-serif' => ['type' => 'text'],
            '$font-family-monospace' => ['type' => 'text'],
            '$font-size-base' => ['newgroup' => 'Font Sizes', 'type' => 'text', 'placeholder' => '1rem'],
            '$font-size-sm' => ['type' => 'text', 'placeholder' => '.875rem'],
            '$font-size-lg' => ['type' => 'text', 'placeholder' => '1.25rem'],
            '$enable-rfs' => ['type' => 'boolean', 'default' => 'true'],
            '$font-weight-base' => ['newgroup' => 'Font Weights', 'type' => 'text', 'placeholder' => '400'],
            '$line-height-base' => ['type' => 'text', 'placeholder' => '1.5'],
            '$font-weight-lighter' => ['type' => 'text', 'placeholder' => 'lighter'],
            '$font-weight-light' => ['type' => 'text', 'placeholder' => '300'],
            '$font-weight-normal' => ['type' => 'text', 'placeholder' => '400'],
            '$font-weight-semibold' => ['type' => 'text', 'placeholder' => '600'],
            '$font-weight-bold' => ['type' => 'text', 'placeholder' => '700'],
            '$font-weight-bolder' => ['type' => 'text', 'placeholder' => 'bolder'],
            '$headings-font-family' => ['type' => 'text', 'placeholder' => 'null', 'newgroup' => 'Headings'],
            '$headings-font-weight' => ['type' => 'text', 'placeholder' => '500'],
            '$headings-line-height' => ['type' => 'text', 'placeholder' => '1.2'],
            '$headings-color' => ['type' => 'color'],
            '$headings-margin-bottom' => ['type' => 'text', 'placeholder' => '$spacer / 2'],
            '$h1-font-size' => ['type' => 'text', 'placeholder' => '2.5rem'],
            '$h2-font-size' => ['type' => 'text', 'placeholder' => '2rem'],
            '$h3-font-size' => ['type' => 'text', 'placeholder' => '1.75rem'],
            '$h4-font-size' => ['type' => 'text', 'placeholder' => '1.5rem'],
            '$h5-font-size' => ['type' => 'text', 'placeholder' => '1.25rem'],
            '$h6-font-size' => ['type' => 'text', 'placeholder' => '1rem'],
            '$lead-font-size' => ['newgroup' => 'Lead, Small and Muted', 'type' => 'text', 'placeholder' => '1.25rem'],
            '$lead-font-weight' => ['type' => 'text', 'placeholder' => '300'],
            '$small-font-size' => ['type' => 'text', 'placeholder' => '80%'],
            '$text-muted' => ['type' => 'color'],
            '$blockquote-margin-y' => ['newgroup' => 'Blockquotes', 'type' => 'text', 'placeholder' => '$spacer'],
            '$blockquote-font-size' => ['type' => 'text', 'placeholder' => '1.25rem'],
            '$blockquote-footer-color' => ['type' => 'color'],
            '$blockquote-footer-font-size' => ['type' => 'text', 'placeholder' => '$small-font-size'],
            '$hr-height' => ['newgroup' => 'HRs', 'type' => 'text', 'placeholder' => '$border-width'],
            '$hr-color' => ['type' => 'color'],
            '$mark-padding' => ['newgroup' => 'Miscellanea', 'type' => 'text', 'placeholder' => '.2em'],
            '$dt-font-weight' => ['type' => 'text', 'placeholder' => '700'],
            '$nested-kbd-font-weight' => ['type' => 'text', 'placeholder' => '700'],
            '$list-inline-padding' => ['type' => 'text', 'placeholder' => '.5rem'],
            '$mark-bg' => ['type' => 'color', 'placeholder' => '#fcf8e3'],
            '$hr-margin-y' => ['type' => 'text', 'placeholder' => '$spacer'],
            '$paragraph-margin-bottom' => ['type' => 'text', 'placeholder' => '1rem'],
        ],
        "buttons-forms" => [
            '$input-btn-padding-y' => ['type' => 'text', 'placeholder' => '.375rem'],
            '$input-btn-padding-x' => ['type' => 'text', 'placeholder' => '.75rem'],
            '$input-btn-font-family' => ['type' => 'text', 'placeholder' => 'null'],
            '$input-btn-font-size' => ['type' => 'text', 'placeholder' => '$font-size-base'],
            '$input-btn-line-height' => ['type' => 'text', 'placeholder' => '$line-height-base'],
            '$input-btn-focus-width' => ['type' => 'text', 'placeholder' => '.2rem'],
            '$input-btn-focus-color-opacity' => ['type' => 'text', 'placeholder' => '.25'],
            '$input-btn-focus-color' => ['type' => 'color', 'placeholder' => 'rgba($component-active-bg, .25)'],
            '$input-btn-focus-blur' => ['type' => 'text', 'placeholder' => '0'],
            '$input-btn-focus-box-shadow' => ['type' => 'text', 'placeholder' => '0 0 0 $input-btn-focus-width $input-btn-focus-color'],
            '$input-btn-padding-y-sm' => ['type' => 'text', 'placeholder' => '.25rem'],
            '$input-btn-padding-x-sm' => ['type' => 'text', 'placeholder' => '.5rem'],
            '$input-btn-font-size-sm' => ['type' => 'text', 'placeholder' => '$font-size-sm'],
            '$input-btn-padding-y-lg' => ['type' => 'text', 'placeholder' => '.5rem'],
            '$input-btn-padding-x-lg' => ['type' => 'text', 'placeholder' => '1rem'],
            '$input-btn-font-size-lg' => ['type' => 'text', 'placeholder' => '$font-size-lg'],
            '$input-btn-border-width' => ['type' => 'text', 'placeholder' => '$border-width'],
        ],
        "buttons" => [
            '$btn-padding-y' => ['type' => 'text', 'placeholder' => '.375rem'],
            '$btn-padding-x' => ['type' => 'text', 'placeholder' => '.75rem'],
            '$btn-font-family' => ['type' => 'text', 'placeholder' => 'null'],
            '$btn-font-size' => ['type' => 'text', 'placeholder' => '$font-size-base'],
            '$btn-line-height' => ['type' => 'text', 'placeholder' => '$line-height-base'],
            '$btn-white-space' => ['type' => 'text', 'placeholder' => 'null (Set to `nowrap` to prevent text wrapping)'],
            '$btn-padding-y-sm' => ['type' => 'text', 'placeholder' => '.25rem'],
            '$btn-padding-x-sm' => ['type' => 'text', 'placeholder' => '.5rem'],
            '$btn-font-size-sm' => ['type' => 'text', 'placeholder' => '$font-size-sm'],
            '$btn-padding-y-lg' => ['type' => 'text', 'placeholder' => '.5rem'],
            '$btn-padding-x-lg' => ['type' => 'text', 'placeholder' => '1rem'],
            '$btn-font-size-lg' => ['type' => 'text', 'placeholder' => '$font-size-lg'],
            '$btn-border-width' => ['type' => 'text', 'placeholder' => '$border-width'],
            '$btn-font-weight' => ['type' => 'text', 'placeholder' => '$font-weight-normal !default'],
            '$btn-box-shadow' => ['type' => 'text', 'placeholder' => 'inset 0 1px 0 rgba($white, .15), 0 1px 1px rgba($black, .075) !default'],
            '$btn-focus-width' => ['type' => 'text', 'placeholder' => '$input-btn-focus-width !default'],
            '$btn-focus-box-shadow' => ['type' => 'text', 'placeholder' => '$input-btn-focus-box-shadow !default'],
            '$btn-disabled-opacity' => ['type' => 'text', 'placeholder' => '.65 !default'],
            '$btn-active-box-shadow' => ['type' => 'text', 'placeholder' => 'inset 0 3px 5px rgba($black, .125) !default'],
            '$btn-link-color' => ['type' => 'text', 'placeholder' => '$link-color !default', 'newgroup' => 'Button Colors'],
            '$btn-link-hover-color' => ['type' => 'text', 'placeholder' => '$link-hover-color !default'],
            '$btn-link-disabled-color' => ['type' => 'text', 'placeholder' => '$gray-600 !default'],
            '$btn-border-radius' => ['type' => 'text', 'placeholder' => '$border-radius !default', 'newgroup' => 'Buttons Border Radius'],
            '$btn-border-radius-sm' => ['type' => 'text', 'placeholder' => '$border-radius-sm !default'],
            '$btn-border-radius-lg' => ['type' => 'text', 'placeholder' => '$border-radius-lg !default'],
            '$btn-transition' => ['newgroup' => 'Buttons Extras', 'type' => 'text', 'placeholder' => 'color .15s ease-in-out, background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out !default'],
            '$btn-hover-bg-shade-amount' => ['type' => 'text', 'placeholder' => '15% !default'],
            '$btn-hover-bg-tint-amount' => ['type' => 'text', 'placeholder' => '15% !default'],
            '$btn-hover-border-shade-amount' => ['type' => 'text', 'placeholder' => '20% !default'],
            '$btn-hover-border-tint-amount' => ['type' => 'text', 'placeholder' => '10% !default'],
            '$btn-active-bg-shade-amount' => ['type' => 'text', 'placeholder' => '20% !default'],
            '$btn-active-bg-tint-amount' => ['type' => 'text', 'placeholder' => '20% !default'],
            '$btn-active-border-shade-amount' => ['type' => 'text', 'placeholder' => '25% !default'],
            '$btn-active-border-tint-amount' => ['type' => 'text', 'placeholder' => '10% !default'],
        ],
        "navbars" => [
            '$navbar-brand-font-size' => ['type' => 'text', 'placeholder' => '1.25rem', 'newgroup' => 'Brand'],
            '$navbar-light-brand-color' => ['type' => 'color'],
            '$navbar-dark-brand-color' => ['type' => 'color'],
            '$nav-link-font-size' => ['type' => 'text', 'placeholder' => '1rem', 'newgroup' => 'Nav Links'],
            '$navbar-light-color' => ['type' => 'color'],
            '$navbar-light-hover-color' => ['type' => 'color'],
            '$navbar-dark-color' => ['type' => 'color'],
            '$navbar-dark-hover-color' => ['type' => 'color'],
            '$navbar-toggler-font-size' => ['type' => 'text', 'placeholder' => '1rem', 'newgroup' => 'Toggler']
        ],
    ];
}

function picostrap_sanitize_checkbox($input) {
    return ((isset($input) && true == $input) ? true : false);
}

// ENABLE SELECTIVE REFRESH PARTIALS
add_action('customize_register', 'picostrap_register_main_partials');
function picostrap_register_main_partials($wp_customize) {
    if (!isset($wp_customize->selective_refresh)) { return; }
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport = 'postMessage';

    $partials = [
        'header_site_title' => ['selector' => 'a.navbar-brand', 'settings' => ['blogname'], 'render_callback' => fn() => get_bloginfo('name', 'display')],
        'header_site_desc' => ['selector' => '#top-description', 'settings' => ['blogdescription'], 'render_callback' => fn() => get_bloginfo('description', 'display')],
        'header_disable_tagline' => ['selector' => '#top-description', 'settings' => ['header_disable_tagline'], 'render_callback' => fn() => !get_theme_mod('header_disable_tagline') ? get_bloginfo('description', 'display') : ""],
        'header_menu_left' => ['selector' => '#navbar .menuwrap-left', 'settings' => ['nav_menu_locations[navbar-left]']],
        'topbar_html_content' => ['selector' => '#topbar-content', 'settings' => ['topbar_content'], 'render_callback' => fn() => get_theme_mod('topbar_content')],
        'footer_ending_text' => ['selector' => 'footer.site-footer', 'settings' => ['picostrap_footer_text'], 'render_callback' => 'picostrap_site_info'],
        'singlepost_entry_footer' => ['selector' => '.entry-categories', 'settings' => ['singlepost_disable_entry_cats'], 'render_callback' => '__return_false'],
        'singlepost_date' => ['selector' => '.post-date', 'settings' => ['singlepost_disable_date'], 'render_callback' => '__return_false'],
        'singlepost_author' => ['selector' => '.post-author', 'settings' => ['singlepost_disable_author'], 'render_callback' => '__return_false'],
        'enable_sharing_buttons' => ['selector' => '.picostrap-sharing-buttons', 'settings' => ['enable_sharing_buttons'], 'render_callback' => '__return_false'],
        'enable_detect_page_scroll' => ['selector' => 'body', 'settings' => ['enable_detect_page_scroll'], 'render_callback' => '__return_false']
    ];

    foreach ($partials as $id => $args) {
        $wp_customize->selective_refresh->add_partial($id, $args);
    }
}

// CUSTOM BACKGROUND SIZING OPTIONS
add_action('customize_register', 'custom_background_size');
function custom_background_size($wp_customize) {
    $wp_customize->add_setting('background-image-size', ['default' => 'cover']);
    $wp_customize->add_control('background-image-size', [
        'label' => __('Background Image Size', 'picostrap5'),
        'section' => 'background_image',
        'priority' => 200,
        'type' => 'radio',
        'choices' => [
            'cover' => __('Cover', 'picostrap5'),
            'contain' => __('Contain', 'picostrap5'),
            'inherit' => __('Inherit', 'picostrap5')
        ]
    ]);
}

add_action('wp_head', 'custom_background_size_css', 999);
function custom_background_size_css() {
    if (!get_theme_mod('background_image')) return;
    $background_size = get_theme_mod('background-image-size', 'inherit');
    echo '<style> body.custom-background { background-size: ' . $background_size . '; } </style>';
}

add_action('customize_register', 'picostrap_theme_customize_register_extras');
function picostrap_theme_customize_register_extras($wp_customize) {
    $sections = [
        "typography" => __("Typography", 'picostrap5'),
        "components" => __("Global Options", 'picostrap5'),
        "buttons-forms" => __("Forms", 'picostrap5'),
        "buttons" => __("Buttons", 'picostrap5')
    ];

    foreach ($sections as $slug => $title) {
        $wp_customize->add_section($slug, ["title" => $title, "priority" => 50]);
    }

    foreach (picostrap_get_scss_variables_array() as $section_slug => $section_data) {
        foreach ($section_data as $variable_name => $variable_props) {
            $variable_slug = str_replace("$", "SCSSvar_", $variable_name);
            $variable_pretty_format_name = ucwords(str_replace("-", ' ', str_replace("$", "", $variable_name)));
            $variable_type = $variable_props['type'];
            $default = $variable_props['default'] ?? "";
            $optional_grouptitle = isset($variable_props['newgroup']) ? " <span hidden class='cs-option-group-title'>" . $variable_props['newgroup'] . "</span> " : "";
            $optional_comment = isset($variable_props['comment']) ? " <span class='cs-optional-comment'>" . $variable_props['comment'] . "</span> " : "";

            $wp_customize->add_setting($variable_slug, ["default" => $default, "transport" => "postMessage"]);

            switch ($variable_type) {
                case 'color':
                    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $variable_slug, [
                        'label' => __($variable_pretty_format_name, 'picostrap5'),
                        'description' => $optional_grouptitle . " (<span class='variable-name'>" . $variable_name . "</span>) " . $optional_comment,
                        'section' => $section_slug,
                    ]));
                    break;
                case 'boolean':
                    $wp_customize->add_control($variable_slug, [
                        'label' => __($variable_pretty_format_name, 'picostrap5'),
                        'description' => $optional_grouptitle . " (<span class='variable-name'>" . $variable_name . "</span>) " . $optional_comment,
                        'section' => $section_slug,
                        'type' => 'checkbox'
                    ]);
                    break;
                case 'text':
                    $placeholder_html = isset($variable_props['placeholder']) ? "<b>Default:</b> " . $variable_props['placeholder'] : "";
                    $wp_customize->add_control($variable_slug, [
                        'label' => __($variable_pretty_format_name, 'picostrap5'),
                        'description' => $optional_grouptitle . " <!-- (" . $variable_name . ") -->" . $placeholder_html . " " . $optional_comment,
                        'section' => $section_slug,
                        'type' => 'text',
                        'input_attrs' => ['title' => esc_attr($variable_name)]
                    ]);
                    break;
            }
        }
    }

    $wp_customize->add_setting('picostrap_header_chrome_color', ['default' => '', 'transport' => 'postMessage']);
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'picostrap_header_chrome_color', [
        'label' => __('Header Color in Android Chrome', 'picostrap5'),
        'section' => 'colors',
        'description' => "<span hidden class='cs-option-group-title'>Extra</span>",
        'type' => 'color'
    ]));

    $wp_customize->add_setting('header_disable_tagline', ['default' => '', 'transport' => 'postMessage']);
    $wp_customize->add_control('header_disable_tagline', ['label' => __('Hide Tagline', 'picostrap5'), 'section' => 'title_tagline', 'type' => 'checkbox']);
}

