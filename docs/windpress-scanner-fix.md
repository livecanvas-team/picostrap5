# WindPress scanner fix

Implemented after checkpoint commit `ae4029b`, which was pushed to `origin/master` before this work began.

## Change

- Initialize the active `WP_Theme` object and call parent methods only when a parent exists.
- Give each theme root its own Finder, with all three extension patterns (`php`, `js`, `html`) in one call. Each root is traversed once.
- Prune dependency directories (`node_modules`, `vendor`), test fixtures (`tests`) and Picostrap's internal directories using exclusions relative to the Finder root. Regression-test class names are not included in the generated CSS.
- Apply the Picostrap-specific exclusions only to the parent/theme framework. Child-theme PHP and JS under `inc/` and `js/` remain available to Tailwind.
- Prefix source names with the theme directory name so parent and child files such as `header.php` remain distinct. The existing array-of-`name`/`content` provider format and enable/disable setting are retained.

The scanner is still invoked through the WindPress compilation provider. No per-page scan or source-content cache was added.

## Validation

Validated on PHP **8.4.14** and the isolated PHP **8.6.0RC4** runtime:

- **22 fixture assertions per runtime** using WindPress's actual bundled Symfony Finder. They cover parent-only and child themes, duplicate detection, same-named parent/child files, extension filtering, dependency/internal-directory exclusions, preserved child sources, edited-file detection, provider hook registration and the unrelated-theme early exit.
- **16 WordPress assertions per runtime**, using actual `WP_Theme` instances and a temporary child theme in the disposable installations. The provider scanned both configurations successfully; the real `WindPress\WindPress\Core\Cache::fetch_contents()` accepted and encoded its payload without losing content. The actual WindPress config API could still disable the provider.
- Parent scan: 30 sources / 112,080 bytes. With the three eligible child fixtures: 33 sources / 112,212 bytes. No duplicate source names, undefined-variable warnings or missing-parent errors.
- PHP syntax checks on both runtimes and `git diff --check`.

Total: **76 assertions across both runtimes**. These validate discovery and the PHP provider/transport, not execution of the browser-side Tailwind compiler.

The repeatable standalone test is `tests/integration/windpress-scanner.php`:

```sh
php tests/integration/windpress-scanner.php /path/to/windpress/vendor/autoload.php
```

It creates and removes only temporary fixture files and uses simulated WordPress theme accessors. The separate actual-WordPress results and harness remain in `/tmp/picostrap-windpress-fix/` (`wp84.json`, `wp86.json`, `wordpress-check.php`). The development site's database and installed plugins were unchanged.
