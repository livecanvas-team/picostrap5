# PHP 8.6 compatibility test

Tested on 2 November 2026 with the current worktree, including the pending cleanup changes.

## Result

**The web and cleanup checks pass on an actual PHP 8.6.0 RC4 runtime.** All 59 PHP files pass native 8.6 lint, and 157 automated assertions pass. No theme-originated warnings, deprecations or fatal errors were observed in those passing checks. The HTTP error capture remained empty. The separately reproduced WindPress scanner failure is described below; a subsequent fix is documented in [WindPress scanner fix](windpress-scanner-fix.md).

No additional production PHP changes were needed for these results. This is evidence for the tested release candidate and paths, not a certification of the future stable release or every plugin combination.

## Runtime and installation

- PHP **8.6.0RC4**, built locally for macOS ARM64 from the [official pre-release source](https://www.php.net/pre-release-builds.php).
- Source archive: `php-8.6.0RC4.tar.xz`, SHA-256 verified against the published value: `93f208da9ae059bf6863b0e1d101e6c6475a7b0f0acbf6c084950ab522f514d8`.
- Native executable: `/tmp/picostrap-php86/php-8.6.0RC4/sapi/cli/php`.
- Separate PHP configuration: `/tmp/picostrap-php86/php.ini`, with all error categories enabled (`error_reporting=-1`), logging enabled and OPcache active.
- Fresh WordPress **7.1.4**, installed using that PHP 8.6 executable, with SQLite Database Integration **3.1.2** and WP-CLI **2.12.0**.
- Disposable installation: `/tmp/picostrap-php86/wordpress`, served only on `127.0.0.1:8886`. An HTTP environment probe confirmed both `PHP_VERSION=8.6.0RC4` and `PHP_SAPI=cli-server`; requests stayed on that server without redirects to the PHP 8.4 installation.
- Existing PHP/Herd installations, the development website and its database were unchanged.

## Coverage

| Check | Result |
| --- | --- |
| Native PHP lint of all theme PHP files | 59 files, zero failures |
| Cleanup/output assertions | 61 passed |
| Authenticated editor, headers and child-theme URL checks | 23 passed |
| Actual duotone and group-layout asset checks | 6 passed |
| Hook, queue, sanitizer and dependency integration checks | 27 passed |
| Additional native-runtime and application checks | 40 passed |

The additional checks covered:

- Homepage, plain HTML/LiveCanvas-marked page, Gutenberg content, single post, search, 404 and public REST responses.
- Authenticated dashboard, Gutenberg and classic editor responses, theme options and Customizer. The Customizer registered the Sass settings successfully.
- Gutenberg enabled, disabled and simulated LiveCanvas `gtblocks` compatibility.
- Compiler-page PHP output and Sass source generation.
- Opt-in PHP branches for topbar, search, dark-mode switch, sharing buttons, back-to-top, hover menu, lightbox, tooltips, scroll detection, disabled comments, widget block editor and XML-RPC.
- Theme options and menu JSON exports.
- A real authenticated AJAX save of a small CSS bundle and source map. Both files and the theme settings were restored after the check.
- Trusted video-provider HTML, unknown-provider oEmbed sanitization, explicitly requested script dependencies, resource hints and self-ping URL boundaries.

CSS comparisons against the earlier PHP 8.4 captures normalize only the local port inside sourceURL comments (`8876` versus `8886`). Gutenberg/compatibility CSS remained equal after that normalization; the intentional disabled-Gutenberg cleanup assertions passed too.

## Diagnostics outside the passing theme paths

**WP-CLI tooling:** its bundled `php-cli-tools` emits a repeated deprecation from `cli/Colors.php:95`: using null as an array offset. This was the sole diagnostic origin in the final CLI capture. It was recorded separately rather than suppressed or attributed to the theme. The web requests do not load WP-CLI and produced none of those messages. A setup-only theme-update check also encountered the intentionally blocked outbound network; that diagnostic was archived with setup logs.

**WindPress issue at the time of this test:** directly invoking the theme's scanner under PHP 8.6 reproduced the existing undefined `$wpTheme` error. A minimally amended experimental copy also reproduced the unguarded `parent()` error. The same failures were reproduced on PHP 8.4; they were not a new PHP 8.6 regression. They have subsequently been fixed and the provider/transport validated on both runtimes; see the follow-up above. Full browser-side Tailwind compilation was not exercised by these PHP checks.

## Limits and artifacts

- LiveCanvas `gtblocks` is simulated; complete LiveCanvas/WooCommerce plugin workflows were not run. WooCommerce override files are included in the native lint result.
- Browser JavaScript was not executed. The Sass compiler's PHP output and AJAX save path were exercised, not browser-side Sass compilation.
- The custom PHP build includes the extensions required by these checks; image editing/upload processing and optional extensions such as GD/Imagick were not tested.
- This is a compatibility test, not an 8.4-versus-8.6 performance benchmark: the runtime builds and fixtures differ.
- Captures, logs and reproduction scripts are under `/tmp/picostrap-php86/`, including `runtime.json`, `lint.json`, `verification.txt`, `recheck-http.txt`, `duotone-http.txt`, `integration-checks.txt`, `extended.txt`, `cli-errors.jsonl` and `windpress.json`.

To rerun the repository integration checks with this runtime:

```sh
/tmp/picostrap-php86/php-8.6.0RC4/sapi/cli/php \
  -c /tmp/picostrap-php86/php.ini \
  /tmp/picostrap-php86/downloads/wp-cli.phar \
  --path=/tmp/picostrap-php86/wordpress \
  eval-file /path/to/picostrap5/tests/integration/clean-head.php
```

WP-CLI's color-library deprecations may appear in CLI logs during that command; the integration result is reported separately.
