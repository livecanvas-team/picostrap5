# PHP performance audit

Audited on 2 November 2026, including the pending cleanup changes. This audit adds no production PHP changes.

## Main findings, in priority order

### 1. Unused WordPress font resolution still costs work with Gutenberg disabled

Relevant theme code: `inc/clean-head.php:172`; core entry point: `wp_print_font_faces()`, registered on `wp_head` at priority 50.

The Disable Gutenberg cleanup removes global CSS generators, but WordPress still calls its Font Face resolver. That calls `wp_get_global_settings()` and initializes the merged theme.json/user global-style data even when no font-face rules are emitted.

On the plain HTML/LiveCanvas-marked page, with Gutenberg disabled and 140 configured Sass variables:

| Variant | Median PHP wall time | SQL queries |
| --- | ---: | ---: |
| Current theme | 12.749 ms | 13 |
| Experimental removal of the font callback | 9.991 ms | 9 |

The returned HTML had the same SHA-256 in both variants. This is approximately **2.8 ms / 22%** of this local request, and four fewer queries. SQL traces identify the removed work as resolution of the `wp_theme` term and `wp_global_styles` post. The existing cleanup was effective at reducing output bytes; this is additional CPU/database work with no output in the tested configuration.

Recommended next change: provide a guarded font-resolver opt-out for the no-Gutenberg/no-gtblocks path, accounting for WordPress Font Library/theme.json font usage and plugin overrides. Do not remove it globally: custom font-face declarations can be necessary even when the content editor is disabled. Persistent object caching may also change the production benefit.

### 2. WindPress scanner is broken and contains a latent triple scan

Relevant code: `inc/windpress-support.php:34`, `:40`, `:42`, `:45`.

Reproduced with the actual Finder dependency from the locally installed WindPress plugin, without activating that plugin on the test site:

- The callback uses `$wpTheme` without assigning it. Calling it raises an undefined-variable warning followed by an error.
- Experimentally adding only `$wpTheme = wp_get_theme()` exposes another error on the parent theme: `parent()` returns false, and line 40 calls a method on it. `?? null` does not guard that method call.
- The `in()` call inside the three-extension loop appends the same root three times. Finder combines the name patterns, then traverses all three copies of that root.
- The `notPath()` patterns include `picostrap5/`, but Finder matches paths relative to the supplied root. Consequently the intended internal-directory exclusions do not match. `notPath()` also filters results rather than pruning traversal as directory exclusions can.

To isolate the latter two issues, the Finder construction was exercised independently of the callback's earlier errors:

| Parent-theme scan | Entries | Unique files | Contents read |
| --- | ---: | ---: | ---: |
| Existing Finder construction | 225 | 75 | 1,766,157 bytes |
| One root, combined extension patterns, relative directory exclusions | 31 | 31 | 120,163 bytes |

A single warm/local traversal took about 10.7 ms versus 0.6 ms; these timings are illustrative, not a compiler benchmark. A child theme containing dependency/build directories could magnify the unnecessary traversal considerably.

Recommended fix: initialize the theme object; guard the parent object; add each root once; combine extension patterns; use appropriate directory exclusions relative to each root. Treat parent and child roots separately so useful child templates are retained. Exclude dependency directories such as `node_modules`/`vendor` where appropriate.

**Scope:** this callback is registered as a WindPress compilation provider. It is not invoked on ordinary page views by the theme. Its current errors prevent the latent traversal from completing; the table measures the Finder construction separately, not a successful current callback execution.

### 3. An unused widget calculation reruns plugin filters for every widget

Relevant code: `inc/widgets.php:60`.

`picostrap_widget_classes()` calls `apply_filters('sidebars_widgets', ...)` for every widget, although the result is used only by commented-out code. A direct 20-widget simulation produced 20 redundant executions, including widgets without the `dynamic-classes` placeholder.

Core alone makes this cheap, but plugins can attach more expensive callbacks. The theme multiplies their cost by the widget count without using the result. Remove the dead calculation rather than caching it. This is a smaller issue than the font resolver and is absent when no widgets are rendered.

### 4. Empty menus still construct a walker and fetch the privacy-policy post

Relevant code: `partials/header-navbar.php:65`.

