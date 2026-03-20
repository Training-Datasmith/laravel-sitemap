<?php

declare(strict_types=1);

/**
 * Example: generating a sitemap with laravel-sitemap (spatie/laravel-sitemap).
 *
 * Demonstrates building a sitemap programmatically without a full Laravel app.
 *
 * Run from the laravel-sitemap project root:
 *   php examples/generate_sitemap.php
 */

require __DIR__ . '/../vendor/autoload.php';

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

// --- Build a sitemap manually ---
$sitemap = Sitemap::create()
    ->add(Url::create('/')
        ->setLastModificationDate(new \DateTime('2026-03-20'))
        ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
        ->setPriority(1.0)
    )
    ->add(Url::create('/about')
        ->setLastModificationDate(new \DateTime('2026-01-01'))
        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
        ->setPriority(0.8)
    )
    ->add(Url::create('/blog')
        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
        ->setPriority(0.9)
    );

// Add multiple article URLs
$articles = [
    ['slug' => 'hello-world',    'updated' => '2026-03-15'],
    ['slug' => 'second-post',    'updated' => '2026-03-10'],
    ['slug' => 'third-post',     'updated' => '2026-03-05'],
];

foreach ($articles as $article) {
    $sitemap->add(
        Url::create('/blog/' . $article['slug'])
            ->setLastModificationDate(new \DateTime($article['updated']))
            ->setChangeFrequency(Url::CHANGE_FREQUENCY_YEARLY)
            ->setPriority(0.7)
    );
}

// --- Write to file ---
$path = sys_get_temp_dir() . '/demo-sitemap.xml';
$sitemap->writeToFile($path);

echo "Sitemap written to: $path\n";
echo "File size: " . filesize($path) . " bytes\n\n";
echo "First 500 chars:\n";
echo substr(file_get_contents($path), 0, 500) . "\n";

// Cleanup
@unlink($path);
