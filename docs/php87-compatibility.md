# PHP 8.7 development snapshot test

Tested on 2 November 2026, including the WindPress scanner fix.

## Result

**60 PHP files pass native lint and 195 assertions pass on an actual PHP 8.7.0-dev runtime.** The HTTP diagnostic capture remained empty. No additional theme changes were required to pass these checks.

This result applies to the pinned development snapshot below. It does not certify a future stable PHP 8.7 release or untested plugin combinations.

## Pinned runtime

- Official `php/php-src` master commit: [`f4d80ce83e0193a674ce42c4512666cb235128cf`](https://github.com/php/php-src/commit/f4d80ce83e0193a674ce42c4512666cb235128cf), committed on 1 November 2026 at 22:05:20 UTC.
- Version reported by the compiled binary: **PHP 8.7.0-dev / Zend Engine 4.7.0-dev**.
- Source archive SHA-256: `c7d14b43832c1e9b1cf456d3441c6d597490c9a8ef0e45c76d3a938d2f0bf656`.
- Executable: `/tmp/picostrap-php87/source/sapi/cli/php`.
- Build dependencies Bison 3.8.2 and re2c 4.3 were built into `/tmp/picostrap-php87/tools`; PHP was compiled locally for macOS ARM64. No system PHP installation was replaced.
- Dedicated configuration: `/tmp/picostrap-php87/php.ini`, with `error_reporting=-1`, logging enabled and OPcache active.
- Fresh WordPress **7.1.4**, installed with this PHP executable, using SQLite Database Integration **3.1.2** and WP-CLI **2.12.0**.
- Disposable WordPress root: `/tmp/picostrap-php87/wordpress`. HTTP tests used `127.0.0.1:8887`; the runtime probe confirmed `PHP_VERSION=8.7.0-dev`, `PHP_SAPI=cli-server` and OPcache enabled. URL assertions ensured requests stayed on that server.

The existing development site, its database, Herd and the installed PHP versions were unchanged.

## Checks

| Check | Passed |
| --- | ---: |
| Cleanup, assets, canonical links, REST, feed and preload assertions | 61 |
| Authenticated editors, header preservation and child-theme URL assertions | 23 |
| Actual duotone SVG/CSS and group-layout checks | 6 |
| Cleanup hooks, sanitizers, script/style queues and dependency checks | 27 |
| Additional native-runtime and application checks | 40 |
| WindPress scanner fixtures with its actual Finder dependency | 22 |
| Actual WordPress theme objects and WindPress provider transport | 16 |
| **Total assertions** | **195** |

Native PHP syntax checks also passed for **all 60 theme/test PHP files**.

The application checks include homepage, HTML/LiveCanvas-marked content, Gutenberg blocks, single post, search, 404, REST, authenticated dashboard, Customizer, theme options, compiler-page PHP output and enabled opt-in features. Theme options/menu JSON exports and a real authenticated AJAX save of CSS and a source map passed; generated files and settings were restored afterwards.

The WindPress checks use both the parent theme and a temporary actual child theme. They verify unique source names, correct exclusions, preserved child PHP/JS, edited-file discovery, provider configuration and successful processing by the real `WindPress\WindPress\Core\Cache::fetch_contents()` implementation. The earlier undefined-variable and missing-parent failures did not recur with the fix.

CSS comparisons normalize the local port in sourceURL comments before comparing against the earlier PHP 8.4 captures. The deliberate disabled-Gutenberg cleanup remains covered separately.

## Diagnostics and limits

- **HTTP:** no captured warnings, deprecations or fatal errors in the tested paths.
- **CLI:** the sole captured diagnostic origin is WP-CLI's bundled `php-cli-tools`, `cli/Colors.php:95`, which uses null as an array offset. This is the same tooling deprecation observed on PHP 8.6. It was recorded separately, not suppressed or attributed to the theme.
- LiveCanvas `gtblocks` is simulated. Complete LiveCanvas and WooCommerce plugin workflows were not exercised; WooCommerce override files pass lint.
- The PHP provider side of WindPress was exercised, not the full browser-side Tailwind compiler. Likewise, the Sass tests cover PHP source output and AJAX saving rather than browser-side compilation.
- Image editing/upload processing and optional runtime extensions such as GD/Imagick were not tested.
- This is a compatibility smoke/regression test, not a PHP-version performance benchmark or production throughput test.

## Reproduction artifacts

Artifacts and scripts are under `/tmp/picostrap-php87/`, including `commit.txt`, `downloads/master.json`, `runtime.json`, `lint.json`, `suite-results.json`, the per-suite text/error logs and `cli-errors.jsonl`.

The orchestrator is `run-suite.py`. The repository's repeatable integration tests can be invoked using this runtime:

```sh
/tmp/picostrap-php87/source/sapi/cli/php \
  -c /tmp/picostrap-php87/php.ini \
  /tmp/picostrap-php87/downloads/wp-cli.phar \
  --path=/tmp/picostrap-php87/wordpress \
  eval-file /path/to/picostrap5/tests/integration/clean-head.php

/tmp/picostrap-php87/source/sapi/cli/php \
  -c /tmp/picostrap-php87/php.ini \
  tests/integration/windpress-scanner.php \
  /path/to/windpress/vendor/autoload.php
```

Use the disposable installation for scripts that alter theme settings or create fixtures. WP-CLI's color-library diagnostics may appear separately from successful test output.
