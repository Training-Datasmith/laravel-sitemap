<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Contracts;

use Spatie\Sitemap\Tags\Url;
interface Sitemapable
{
    public function to_sitemap_tag(): Url|string|array;
}