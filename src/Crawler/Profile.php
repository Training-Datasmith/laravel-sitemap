<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Crawler;

use Spatie\Crawler\Crawl_Profiles\Crawl_Profile;
class Profile implements Crawl_Profile
{
    /** @var callable */
    protected $callback;
    public function __construct(protected string $base_url)
    {
    }
    public function should_crawl_callback(callable $callback): void
    {
        $this->callback = $callback;
    }
    public function should_crawl(string $url): bool
    {
        return ($this->callback)($url);
    }
}