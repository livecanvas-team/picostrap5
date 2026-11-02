<?php
/** Curated, outbound-only tools and the optional LiveCanvas companion introduction. */
defined( 'ABSPATH' ) || exit;

/** Installed means present, including inactive, network and must-use installations. */
function picostrap_options_livecanvas_installed( $plugins = null, $mu_plugins = null ) {
    if ( null === $plugins && null === $mu_plugins && ( defined( 'LC_PLUGIN_FILE' ) || defined( 'LC_PLUGIN_VERSION' ) || function_exists( 'lc_get_posts' ) ) ) return true;
    if ( null === $plugins || null === $mu_plugins ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        if ( null === $plugins ) $plugins = get_plugins();
        if ( null === $mu_plugins ) $mu_plugins = get_mu_plugins();
    }
    foreach ( array_merge( $plugins, $mu_plugins ) as $path => $plugin ) {
        if ( 'livecanvas-plugin-index.php' === basename( $path ) || 'livecanvas' === strtolower( trim( $plugin['TextDomain'] ?? '' ) ) || 'livecanvas' === strtolower( trim( wp_strip_all_tags( $plugin['Name'] ?? '' ) ) ) ) return true;
    }
    return false;
}

function picostrap_options_resource_groups() {
    return array(
        array( 'title' => __( 'Performance: find the actual bottleneck', 'picostrap5' ), 'tools' => array(
            array( 'PageSpeed Insights', 'https://pagespeed.web.dev/', 'pagespeed', __( 'Start here', 'picostrap5' ), __( 'Compare mobile and desktop lab diagnostics with real-user Core Web Vitals when field data is available.', 'picostrap5' ), __( 'Our take: investigate the largest image, server response and blocking work first. A missing field-data report is not a failed test, and a Lighthouse score is not a Google ranking score.', 'picostrap5' ) ),
            array( 'GTmetrix', 'https://gtmetrix.com/', '', __( 'Waterfall diagnosis', 'picostrap5' ), __( 'Inspect requests, loading order and resource weight to understand what is slowing a page down.', 'picostrap5' ), __( 'Use the same location and test settings for comparisons. Paste the selected URL into the tool; some features or test options may require an account.', 'picostrap5' ) ),
            array( 'WebPageTest', 'https://www.webpagetest.org/', '', __( 'Deeper investigation', 'picostrap5' ), __( 'Explore loading waterfalls and visual progress under controlled browser and connection conditions.', 'picostrap5' ), __( 'Useful when a headline score does not explain a slow experience. Paste the selected URL and compare first and repeat visits where available; keep test conditions consistent.', 'picostrap5' ) ),
        ) ),
        array( 'title' => __( 'SEO: start with evidence from your site', 'picostrap5' ), 'tools' => array(
            array( 'Google Search Console', 'https://search.google.com/search-console/', '', __( 'First SEO setup', 'picostrap5' ), __( 'Review search queries, clicks, indexing, sitemaps and how Google sees a page through URL Inspection.', 'picostrap5' ), __( 'Our first choice for an existing site. Check that important pages can be indexed before polishing speed scores. Requires a Google account and property verification.', 'picostrap5' ) ),
            array( 'Bing Webmaster Tools', 'https://www.bing.com/webmasters/', '', __( 'Another search perspective', 'picostrap5' ), __( 'Manage your site’s presence in Bing and investigate crawling and indexing through its webmaster tools.', 'picostrap5' ), __( 'Worth setting up alongside Search Console. Do not assume Google and Bing index the same pages. An account and site verification are required.', 'picostrap5' ) ),
            array( 'Screaming Frog SEO Spider', 'https://www.screamingfrog.co.uk/seo-spider/', '', __( 'Technical site crawl', 'picostrap5' ), __( 'A desktop crawler for finding broken links, redirects, duplicate titles and canonical or indexing inconsistencies across pages.', 'picostrap5' ), __( 'Use it when you need a site-wide inventory rather than a one-page grade. Review findings before making bulk changes; free and paid editions have different limits.', 'picostrap5' ) ),
            array( 'Ahrefs Free', 'https://ahrefs.com/webmaster-tools', '', __( 'Audits & link research', 'picostrap5' ), __( 'Formerly Ahrefs Webmaster Tools: site audits and search/link insights for verified websites.', 'picostrap5' ), __( 'Useful for spotting technical issues and investigating links. Its proprietary authority metrics are not Google ranking scores. Account access, verification and product limits apply.', 'picostrap5' ) ),
            array( 'Semrush', 'https://www.semrush.com/', '', __( 'Research & competitive context', 'picostrap5' ), __( 'Explore keyword research, competitor analysis and technical site audits.', 'picostrap5' ), __( 'Consider it when you have a recurring research workflow. Treat third-party traffic and keyword estimates as directional, then check your own Search Console data. Features depend on the account and plan.', 'picostrap5' ) ),
        ) ),
        array( 'title' => __( 'Content & search appearance', 'picostrap5' ), 'tools' => array(
            array( 'Google Trends', 'https://trends.google.com/trends/', '', __( 'Topic & seasonality research', 'picostrap5' ), __( 'Explore search interest over time and compare topics before planning or updating content.', 'picostrap5' ), __( 'Use it to understand seasonality and audience language, not to manufacture pages around every trend. Interest is relative, not an exact search-volume forecast.', 'picostrap5' ) ),
            array( 'Google Rich Results Test', 'https://search.google.com/test/rich-results', 'richresults', __( 'Structured-data validation', 'picostrap5' ), __( 'Test whether a public page’s structured data supports Google rich-result features.', 'picostrap5' ), __( 'Use only markup that truthfully reflects the visible page. A valid result does not guarantee enhanced search appearance, indexing or rankings.', 'picostrap5' ) ),
        ) ),
    );
}
function picostrap_options_resources_panel() {
    ?>
    <section class="pico-tabpanel" id="pico-resources" aria-labelledby="pico-resources-heading">
        <div class="pico-resource-intro">
            <h2 id="pico-resources-heading"><?php esc_html_e( 'Build pages worth finding. Then make them fast.', 'picostrap5' ); ?></h2>
            <p><?php esc_html_e( 'Our advice: put useful, original content and search intent ahead of chasing a perfect speed score. Speed helps visitors use your content; it cannot replace relevance, first-hand knowledge, trust or a clear answer.', 'picostrap5' ); ?></p>
            <p><?php esc_html_e( 'Picostrap gives you a lean foundation. Your content, images, hosting and plugin choices still shape the result. Measure meaningful outcomes: relevant search clicks, qualified enquiries and completed purchases.', 'picostrap5' ); ?></p>
            <div class="pico-actions"><a href="https://developers.google.com/search/docs/fundamentals/creating-helpful-content" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Google’s people-first content guidance', 'picostrap5' ); ?></a><a href="https://developers.google.com/search/docs/appearance/page-experience" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'How page experience fits into Search', 'picostrap5' ); ?></a></div>
        </div>
        <form class="pico-resource-target" id="pico-resource-target">
            <label for="pico-test-url"><?php esc_html_e( 'Public page to test', 'picostrap5' ); ?></label>
            <div class="pico-resource-url-row"><input id="pico-test-url" type="url" required value="<?php echo esc_url( home_url( '/' ) ); ?>" aria-describedby="pico-test-advice"><button type="submit" class="pico-button"><?php esc_html_e( 'Update test links', 'picostrap5' ); ?></button><button type="button" class="pico-button" id="pico-copy-test-url"><?php esc_html_e( 'Copy URL', 'picostrap5' ); ?></button></div>
            <p id="pico-test-advice" role="status" aria-live="polite"><?php esc_html_e( 'Online tools need a publicly accessible URL. Localhost, private networks and pages behind a login cannot be tested externally. All tool links open a new tab or window.', 'picostrap5' ); ?></p>
            <p class="pico-help"><?php esc_html_e( 'PageSpeed and Rich Results links can use this URL directly. For GTmetrix and WebPageTest, open the tool and paste the copied URL. No test is run or site URL sent automatically by this panel.', 'picostrap5' ); ?></p>
        </form>
        <details class="pico-details pico-resource-method"><summary><?php esc_html_e( 'A practical order of work — and how to read the results', 'picostrap5' ); ?></summary>
            <ol class="pico-pipeline">
                <li><strong><?php esc_html_e( 'Check discovery and indexing.', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'Inspect an important URL in Search Console. Resolve accidental noindex rules, blocked access and wrong canonical URLs before expecting search traffic.', 'picostrap5' ); ?></p></li>
                <li><strong><?php esc_html_e( 'Make the page worth visiting.', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'Answer the intended question clearly. Add original examples, evidence, helpful product or service details, clear navigation and a useful next step. More words alone do not make a better page.', 'picostrap5' ); ?></p></li>
                <li><strong><?php esc_html_e( 'Measure representative pages on mobile.', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'Test the homepage and key landing, article or product templates. Field data describes real visitors; lab tests describe a controlled run. Compare several runs under the same conditions, including cold and warm caches.', 'picostrap5' ); ?></p></li>
                <li><strong><?php esc_html_e( 'Fix large, visible problems first.', 'picostrap5' ); ?></strong><p><?php esc_html_e( 'Prioritize oversized images, slow server responses, unnecessary third-party scripts and layout shifts. Once real visitors have a good experience, improving the offer or content is often a better use of time than chasing the last few lab-score points.', 'picostrap5' ); ?></p></li>
            </ol>
        </details>
        <?php foreach ( picostrap_options_resource_groups() as $group ) : ?>
            <section class="pico-resource-group"><h3><?php echo esc_html( $group['title'] ); ?></h3>
            <?php foreach ( $group['tools'] as $tool ) : ?>
                <article class="pico-resource-row">
                    <div><h4><?php echo esc_html( $tool[0] ); ?></h4><span class="pico-resource-purpose"><?php echo esc_html( $tool[3] ); ?></span></div>
                    <div><p><?php echo esc_html( $tool[4] ); ?></p><p class="pico-resource-opinion"><?php echo esc_html( $tool[5] ); ?></p></div>
                    <a class="pico-button" href="<?php echo esc_url( $tool[1] ); ?>" <?php if ( $tool[2] ) : ?>data-pico-tester="<?php echo esc_attr( $tool[2] ); ?>"<?php endif; ?> target="_blank" rel="noopener noreferrer"><?php printf( esc_html__( 'Open %s', 'picostrap5' ), esc_html( $tool[0] ) ); ?><svg width="14" height="14" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="M4 12 12 4M4 4h8v8" stroke="currentColor" stroke-width="1.5"/></svg></a>
                </article>
            <?php endforeach; ?></section>
        <?php endforeach; ?>
    </section>
    <?php
}
function picostrap_options_livecanvas_panel() {
    ?>
    <section class="pico-tabpanel" id="pico-livecanvas" aria-labelledby="pico-livecanvas-heading">
        <div class="pico-companion-hero">
            <div>
                <img class="pico-companion-logo" src="<?php echo esc_url( get_template_directory_uri() . '/inc/options-page/assets/livecanvas-logo.svg' ); ?>" width="261" height="41" alt="LiveCanvas" loading="lazy">
                <h2 id="pico-livecanvas-heading"><?php esc_html_e( 'Your foundation is ready. Bring your pages to life.', 'picostrap5' ); ?></h2>
                <p><?php esc_html_e( 'LiveCanvas is the perfect companion to Picostrap: a visual WordPress builder for creating distinctive websites while keeping control of your HTML.', 'picostrap5' ); ?></p>
                <p><?php esc_html_e( 'Picostrap sets the typography, colors and Bootstrap foundation. LiveCanvas helps you turn that foundation into landing pages, compelling content and reusable sections — with visual editing and code access working together.', 'picostrap5' ); ?></p>
                <div class="pico-actions"><a class="pico-button pico-primary" href="https://livecanvas.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Explore LiveCanvas', 'picostrap5' ); ?></a><a href="https://docs.livecanvas.com/the-stack/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'See how the stack fits together', 'picostrap5' ); ?></a></div>
                <p class="pico-help pico-companion-license"><?php esc_html_e( 'LiveCanvas is a separate plugin. Check its website for current licensing and pricing. Nothing is installed automatically.', 'picostrap5' ); ?></p>
            </div>
            <div class="pico-companion-code"><p><?php esc_html_e( 'Visual freedom. Real Bootstrap markup.', 'picostrap5' ); ?></p><pre><code>&lt;section class="container py-5"&gt;
  &lt;div class="row align-items-center"&gt;
    &lt;div class="col-lg-7"&gt;
      &lt;h1&gt;Make your message matter.&lt;/h1&gt;
      &lt;p class="lead"&gt;
        A clear offer. A useful next step.
      &lt;/p&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/section&gt;</code></pre><p class="pico-help"><?php esc_html_e( 'Example Bootstrap markup. Work visually and refine the HTML when you need to.', 'picostrap5' ); ?></p></div>
        </div>
        <dl class="pico-companion-benefits">
            <div><dt><?php esc_html_e( 'One coherent design system', 'picostrap5' ); ?></dt><dd><?php esc_html_e( 'Build on the Bootstrap styles and Sass choices you already set in Picostrap, instead of starting your design decisions from scratch.', 'picostrap5' ); ?></dd></div>
            <div><dt><?php esc_html_e( 'Visual editing, with code in reach', 'picostrap5' ); ?></dt><dd><?php esc_html_e( 'Arrange sections visually and work directly with HTML for precise layouts, responsive utilities and custom details.', 'picostrap5' ); ?></dd></div>
            <div><dt><?php esc_html_e( 'Content that stays in WordPress', 'picostrap5' ); ?></dt><dd><?php esc_html_e( 'LiveCanvas stores page markup as HTML in WordPress post content. Review its documentation to understand the editing and rendering workflow.', 'picostrap5' ); ?></dd></div>
        </dl>
        <section class="pico-tool-section"><h3><?php esc_html_e( 'Build for a successful visit.', 'picostrap5' ); ?></h3><p><?php esc_html_e( 'Start with your visitor’s question, show why your offer is useful, and make the next action obvious. Combine thoughtful content with lean markup, well-sized images and a responsive layout. No builder can guarantee rankings or conversions; the craft is in what you create.', 'picostrap5' ); ?></p><div class="pico-actions"><a href="https://docs.livecanvas.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Browse LiveCanvas documentation', 'picostrap5' ); ?></a><a href="#pico-resources" data-pico-link><?php esc_html_e( 'Plan your performance & SEO checks', 'picostrap5' ); ?></a></div></section>
    </section>
    <?php
}
add_action( 'admin_enqueue_scripts', function( $hook ) {
    if ( 'appearance_page_picostrap-theme-options' !== $hook ) return;
    wp_enqueue_script( 'pico-resources', get_template_directory_uri() . '/inc/options-page/options-resources.js', array(), filemtime( __DIR__ . '/options-resources.js' ), true );
    wp_localize_script( 'pico-resources', 'picoResourceText', array(
        'local' => __( 'This URL is local or private. Enter your public production URL to create direct test links; online services cannot reach this installation.', 'picostrap5' ),
        'ready' => __( 'Direct test links updated. PageSpeed and Rich Results will receive this page URL only when you open their links. Other tools open their start page; paste the copied URL there.', 'picostrap5' ),
        'invalid' => __( 'Enter a complete HTTP or HTTPS page URL without embedded login credentials.', 'picostrap5' ),
        'copied' => __( 'URL copied. Paste it into your chosen tool.', 'picostrap5' ),
        'copy' => __( 'Select and copy the URL from the field using your keyboard.', 'picostrap5' ),
    ) );
} );
