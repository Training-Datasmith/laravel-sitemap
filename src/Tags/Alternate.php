<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Tags;

class Alternate
{
    public string $locale;
    public string $url;
    public static function create(string $url, string $locale = ''): static
    {
        return new static($url, $locale);
    }
    public function __construct(string $url, string $locale = '')
    {
        $this->set_url($url);
        $this->set_locale($locale);
    }
    public function set_locale(string $locale = ''): static
    {
        $this->locale = $locale;
        return $this;
    }
    public function set_url(string $url = ''): static
    {
        $this->url = $url;
        return $this;
    }
}