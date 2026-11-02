# Picostrap 5 — WordPress, Bootstrap and NinjaBootstrap

[![License: GPL v2 or later](https://img.shields.io/badge/License-GPL%20v2%20or%20later-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

<p align="center">
  <img src="inc/options-page/assets/picostrap-logo.svg" alt="Picostrap" width="300">
</p>

**A WordPress starter theme with Bootstrap, NinjaBootstrap and Sass compilation directly in your browser.**

Picostrap connects the WordPress Customizer to [Bootstrap 5](https://getbootstrap.com/docs/5.3/) and includes [NinjaBootstrap](https://bootstrap.ninja/ninjabootstrap/) for additional utilities and framework defaults. Set colors, typography and component variables, then compile your design with PicoSASS. No local Node.js installation or build process is needed for this workflow.

Use Picostrap on its own, extend it with a child theme, or pair it with LiveCanvas. The options panel brings theme settings, asset measurements, Sass tools and update controls into WordPress admin.

[Picostrap website](https://picostrap.com/) · [Documentation](https://picostrap.com/docs/) · [Introduction video](https://youtu.be/nrHFh_08w78) · [GitHub releases](https://github.com/livecanvas-team/picostrap5/releases)

## What is included

- **Bootstrap 5.3:** responsive layouts, components, forms, utilities and color modes. The bundled Bootstrap JavaScript identifies itself as **5.3.7**; the bundled files determine the version used by the theme.
- **NinjaBootstrap:** Sass defaults and utility extensions compiled into the theme stylesheet, including extended spacing, responsive positioning, sizing, borders, letter spacing and color shades.
- **PicoSASS:** browser-based Dart Sass compilation, Customizer previews, manual recompilation and continuous recompilation while working on Sass files.
- **WordPress Customizer:** colors, typography, Bootstrap variables, navigation, layouts, a live style guide and an AI palette generator.
- **Theme options:** a light WordPress-style workspace with an optional, browser-persisted dark mode, shared optimization switches and contextual help.
- **Performance tools:** saved asset sizes, gzip estimates, an HTTP compression check and a CSS reduction preview.
- **Optional frontend features:** lightbox, back-to-top button, hover menus, Bootstrap tooltips/popovers and a visitor-facing dark-mode switch.
- **WordPress integrations:** navigation menus, widget areas, page templates, WooCommerce support, LiveCanvas integration and a WindPress source provider.
- **GitHub updates:** stable parent-theme releases delivered through the native WordPress updater.
- **Translations:** a translation template and language files for German, Spanish, Estonian, French, Italian, Dutch, Portuguese and Romanian. New interface strings may require updated translations.

## Bootstrap and NinjaBootstrap

### Bootstrap: the foundation

[Bootstrap](https://getbootstrap.com/) supplies the grid, breakpoints, components, forms, helpers and core utility API. Picostrap includes its Sass sources in [`sass/bootstrap5/`](sass/bootstrap5/) and its JavaScript bundle in [`js/bootstrap.bundle.min.js`](js/bootstrap.bundle.min.js).

Use the [Bootstrap 5.3 documentation](https://getbootstrap.com/docs/5.3/) for standard classes and component markup, and the [Bootstrap Sass guide](https://getbootstrap.com/docs/5.3/customize/sass/) for variables, maps, mixins and import dependencies. Bootstrap JavaScript is enqueued in the footer using WordPress's `defer` strategy, subject to WordPress dependency resolution.

### NinjaBootstrap: more utilities in the same build

[NinjaBootstrap](https://bootstrap.ninja/ninjabootstrap/) configures and extends Bootstrap through Sass. It is already included in Picostrap: no separate plugin, npm installation or additional framework stylesheet is needed.

The included [`sass/ninjabootstrap/`](sass/ninjabootstrap/) sources extend the framework with:

- Spacing steps through `10`, plus negative margins.
- Responsive positioning and additional positional values.
- Extended width and height utilities.
- Responsive border utilities and letter-spacing classes.
- Additional theme-color shades and color utility collections.

For example, `position-relative position-lg-absolute` changes positioning at the large breakpoint, while `mt-3 mt-lg-7` applies a larger top margin on large screens. These utilities are generated with the rest of the theme CSS.

Consult the [NinjaBootstrap utility reference](https://bootstrap.ninja/ninjabootstrap/) for examples and syntax. The sources shipped with your installed theme remain the authority for available classes, especially if you customize the Sass imports.

## Getting started

1. **Install Picostrap.** In **Appearance > Themes > Add New > Upload Theme**, upload the theme ZIP. The theme directory should be named `picostrap5`; when using a GitHub source archive, rename its extracted top-level directory before uploading it by FTP or creating an installation ZIP.
2. **Activate a child theme for project code.** Use **Design & Sass > Install starter child theme** to install the latest stable tagged release of [Picostrap Child Base](https://github.com/livecanvas-team/picostrap5-child-base/releases) through WordPress. Activation is a separate step. An existing starter installation is preserved and can be reviewed in Themes. Keep PHP, template and Sass changes in the child theme so parent-theme updates do not overwrite them.
3. **Open Appearance > Picostrap Options.** Review the theme overview, saved asset sizes and available settings.
4. **Customize the design.** Open **Appearance > Customize** to change colors, typography, Bootstrap variables and layout options. Publish your changes to save the settings and compiled CSS.
5. **Recompile after editing Sass files.** Use **Design & Sass > Recompile CSS** in Picostrap Options, or the frontend compiler controls in the admin toolbar.
6. **Check the result.** Review desktop and mobile layouts and interactive components. Clear any page cache after changing settings or rebuilding styles.

Compilation needs an administrator session, JavaScript, network access to the browser compiler's dependencies, and a writable CSS output directory in the active theme. Regular visitors receive the saved stylesheet; they do not run the Sass compiler.

## The theme options workspace

Find **Picostrap Options** as the first item in **Appearance**. Its styles and scripts load only on that admin screen.

| Section | What you can do |
| --- | --- |
| **Overview** | Inspect saved CSS/JS sizes, estimate gzip payloads, check HTTP compression and preview CSS reduction against the public homepage. |
| **Optimizations** | Configure grouped frontend and WordPress utilities, then click **Save changes**. These settings share the same theme modifications as **Customizer > Global Utilities**. |
| **Design & Sass** | Open the Customizer, recompile Sass, inspect the saved bundle and read the compiler entry code and Sass pipeline. |
| **Tools & updates** | Export Customizer settings and navigation menus, check for updates and open documentation. |
| **Useful resources** | Find performance, SEO and content tools and prepare links for testing a public URL. |
| **LiveCanvas** | View the companion page-builder introduction when LiveCanvas is not installed. |

The panel defaults to a light appearance with neutral surfaces and blue accents. Its **Dark mode** switch remembers your choice in this browser and applies only to the options workspace. The separate frontend dark-mode setting controls the visitor-facing site.

A dashboard widget links users with theme-editing permissions to the panel and explains the experimental CSS feature. Individual actions retain their own WordPress capability checks: exports require permission to manage options, updates require permission to update themes, and Sass compilation requires an administrator.

### Asset sizes and compression

The payload inspector reports the size of the saved theme CSS and Bootstrap JavaScript. Gzip values are **level-6 estimates calculated from local files**, not observed transfer sizes.

The delivery check requests the public homepage, saved CSS and Bootstrap JS URLs and reads `Content-Encoding`. It recognizes gzip, Brotli and Zstandard; failed, redirected and cross-origin responses remain unverified. The theme does not enable server compression: configure that with your host or CDN.

Excluded WordPress core-script sizes are reference file sizes, not measured savings on every visit. Plugins, fonts, images and other stylesheets contribute additional requests and bytes.

### Experimental page-specific CSS

Enable this explicitly in **Optimizations**, or preview a candidate first with **Overview > Analyze homepage**. The preview does not change settings.

When enabled, the theme conservatively reduces its saved CSS against the rendered page and serves retained rules inline in place of the theme stylesheet request. An inline JavaScript guard can restore the complete bundle when dynamic content needs classes or IDs outside the retained set. If processing cannot safely produce a replacement, the original bundle remains available. Plugin stylesheets are handled separately.

The feature skips editing and preview contexts, the Customizer, Sass compilation, and non-page responses such as feeds and REST requests. It can apply to both anonymous and logged-in frontend visits. Reduction results use the object cache where available, with a transient fallback for anonymous requests.

A smaller CSS candidate does not guarantee a faster page or a higher PageSpeed score. Inline CSS travels with each HTML response, while an external stylesheet can be reused from the browser cache. Preview sizes include the inline markup and fallback helper. Compare first and repeat visits, and check navigation, galleries, forms and other dynamic content on desktop and mobile. Clear your page cache after enabling or disabling the setting.

## Customization and Sass compilation

### How the build works

1. Saved Customizer values with the `SCSSvar_` prefix become Sass variable declarations.
2. PicoSASS imports `sass/main.scss` and resolves Sass files from the child theme first, falling back to the parent theme.
3. The Bootstrap loader imports functions, NinjaBootstrap defaults and theme variables, followed by Bootstrap components and extended utility maps.
4. WordPress, Picostrap and WooCommerce styles follow; `sass/_custom.scss` is imported last.
5. The browser compiles the Sass and injects CSS into the preview.
6. An authenticated AJAX request saves the compiled bundle in the active theme's output directory and advances its cache version.

The default output is `css-output/bundle.css`. On multisite, compiled filenames include the site ID. Theme helper functions can override the output directory and filenames. The frontend compiler can also save a source map when one is supplied.

The bundle contains the framework and theme styles; it does not combine every plugin stylesheet. Editing a Sass file does not update the saved CSS until you recompile.

### Where to put custom code

Use these files **in your child theme**:

| File | Purpose |
| --- | --- |
| `sass/_theme_variables.scss` | Set variable defaults before Bootstrap generates components and utilities. |
| `sass/_custom.scss` | Add selectors and component styles after the framework and theme styles. |
| `sass/main.scss` | Customize the overall import pipeline when your project needs it. |

For example, in the child theme's `sass/_theme_variables.scss`:

```scss
$primary: #176b87 !default;
$border-radius: .375rem !default;
```

Using `!default` preserves an already-declared value, including one supplied by the Customizer. NinjaBootstrap defaults load before `_theme_variables.scss`; if you need to replace an already-defined default, assign it deliberately without `!default` and account for the Customizer precedence you want. See the [Bootstrap Sass variable documentation](https://getbootstrap.com/docs/5.3/customize/sass/#variable-defaults).

In `sass/_custom.scss`, add your project styles:

```scss
.project-intro {
  padding-block: 4rem;

  @include media-breakpoint-up(lg) {
    padding-block: 6rem;
  }
}
```

Setting Bootstrap variables only in `_custom.scss` is too late to regenerate components already imported earlier in the pipeline. Use the Customizer or the earlier variable file, then recompile.

### Typography and external services

The Customizer includes body and heading font pickers backed by Fontsource. The generated font code loads WOFF2 files from jsDelivr. The palette generator uses Huemint, and PicoSASS loads Dart Sass through JSPM. These workflows need access to their external services; selecting a remotely hosted font also adds a frontend font dependency.

Use the Customizer font controls, system font stacks or child-theme font-loading code to match your typography and hosting requirements.

### Locally hosted fonts with WordPress Font Library

On WordPress 7.0 or later, Picostrap can use enabled fonts from the native [WordPress Font Library](https://wordpress.org/documentation/article/the-font-library/). Existing font choices remain unchanged until you select a WordPress font.

1. Open **Appearance > Fonts** or **Manage WordPress fonts** in the options page’s **Design & Sass** tab.
2. Upload your font files or install a family from the library. Enable the weights and styles you need, then click **Update**. Installation and variant activation are saved separately from Customizer changes.
3. Open **Customize > Typography**, click the existing **Choose Font…** button for body text or headings, then choose **Hosted fonts** from the category filter. Only enabled families with font faces appear. **Add or manage fonts** opens the WordPress Font Library inside a modal without the admin sidebar; closing it refreshes the hosted list automatically. Use **Refresh list** after changes made in another tab. A new-tab link is also available inside the library modal.
4. Choose a hosted family in the picker for body text, headings, or both. Existing font-weight controls are preserved: enable matching variants, including italics or a suitable variable-font range, in the library.
5. Wait for the preview to finish compiling, then **Publish**. Clear page/CDN caches and check the public site. Editing Sass separately still requires **Recompile CSS**.

WordPress prints the font-face declarations and serves the installed files from your site. Picostrap assigns the selected family through its existing Bootstrap Sass variables; the compiler is unchanged. Local selections skip Picostrap’s Fontsource lookup and generated CDN declarations. You can use a local font for one role and the existing CDN picker for the other. WordPress may emit declarations for other enabled families, but browsers request font files when they are used.

**One-click local hosting:** after choosing a Fontsource/CDN font, use **Host locally** beneath its family field or inside the picker. Picostrap downloads its Latin, normal-style weights from jsDelivr and uploads them through WordPress’s Font Library API. Variable fonts use one file for the available weight range; static fonts import their listed weights. Matching local variants are reused. The family is enabled in WordPress and the selected Picostrap role switches to **Hosted fonts**. Wait for compilation, then **Publish** and clear page/CDN caches. Importing saves font files and library activation immediately; it does not publish pending Customizer changes. Other scripts, styles and language subsets can be added through **Add or manage fonts**. Custom remote fonts without Fontsource metadata use that manual upload workflow.

Imports require permission to upload files and edit theme options, plus access to Fontsource, jsDelivr and the WordPress REST API. If a download or upload fails, the current font choice is retained. Successfully uploaded variants stay in the Font Library so a retry can continue without downloading them again. A newly selected font made while an import is running is preserved.

Choosing a font through the existing picker, editing a family field manually, using font combinations or resetting Sass settings clears the corresponding WordPress selection. Picostrap does not migrate or download existing CDN fonts automatically, and it does not rewrite manual header code merely because you open the Customizer. Explicit font changes regenerate the theme-owned code under **Edit theme font code**, and the combined preview updates to match. Keep separately maintained font code in your child theme or the general header-code field.

If you disable or delete a selected family, Picostrap retains its family value and warns in the Customizer. Normal CSS fallback applies; no replacement is downloaded from a CDN. Re-enable the family and its variants, or choose another font and publish. WordPress font output remains enabled when **Disable Gutenberg and core block styles** is on. Older WordPress versions keep the original font controls without this integration.

### Inspect font-loading code

**Customize > Header / Footer Code > Font loading header code** shows one read-only preview combining your enabled theme font code with WordPress’s generated font-face rules. It works with CDN fonts, hosted fonts or a mix. The WordPress section reflects all enabled families in the Font Library, including families not currently assigned to body text or headings. It refreshes when you return from the library modal, refresh hosted fonts or complete a local import.

Expand **Edit theme font code** to edit the existing CDN/custom code or regenerate it from the font choices. That editable value is the existing `picostrap_fonts_header_code` theme modification; no migration is required. The combined preview is never saved or printed as a second font-loading block. WordPress remains responsible for its local font-face rules, so disabling or deleting a font does not leave a saved copy of those rules in the theme code.

**Disable theme font header code** suppresses only the editable theme code. It leaves WordPress-managed font rules active, and the preview explains what remains enabled. If a library refresh fails, the inspector identifies its WordPress rules as the last retrieved snapshot. Other font output added independently by plugins or custom hooks is outside this inspector.

## Optional features

The shared **Optimizations / Global Utilities** controls include:

- Disable Gutenberg and core block styles. Leave this off when your content depends on blocks; LiveCanvas Gutenberg compatibility preserves block styles.
- Disable the block-based widgets editor.
- Disable WordPress comments.
- Disable XML-RPC.
- Disable Bootstrap. **Enable only if you know what you are doing:** this removes the entire theme CSS bundle (including Bootstrap, NinjaBootstrap and custom Sass styles) and Bootstrap JavaScript. You must add your own CSS and replace any Bootstrap interactions your site needs.
- Enable the back-to-top button, desktop hover menus, lightbox, and Bootstrap tooltips/popovers.
- Enable experimental page-specific CSS.

Other Customizer controls include the frontend dark-mode switch, scroll-position detection and navigation/layout settings. They are separate from the ten switches in the options panel.

The optional lightbox uses gLightBox from jsDelivr. On single posts and pages it marks linked images automatically; use `glightbox` for additional targets, `nolightbox` to exclude an image, or `autolightbox` on a container.

## Frontend cleanup and integrations

Built-in cleanup removes selected WordPress discovery markup, REST discovery links, automatic emoji assets and automatic loading of the post-embed script. It preserves the REST API itself and normal content embeds. Plugins can still request script dependencies. The theme also suppresses detailed login error messages and prevents self-pingbacks.

- **LiveCanvas:** theme compatibility hooks, content handling and Bootstrap version information support the page builder.
- **WindPress:** the source provider scans active parent/child theme PHP, JavaScript and HTML, avoids duplicate roots and excludes dependency directories. Custom child-theme sources remain available to the provider.
- **WooCommerce:** theme support, shop widgets, styles and template overrides are included. Check your storefront against the WooCommerce version and extensions you deploy.
- **Multisite:** compiled CSS uses site-specific filenames after compilation. Settings, write permissions and cache behavior still need to be checked for your network.

These integrations do not install the corresponding plugins.

## Updates and exports

### Stable releases from GitHub

On WordPress 6.1 or later, with Picostrap or a Picostrap child theme active, the parent theme checks [GitHub Releases](https://github.com/livecanvas-team/picostrap5/releases) through the native WordPress update system.

Only published stable releases with a numeric `X.Y.Z` or `vX.Y.Z` tag are accepted. The release must be newer than the installed parent-theme version to produce an update offer. Drafts and prereleases are excluded; creating a tag or pushing a branch alone does not publish an update.

The updater prefers a release asset named `picostrap5.zip` and otherwise uses the release tag's source archive. Successful metadata is cached for six hours; failed checks are cached for five minutes. Use **Dashboard > Updates > Check again** to refresh the check immediately. Unavailable or invalid GitHub responses do not trigger a fallback to a development branch.

Keep custom code in a child theme: parent-theme updates replace parent-theme files.

### Configuration exports

**Tools & updates** can download saved theme modifications as `theme-options.json` and navigation menu data as `menus.json`. These exports do not include theme files, site content or a built-in import workflow. Use a full site backup when you need a restorable copy of the installation.

## Repository structure

| Path | Contents |
| --- | --- |
| [`sass/`](sass/) | Main pipeline, Bootstrap sources, NinjaBootstrap utilities and theme styles. |
| [`css-output/`](css-output/) | Saved theme CSS and any generated source maps. |
| [`inc/options-page/`](inc/options-page/) | Options panel PHP, CSS, JavaScript and local brand assets. |
| [`inc/customizer-assets/`](inc/customizer-assets/) | Customizer controls, preview helpers and font picker. |
| [`inc/picosass/`](inc/picosass/) | Browser compiler implementation. |
| [`inc/extra-settings.php`](inc/extra-settings.php) | Shared options definitions and save handler. |
| [`inc/used-css.php`](inc/used-css.php) | Experimental CSS reduction, caching and runtime fallback. |
| [`inc/theme-updates.php`](inc/theme-updates.php) | Stable GitHub release updater. |
| [`page-templates/`](page-templates/) | Blank, sidebar, demo and other page templates. |
| [`loops/`](loops/) and [`partials/`](partials/) | Post loops, navigation and reusable template parts. |
| [`languages/`](languages/) | Translation template and language files. |
| [`woocommerce/`](woocommerce/) | WooCommerce template overrides. |

The theme registers **Primary Menu** and **Secondary Menu**, plus **Main Sidebar**, **Footer Full** and **WooCommerce Shop Sidebar** widget areas. Page templates include blank layouts, left/right sidebar layouts and a Bootstrap demo. Loop layouts include the standard index, cards and an accordion.

## Developer hooks

Use child-theme code and the available hooks to extend behavior. Examples include:

- `ps_main_sass`: filter the generated compiler entry source.
- `picostrap_theme_default_settings`: filter initial theme defaults.
- `picostrap_used_css_enabled`: control eligibility for experimental CSS reduction.
- `picostrap_used_css_safelist`: retain additional classes and IDs needed by dynamic content.
- `picostrap_used_css_persistent_cache`: control the reducer's transient-cache fallback.

Footer and sharing helpers live in [`inc/template-tags.php`](inc/template-tags.php); WordPress actions and filters remain available throughout the theme.

## Compatibility and troubleshooting

The codebase includes Bootstrap 5.3 sources and JavaScript. The GitHub updater requires WordPress 6.1 or later; browser compilation requires modern JavaScript support, network access and administrator permissions. Validate the complete theme/plugin combination on your intended WordPress and PHP versions before deploying.

| Symptom | What to check |
| --- | --- |
| Sass changes are missing | Recompile after saving the source, then clear browser, page and CDN caches. |
| Compilation does not start | Confirm an administrator session, enabled JavaScript and access to the compiler's external dependencies. Inspect the browser console and network requests. |
| Compiled CSS cannot be saved | Check write access to the active theme's CSS output directory and the AJAX response. |
| Layout breaks after enabling an optimization | Restore the setting, clear page caches and test the affected content and interactions. |
| The compression check is unverified | Check the public asset URLs and response headers. Redirects, network failures and cross-origin responses prevent verification. |
| No GitHub update appears | Confirm a newer stable release exists, the theme's update metadata is intact and the GitHub API is reachable; use **Check again**. |

For site-specific troubleshooting, enable WordPress debug logging on your development installation and inspect PHP logs alongside browser errors.

## License

Picostrap 5 is licensed under [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html). Bundled third-party components retain their own license notices, including Bootstrap's MIT license.
