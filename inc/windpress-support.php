<?php

// Integration Support for WindPress plugin
// https://wind.press/
// A great solution to add TailWind CSS

function pico_scanner_picostrap_provider(): array
{
    // Any files with this extension will be scanned
    $file_extensions = [
        'php',
        'js',
        'html',
    ];

    // Exclude the picostrap theme's internal directories
    $parentNotPath = [
        'picostrap5/css-output',
        'picostrap5/inc',
        'picostrap5/js',
        'picostrap5/languages',
        'picostrap5/sass',
    ];

    $contents = [];

    // The current active theme

    // if the theme is not Picostrap or its' child, early return
    if (get_template() != 'picostrap5') {
        return $contents;
    }

    $themeDir = $wpTheme->get_stylesheet_directory();

    $finder = new \WindPressDeps\Symfony\Component\Finder\Finder();

    // Check if the current theme is a child theme and get the parent theme directory
    $has_parent = $wpTheme->parent() ? true : false;
    $parentThemeDir = $wpTheme->parent()->get_stylesheet_directory() ?? null;

    $finder->files()->notPath($parentNotPath);

    // Scan the theme directory according to the file extensions
    foreach ($file_extensions as $extension) {
        $finder->files()->in($themeDir)->name('*.' . $extension);
        if ($has_parent) {
            $finder->files()->in($parentThemeDir)->name('*.' . $extension);
        }
    }

    // Get the file contents and send to the compiler
    foreach ($finder as $file) {
        $contents[] = [
            'name' => $file->getRelativePathname(),
            'content' => $file->getContents(),
        ];
    }

    return $contents;
}

/**
 * @param array $providers The collection of providers that will be used to scan the design payload
 * @return array
 */
function pico_register_picostrap_provider(array $providers): array
{
    $providers[] = [
        'id' => 'picostrap',
        'name' => 'Picostrap Theme',
        'description' => 'Scans the Picostrap theme & child theme',
        'callback' => 'pico_scanner_picostrap_provider', // The function that will be called to get the data
        'enabled' => \WindPress\WindPress\Utils\Config::get(sprintf(
            'integration.%s.enabled',
            'picostrap' // The id of this custom provider
        ), true),
    ];

    return $providers;
}

add_filter('f!windpress/core/cache:compile.providers', 'pico_register_picostrap_provider');
