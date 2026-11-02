<?php
/**
 * Standalone scanner regression test using WindPress's actual Finder dependency.
 * Run: php tests/integration/windpress-scanner.php /path/to/windpress/vendor/autoload.php
 * WordPress theme accessors are simulated; all fixtures live in a temporary directory.
 */
if (defined('ABSPATH')) {
    throw new RuntimeException('Run this test directly with PHP, outside WordPress.');
}
if (empty($argv[1]) || !is_file($argv[1])) {
    throw new RuntimeException('Pass the path to WindPress vendor/autoload.php.');
}
require $argv[1];

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

class PicostrapScannerTestTheme
{
    private $name;
    private $path;
    private $parent;

    public function __construct($name, $path, $parent = false)
    {
        $this->name = $name;
        $this->path = $path;
        $this->parent = $parent;
    }

    public function get_stylesheet() { return $this->name; }
    public function get_stylesheet_directory() { return $this->path; }
    public function parent() { return $this->parent; }
}

// Declare conditionally so accidental loading inside WordPress cannot redeclare its APIs.
if (!function_exists('get_template')) {
    function get_template() { return $GLOBALS['scanner_test_template']; }
    function wp_get_theme() {
        ++$GLOBALS['scanner_test_theme_reads'];
        return $GLOBALS['scanner_test_theme'];
    }
    function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {
        $GLOBALS['scanner_test_filters'][$hook] = $callback;
        return true;
    }
}
require dirname(__DIR__, 2) . '/inc/windpress-support.php';

$checks = 0;
function scanner_test_check($condition, $message)
{
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    ++$checks;
    echo 'PASS ', $message, PHP_EOL;
}

$root = tempnam(sys_get_temp_dir(), 'picostrap-scanner-');
unlink($root);
mkdir($root);
$parent_files = [
    'header.php' => '<header class="text-blue-500">Parent header</header>',
    'page-templates/landing.php' => '<main class="grid">Parent page</main>',
    'partials/banner.html' => '<aside class="p-4">Parent banner</aside>',
    'assets/custom.js' => 'const classes = "flex gap-4";',
];
$child_files = [
    'header.php' => '<header class="text-red-500">Child header</header>',
    'inc/component.php' => '<div class="rounded-xl">Child component</div>',
    'js/component.js' => 'const classes = "hover:bg-red-500";',
    'templates/component.html' => '<div class="p-8">Child template</div>',
    'sass/source.js' => 'const classes = "text-lg";',
];
$ignored_parent = [
    'inc/internal.php', 'js/bootstrap.js', 'sass/internal.php', 'languages/internal.php',
    'css-output/generated.js', 'node_modules/package/index.js', 'vendor/package/index.php',
    'tests/fixture.php', 'style.css', '.hidden.php',
];
$ignored_child = [
    'node_modules/package/index.js', 'vendor/package/index.php',
    'components/node_modules/package/index.js', 'components/vendor/package/index.php',
    'tests/fixture.html', 'style.css', 'sass/custom.scss', '.hidden.php',
];

try {
    foreach (['picostrap5' => $parent_files, 'child' => $child_files] as $name => $files) {
        $ignored = $name === 'picostrap5' ? $ignored_parent : $ignored_child;
        foreach ($ignored as $path) $files[$path] = 'IGNORED SOURCE';
        foreach ($files as $path => $content) {
            $destination = $root . '/' . $name . '/' . $path;
            if (!is_dir(dirname($destination))) mkdir(dirname($destination), 0777, true);
            file_put_contents($destination, $content);
        }
    }

    $parent = new PicostrapScannerTestTheme('picostrap5', $root . '/picostrap5');
    $child = new PicostrapScannerTestTheme('child', $root . '/child', $parent);
    $GLOBALS['scanner_test_template'] = 'picostrap5';
    $GLOBALS['scanner_test_theme_reads'] = 0;
    scanner_test_check(($GLOBALS['scanner_test_filters']['f!windpress/core/cache:compile.providers'] ?? null) === 'pico_register_picostrap_provider', 'Provider registration hook retained');

    foreach (['parent' => $parent, 'child' => $child] as $case => $theme) {
        $GLOBALS['scanner_test_theme'] = $theme;
        $contents = pico_scanner_picostrap_provider();
        $names = array_column($contents, 'name');
        scanner_test_check(count($names) === count(array_unique($names)), $case . ': no duplicate sources');
        $expected = [];
        foreach ($parent_files as $path => $content) $expected['picostrap5/' . $path] = $content;
        if ($case === 'child') {
            foreach ($child_files as $path => $content) $expected['child/' . $path] = $content;
        }
        $actual = array_column($contents, 'content', 'name');
        ksort($actual);
        ksort($expected);
        scanner_test_check($actual === $expected, $case . ': all intended sources included once, internal/dependency files excluded');
        foreach ($contents as $entry) {
            scanner_test_check(array_keys($entry) === ['name', 'content'] && is_string($entry['content']), $case . ': legacy provider payload preserved for ' . $entry['name']);
        }
    }

    file_put_contents($root . '/child/header.php', 'UPDATED CHILD SOURCE');
    $updated = array_column(pico_scanner_picostrap_provider(), 'content', 'name');
    scanner_test_check($updated['child/header.php'] === 'UPDATED CHILD SOURCE', 'Subsequent scan sees edited files');
    scanner_test_check(isset($updated['picostrap5/header.php'], $updated['child/header.php']), 'Parent and child files with the same relative name remain distinct');

    $GLOBALS['scanner_test_template'] = 'unrelated';
    $reads = $GLOBALS['scanner_test_theme_reads'];
    scanner_test_check(pico_scanner_picostrap_provider() === [], 'Unrelated theme returns no sources');
    scanner_test_check($GLOBALS['scanner_test_theme_reads'] === $reads, 'Unrelated theme exits before theme lookup or filesystem scan');
    echo $checks, ' scanner assertions passed on PHP ', PHP_VERSION, '.', PHP_EOL;
} finally {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) rmdir($file->getPathname());
        else unlink($file->getPathname());
    }
    rmdir($root);
    restore_error_handler();
}
