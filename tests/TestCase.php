<?php

declare(strict_types=1);

namespace Spatie\Sitemap\Test;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\Sitemap\SitemapServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SitemapServiceProvider::class,
        ];
    }
}
