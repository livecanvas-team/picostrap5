# WordPress cleanup audit

Tested on 2 November 2026 against commit `72a69dc` and the changes accompanying this report.

## Environment

- Fresh WordPress **7.1.4**, downloaded from [wordpress.org/latest.tar.gz](https://wordpress.org/latest.tar.gz); version confirmed in `wp-includes/version.php`.
- PHP 8.4.14, WP-CLI 2.12.0, SQLite Database Integration 3.1.2 (drop-in 1.8.0).
- Isolated installation at `/tmp/picostrap-performance-audit/wordpress`, served locally on port 8876. The existing development site's database and files were untouched.
- No application plugins enabled. Temporary test plugins simulate the LiveCanvas `gtblocks` option and third-party block/late CSS; this is not an end-to-end LiveCanvas plugin test.
- Cron and outbound WordPress HTTP requests disabled. Admin update checks were also disabled in the test installation after they produced expected offline-connection warnings.

Archive SHA-256: `881e53a4681ff2cd911651b11cccdede5ae477ddf0765af5d6a1a8b219a1d835`.

## Findings and changes

The existing generator, feeds/discovery links, REST discovery links, emoji assets and wp-embed cleanup already worked on the tested frontend pages. Feed and public REST functionality still worked.

1. **Global styles survived the Disable Gutenberg setting.** WordPress also enqueues these on `wp_footer` at priority 1. Removing only the `wp_enqueue_scripts` callback left approximately 9 KB of inline global CSS. Cleanup now removes both callbacks, only on the frontend and when LiveCanvas `gtblocks` is not enabled.
2. **Individual block styles survived the setting.** Modern WordPress loads core block styles during content rendering. The existing head-only cleanup removed the aggregate stylesheet but missed these late styles. Cleanup now runs before footer printing too, removes core block style handles and their placeholders/layout styles, and leaves third-party block styles registered and available.
3. **The block-library preload was unused.** Picostrap sent an HTTP preload for the full `block-library/style.min.css`, while the tested WordPress installation emitted individual inline block CSS. The hardcoded block preload is removed. WordPress still controls the styles needed by rendered blocks; the Bootstrap bundle preload remains and matches its stylesheet URL.
4. **Admin emoji cleanup ran too early.** WordPress registers its admin emoji callbacks after the theme's `init` callback. The authenticated editor still included emoji CSS, settings and loader JavaScript. Cleanup now runs again at `admin_init`, explicitly removes the modern style enqueue callbacks, and also covers embed templates. Native emoji text is preserved.
5. **Embed cleanup disabled useful core behavior.** The old code removed oEmbed sanitization and the local-result shortcut, then deregistered `wp-embed` entirely. Sanitization and the shortcut are retained; automatic host-script enqueueing is removed directly and standalone `wp-embed` is dequeued before printing. A plugin explicitly depending on that script can still resolve it. Both discovery hook priorities (4 and 10) are removed explicitly.
6. **Opt-in block cleanup missed modern visual assets.** Duotone SVG/CSS generators and block-style variation styles are now covered when Disable Gutenberg is effective. Unneeded classic-theme style enqueue callbacks are removed early as well. A real image block with a custom duotone filter and a group with flex/gap layout verified that these assets disappear only in the disabled scenario, and remain with Gutenberg or `gtblocks` enabled.
7. **Preload handling affected other responses and headers.** Feeds, robots, sitemaps, sitemap stylesheets, trackbacks and embed templates do not need the Bootstrap bundle preload. They are excluded; existing plugin `Link` headers are preserved by appending rather than replacing. `add_query_arg()` keeps preload URLs correct when a child theme's CSS URL helper already includes query parameters.
8. **Resource hints and self-pings had fragile comparisons.** Emoji hint cleanup now handles both URL strings and attribute arrays without array-to-string warnings, and does not rely on a filter already forced to false. Self-ping detection checks host, port and path boundaries, including explicit default ports, rather than incorrectly treating similarly prefixed external URLs as internal.

## Measured results

Uncompressed response bytes, using the same content and templates before and after:

| Gutenberg disabled | HTML before | HTML after | Inline CSS before | Inline CSS after |
| --- | ---: | ---: | ---: | ---: |
| Plain HTML page, marked as LiveCanvas content | 12,951 | 3,568 | 9,470 | 135 |
| Group, paragraph and button blocks | 18,472 | 3,841 | 14,523 | 135 |
| Blog index | 14,895 | 4,722 | 10,209 | 135 |
| Single post | 19,391 | 9,218 | 10,209 | 135 |

The remaining 135 bytes are WordPress responsive-image `auto` sizing CSS, which is useful and retained.

With Gutenberg enabled, or `gtblocks` enabled, inline CSS was identical before and after on all four pages. Only the unused preload was removed. That file is **140,829 bytes raw**, or **18,924 bytes with local gzip compression**. These are file sizes, not measured browser transfer savings; actual transfer depends on server compression and browser caching.

## Verification

117 automated assertions passed across the captures, HTTP checks and in-process integration checks, plus PHP syntax checks and `git diff --check`:

- Existing head cleanup and canonical links retained; native emoji content unchanged.
- No global/core block CSS with Disable Gutenberg enabled; identical block CSS with Gutenberg or `gtblocks` enabled.
- Responsive-image CSS retained.
- Public REST page endpoint and RSS feed work.
- Authenticated admin editor loads with its CSS; regular pages use the classic editor when disabled; `lc_gt_block` retains its block-editor exception.
- Third-party block CSS and styles enqueued late in the footer remain available.
- Disable Bootstrap removes its CSS, JS and preload.
- CSS preload matches the stylesheet URL with both default and custom bundle versions, preserving the fix in `72a69dc`.
- No PHP debug warnings/errors in the final run after isolating offline update checks.
- Actual authenticated Gutenberg/classic editor and embed responses contain no emoji loader or styles.
- Trusted video HTML remains unchanged, unknown-provider HTML is sanitized/sandboxed, and explicit plugin script dependencies resolve.
- Non-page responses omit the bundle preload; plugin Link headers survive; a child theme URL containing an existing query string matches its preload.
- Real duotone and layout block assets are retained or removed according to the setting and compatibility option.

The repeatable hook/queue integration test is included alongside the source at `tests/integration/clean-head.php` (27 assertions). Run it without LiveCanvas on a disposable WordPress installation with Picostrap active:

```sh
wp --path=/path/to/test-wordpress eval-file /path/to/picostrap5/tests/integration/clean-head.php
```

It changes only in-process hooks, queues and registries; it does not save options.

Captured HTML, headers, metrics, fixtures and scripts remain under `/tmp/picostrap-performance-audit/`. The main comparison script is `run-audit.py`; the original 61 assertions are in `verify.py`, with results in `verification.txt`. Additional results are in `recheck-http.txt` (23), `duotone-http.txt` (6) and `integration-checks.txt` (27). The HTTP scripts target and mutate only that disposable installation. The before/after captures are the evidence for this report; rerunning a capture phase overwrites that phase's files.

This was an HTTP/output regression audit, not a browser rendering, Lighthouse, Core Web Vitals or production TTFB benchmark. Disabling Gutenberg intentionally removes core block presentation styles; sites using blocks should retain Gutenberg or enable the LiveCanvas block compatibility option. Plugin-specific integration beyond the simulated cases remains untested.

## Deliberately retained

The complete Bootstrap bundle is part of Picostrap's intended functionality. Responsive-image sizing CSS, canonical links, WordPress image loading/priority behavior, useful resource hints and plugin dependencies are retained. No blanket jQuery, REST API, Heartbeat or speculation-rule disabling was added: those changes could interfere with functionality or remove useful performance behavior without a measured benefit here.
