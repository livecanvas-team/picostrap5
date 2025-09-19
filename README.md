# Picostrap 5 - Bootstrap 5 WordPress Starter Theme

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPL%20v2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)



<p align="center">
  <img src="https://picostrap.com/wp-content/uploads/2025/03/picostrap5-logo.svg" alt="Picostrap 5 Logo" width="300">
</p>

**The fastest Bootstrap 5 WordPress starter theme. Experience the power of SASS merged with the WordPress Customizer.**

Picostrap 5 leverages the flexibility of Bootstrap 5 (currently v5.3+) and integrates it deeply with the WordPress Customizer. Customize Bootstrap's look and feel directly from your WordPress backend, and Picostrap will recompile the necessary CSS on the fly using a built-in SASS compiler that runs right in your browser.

**[Watch the introduction video](https://youtu.be/nrHFh_08w78) to get started!**
**[Visit the Picostrap website](https://picostrap.com)**

## Key Features

*   **Bootstrap 5.3+ Integration:** Built on the latest stable version of the world's most popular front-end framework.
*   **Turbocharged by NinjaBootstrap** Bootstrap Configuration & Additional Utility Classes. This helps you make the most out of Bootstrap, enabling responsive variations of utility classes you DO need, eg position-lg-absolute - check out https://bootstrap.ninja/ninjabootstrap/ 
*   **Live SASS Compilation:** Modify Bootstrap SCSS variables (colors, fonts, spacing, components, etc.) directly through the WordPress Customizer. Picostrap uses **PicoSASS** (an implementation of Dart SASS running in the browser via JSPM) to recompile your CSS instantly upon hitting "Publish" or via a manual trigger in the admin bar.
*   **Single CSS Bundle:** Generates a single, minified `css-output/bundle.css` file for optimal performance.
*   **Extensive Customizer Options:**
    *   Control Bootstrap SCSS variables.
    *   **AI Palette Generator:** Generate color palettes using AI (powered by Huemint API).
    *   **Live Style Guide Preview:** See your changes reflected in a comprehensive style guide directly within the Customizer preview.
    *   **Font Picker:** Choose Google Fonts easily with a built-in font picker.
    *   Layout options (Navbar position, expansion, etc.).
    *   Toggle various theme features on/off.
*   **Opt-in Features:** Keep your site lean by enabling only the features you need:
    *   Disable Gutenberg Editor (and its CSS)
    *   Disable Block-based Widgets Editor
    *   Disable WordPress Comments
    *   Disable XML-RPC
    *   Add a "Back to Top" button
    *   Enable GLightbox for image lightboxes
    *   Enable Bootstrap Tooltips & Popovers
    *   Enable Page Scroll Detection (adds body classes `scroll-position-at-top` / `scroll-position-not-at-top`)
    *   Open Main Menu on Hover (for non-touch devices)
*   **Performance Focused:** Includes head cleanup (`clean-head.php`) to remove unnecessary WordPress meta tags, emoji scripts, etc.
*   **Dark Mode Switch:** Optional Bootstrap 5-compatible dark mode toggle.
*   **WooCommerce Ready:** Includes basic support and template overrides for WooCommerce integration.
*   **LiveCanvas & WindPress Friendly:** Designed to work well with the LiveCanvas page builder and WindPress Tailwind CSS integration plugins.
*   **Developer Friendly:** Clean codebase, standard WordPress practices, starter theme philosophy. Uses a Bootstrap 5 Navwalker.
*   **Translation Ready:** Includes language files (`.pot`, `.po`) for easy translation.

## Getting Started

1.  **Installation:** Install like any other WordPress theme. Download the `.zip` file, **RENAME** it to "**picostrap5.zip**" and upload it via the WordPress admin panel (`Appearance > Themes > Add New`) or upload the extracted folder via FTP to `/wp-content/themes/`.
2.  **Child Theme (Recommended):** For customization beyond the Customizer, it's highly recommended to use a child theme. You can download a starter child theme from the [Picostrap website](https://picostrap.com/#downloads).
3.  **Customize:** Navigate to `Appearance > Customize` in your WordPress admin panel. Explore the various sections (Colors, Typography, Global Options, Buttons, Forms, Navbars, etc.) to tailor the theme to your needs.
4.  **Compile SASS:**
    *   **Via Customizer:** Make your changes and click "Publish". The SCSS will automatically recompile in your browser, and the new CSS bundle will be saved via AJAX.
    *   **Via Admin Bar:** While viewing the frontend (as an administrator), click the "SASS" icon in the admin bar and choose "Recompile Once" or "Recompile Continuously".

## How SASS Compilation Works

1.  **Customizer Changes:** When you modify SCSS variables in the Customizer, the values are saved as theme mods.
2.  **PicoSASS Trigger:** When you publish changes or use the admin bar trigger, the `picosass.js` script is loaded.
3.  **SCSS Generation:** JavaScript reads the SCSS theme mods, constructs the SASS code (variables + `@import 'main';`), and passes it to the Dart SASS compiler running in the browser.
4.  **Compilation:** PicoSASS fetches necessary Bootstrap SCSS files (from the theme folder, potentially falling back to the parent theme if using a child theme) and compiles the final CSS.
5.  **Live Preview:** The compiled CSS is injected into the Customizer preview iframe (or the main page if using the admin bar trigger) via a `<style id="picosass-injected-style">` tag.
6.  **Saving:** After compilation (and Customizer save, or manual trigger completion), the compiled CSS is sent via AJAX to a WordPress action (`picostrap_save_css_bundle`) which saves it to `/wp-content/themes/(your-theme)/css-output/bundle.css`. The theme mod `css_bundle_version_number` is incremented to ensure cache busting.

*Note: SASS compilation requires administrator privileges and happens in the administrator's browser.*

## Theme Structure

### Core Directories
*   **`/sass/`** - SCSS source files and Bootstrap 5 sources
*   **`/css-output/`** - Compiled CSS bundle (`bundle.css`)
*   **`/inc/`** - PHP functionality and customizer assets
*   **`/page-templates/`** - Specialized page templates
*   **`/loops/`** - Content display loop templates
*   **`/partials/`** - Reusable template parts
*   **`/languages/`** - Translation files (8 languages included)
*   **`/woocommerce/`** - WooCommerce template overrides

### Available Page Templates
*   **Blank Templates:** `blank.php`, `blank-nofooter.php`, `empty.php`
*   **Sidebar Layouts:** `page-sidebar-left.php`, `page-sidebar-right.php`
*   **Special Pages:** `bootstrap-demo.php`, `default-no-jumbotron.php`

### Loop Templates
*   **Cards Layout:** `loops/cards.php` - Bootstrap card-based post display
*   **Accordion Layout:** `loops/accordion.php` - Collapsible content display
*   **Standard Loop:** `loops/index.php` - Default post loop

### Widget Areas
*   **Main Sidebar** - Primary sidebar area
*   **Footer Full** - Full-width footer with dynamic grid
*   **WooCommerce Sidebar** - Shop sidebar (when WooCommerce is active)

### Menu Locations
*   **Primary Menu** - Main navigation
*   **Secondary Menu** - Secondary navigation area

## Customization

*   **WordPress Customizer:** The primary way to customize colors, typography, and theme options.
*   **Child Theme SCSS:** For more advanced CSS or structural changes, create a child theme and edit the `sass/_custom.scss` file. Remember to recompile using the admin bar link after saving your changes.

## Opt-in Features

Many features can be toggled on or off under `Customize > Global Utilities`. This allows you to load only the code you need. Features include:

*   Disable Gutenberg Editor & CSS
*   Disable Block-based Widgets Editor
*   Disable WordPress Comments entirely
*   Disable XML-RPC for security
*   Add a "Back to Top" button
*   Enable GLightbox JS library for image lightboxes
*   Enable Bootstrap Tooltips & Popovers initialization script
*   Enable Page Scroll Detection script
*   Open Main Menu on Hover script

## Performance & Security Features

### Performance Optimizations
*   **Single CSS Bundle:** All styles compile to one minified file
*   **Head Cleanup:** Removes unnecessary WordPress meta tags and scripts
*   **Emoji Removal:** Completely disables WordPress emoji scripts and CSS
*   **REST API Optimization:** Removes REST API links for non-logged-in users
*   **Selective Loading:** Opt-in features prevent loading unused code

### Security Enhancements
*   **Login Security:** Obscures login error messages
*   **Self-Pingback Prevention:** Disables self-referential pingbacks
*   **XML-RPC Control:** Option to disable XML-RPC entirely
*   **oEmbed Management:** Smart embed handling while preserving video functionality

## Developer Features

### Custom Functions
*   **`picostrap_site_info()`** - Customizable footer text
*   **`picostrap_the_sharing_buttons()`** - Social sharing functionality
*   **Modified excerpt length** - 22 words with custom "Read More" styling

### WordPress Features Support
*   **Post Thumbnails:** Full featured image support
*   **Post Formats:** Aside, image, video, quote, link
*   **Custom Logo:** WordPress custom logo feature
*   **HTML5 Markup:** Modern markup for forms, galleries, captions
*   **Responsive Embeds:** Automatic responsive video embedding
*   **Selective Refresh:** Customizer widget live preview

### Bootstrap Integration
*   **Bootstrap 5 Navwalker:** v1.3.4 with custom patches for dropdown support
*   **NinjaBootstrap:** Additional utility classes and responsive variations
*   **Dark Mode:** Bootstrap 5-compatible dark mode toggle

## Translation Support

Included translations for 8 languages:
*   German (de_DE), Spanish (es_ES), Estonian (et), French (fr_FR)
*   Italian (it_IT), Dutch (nl_NL), Portuguese (pt_PT), Romanian (ro_RO)
*   Base POT file included for creating new translations

## Compatibility

*   **WooCommerce:** Theme support and basic template adjustments included.
*   **LiveCanvas:** Tested and marked as friendly (`lc_theme_is_livecanvas_friendly`). Respects LC's header/footer handling options.
*   **WindPress:** Includes specific support for integrating with the WindPress plugin for Tailwind CSS scanning (`inc/windpress-support.php`).
*   **Multisite:** Full WordPress multisite compatibility with separate CSS bundles per site.

## Troubleshooting

### Common Issues
*   **SASS Not Compiling:** Ensure you have administrator privileges and JavaScript is enabled
*   **Changes Not Showing:** Clear browser cache and check if CSS bundle version incremented
*   **Performance Issues:** Use opt-in features to disable unused functionality
*   **Child Theme SASS:** Remember to recompile using admin bar after editing child theme SASS files

### Debug Mode
*   Enable WordPress debug mode to see compilation errors
*   Check browser console for JavaScript errors during SASS compilation
*   Verify file permissions for `/css-output/` directory write access

## Advanced Configuration

### Custom SCSS Variables
Child themes can override Bootstrap variables in `sass/_custom.scss`:
```scss
$primary: #your-color;
$font-family-base: 'Your Font', sans-serif;
```

### Customizer Export/Import
*   Theme includes JSON export functionality for theme customizations
*   Useful for transferring settings between sites

### Hooks and Filters
The theme provides various WordPress hooks for developers:
*   Custom template tags in `/inc/template-tags.php`
*   Filter hooks for content modification
*   Action hooks for theme setup and enhancements

## License

Picostrap 5 is licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
