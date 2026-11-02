<?php

// Integration Support for WindPress plugin
// https://wind.press/
// A great solution to add TailWind CSS

function pico_scanner_picostrap_provider(): array
{
    $contents = [];

    // if the theme is not Picostrap or its' child, early return
    if (get_template() != 'picostrap5') {
        return $contents;
    }

    $wpTheme = wp_get_theme();
    $themes = [$wpTheme];
    $parent = $wpTheme->parent();
    if ($parent) {
        $themes[] = $parent;
    }

    foreach ($themes as $theme) {
        $themeName = $theme->get_stylesheet();
        $excludedDirectories = ['node_modules', 'vendor', 'tests'];

        // Exclusions are relative to each Finder root. Keep custom child-theme
        // PHP/JS sources, including inc/ and js/, available to Tailwind.
        if ($themeName === 'picostrap5') {
            $excludedDirectories = array_merge($excludedDirectories, [
                'css-output', 'inc', 'js', 'languages', 'sass',
            ]);
        }

        $finder = new \WindPressDeps\Symfony\Component\Finder\Finder();
        $finder->files()
            ->in($theme->get_stylesheet_directory())
            ->name(['*.php', '*.js', '*.html'])
            ->exclude($excludedDirectories);

        // Add each root once; repeated in() calls would scan every file again.
        foreach ($finder as $file) {
            $contents[] = [
                'name' => $themeName . '/' . $file->getRelativePathname(),
                'content' => $file->getContents(),
            ];
        }
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
