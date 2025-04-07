<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'app_bootstrap' => [
        'path' => './assets/app_bootstrap.js',
        'entrypoint' => true,
    ],
    'app_tabler' => [
        'path' => './assets/app_tabler.js',
        'entrypoint' => true,
    ],
    '@symfony/stimulus-bundle' => [
        'path' => './vendor/symfony/stimulus-bundle/assets/dist/loader.js',
    ],
    'bootstrap' => [
        'version' => '5.3.3',
    ],
    '@popperjs/core' => [
        'version' => '2.11.8',
    ],
    'bootstrap/dist/css/bootstrap.min.css' => [
        'version' => '5.3.3',
        'type' => 'css',
    ],
    '@fontsource/noto-sans/latin.css' => [
        'version' => '5.2.6',
        'type' => 'css',
    ],
    '@fontsource/noto-sans/latin-ext.css' => [
        'version' => '5.2.6',
        'type' => 'css',
    ],
    'bootstrap-icons/font/bootstrap-icons.min.css' => [
        'version' => '1.11.3',
        'type' => 'css',
    ],
    '@hotwired/stimulus' => [
        'version' => '3.2.2',
    ],
    'sortablejs' => [
        'version' => '1.15.6',
    ],
    '@fontsource/roboto-mono/latin.css' => [
        'version' => '5.2.5',
        'type' => 'css',
    ],
    '@fontsource/roboto-mono/latin-ext.css' => [
        'version' => '5.2.5',
        'type' => 'css',
    ],
    '@tabler/icons-webfont/dist/tabler-icons.min.css' => [
        'version' => '3.31.0',
        'type' => 'css',
    ],
    '@tabler/core' => [
        'version' => '1.1.1',
    ],
    'autosize' => [
        'version' => '6.0.1',
    ],
    'imask' => [
        'version' => '7.6.1',
    ],
    '@tabler/core/dist/css/tabler.min.css' => [
        'version' => '1.1.1',
        'type' => 'css',
    ],
    '@hotwired/turbo' => [
        'version' => '7.3.0',
    ],
    'chart.js' => [
        'version' => '3.9.1',
    ],
];
