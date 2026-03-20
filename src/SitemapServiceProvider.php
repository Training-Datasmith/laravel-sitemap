<?php

declare (strict_types=1);
namespace Spatie\Sitemap;

use Spatie\Laravel_Package_Tools\Package;
use Spatie\Laravel_Package_Tools\Package_Service_Provider;
class Sitemap_Service_Provider extends Package_Service_Provider
{
    public function configure_package(Package $package): void
    {
        $package->name('laravel-sitemap')->has_config_file()->has_views();
    }
}