The theme constructs its menu walker even if the primary location has no menu and the fallback will return false. In the tested WordPress version, the inherited `Walker_Nav_Menu` constructor resolves the privacy-policy URL, which generated one `SELECT` for the privacy-policy post on the otherwise empty-menu page.

A location check before constructing the walker is a small potential saving for sites without that menu. It should preserve supported menu filters/fallback behavior. It is not a significant bottleneck on sites that actually render a menu.

## What did not reveal an elephant

- No theme-originated outbound HTTP attempts were recorded on normal page, post, archive, search, 404 or REST requests.
- No Sass compilation or recursive filesystem scan runs on an ordinary visitor request. The Sass compiler is browser-side; its PHP output and file-save endpoint are gated to the compilation/admin workflow.
- Customizer control registration runs on `customize_register`; merely including its definitions does not build all controls on the frontend.
- The theme's PHP `functions.php` load, including its requires, measured about **0.090 ms** median with OPcache warm. This measures loading, not the whole cost of rendering the theme or callbacks later in the request.
- The three default theme settings are written only when missing, not on every established request.
- Repeated `get_theme_mod()` calls do deserialize the serialized options array again, but realistic settings did not make this a major cost. Artificially caching them globally would introduce Customizer/filter/multisite invalidation concerns for a small gain.

| Plain HTML page settings | Serialized theme mods | Median PHP wall time | Queries |
| --- | ---: | ---: | ---: |
| Existing test settings | 560 bytes | 13.468 ms | 13 |
| All 140 declared Sass variables populated | 7,135 bytes | 13.839 ms | 13 |
| Above plus an artificial 1 MiB unused option value | 1,055,756 bytes | 14.862 ms | 13 |

The page read theme mods 42 times. A separate CLI microbenchmark measured approximately 0.25 / 0.69 / 2.21 ms per 100 reads respectively. The large payload is a stress case, not an observed default-theme problem.

`pico_strip_lc_attributes()` took approximately 1.72 ms on a deliberately dense 560 KB HTML string containing 10,000 repetitions of editor attributes. It is linear string processing, not an unexpected database or I/O operation, and is only used by the fallback path when the LiveCanvas plugin is absent.

## Scalability checks

Fixtures used distinct thumbnail attachment records with populated metadata and articles of roughly 15 KB each. Images were not downloaded/rendered by a browser in this PHP audit.

| Blog index fixture | Median PHP wall time | Queries |
| --- | ---: | ---: |
| 10 posts, no menu | 20.691 ms | 18 |
| 50 posts, no menu | 43.878 ms | 18 |
| 10 posts, 200 custom menu links | 31.700 ms | 21 |

The query count did not grow with the number of articles/thumbnails. Rendering and excerpt processing grew with content volume. The large menu added processing and three queries, rather than one query per item. Neither test demonstrated an N+1 query problem in the theme.

## Method and limits

- Isolated WordPress 7.1.4 / PHP 8.4.14 / SQLite Database Integration installation, using the current theme worktree copied into `/tmp`.
- Local PHP server at the canonical site URL on port 8876, with OPcache confirmed enabled in the measured requests. No persistent object cache and no active application plugins.
- Principal comparisons used 30 measured requests after five warmups per variant. Font-resolver variants were interleaved. `SAVEQUERIES` was disabled for timing comparisons; a separate diagnostic run collected query call stacks.
- Time is PHP wall time from `REQUEST_TIME_FLOAT` to the shutdown measurement, excluding browser/network timing and the audit's result-file write. Lightweight measurement hooks still add overhead. Absolute timings are local diagnostics, not production latency or throughput predictions, especially with a different database/server.
- Callback profiling used Reflection-based wrappers in the disposable copy, not a native sampling profiler. It helped attribute work but was kept separate from the principal timing runs.
- Existing admin update checks, cron and outbound WordPress HTTP traffic remained disabled in the disposable installation. WindPress callback/Finder tests used its actual dependency but did not test a complete plugin compilation lifecycle. LiveCanvas and WooCommerce production workloads were not profiled.
- Raw results and reproduction scripts are in `/tmp/picostrap-php-audit/`: `comparison.json`, `scaling.json`, `windpress.json`, `settings-bench.json`, `content-bench.json`, SQL/callback snapshots under `results/`, and the corresponding PHP/Python scripts.

Production PHP files were left unchanged by this audit. Font skipping was experimental in the test harness only.
