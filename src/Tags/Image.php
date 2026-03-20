<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Tags;

class Image
{
    public string $url;
    public string $caption;
    public string $geo_location;
    public string $title;
    public string $license;
    public static function create(string $url, string $caption = '', string $geo_location = '', string $title = '', string $license = ''): static
    {
        return new static($url, $caption, $geo_location, $title, $license);
    }
    public function __construct(string $url, string $caption = '', string $geo_location = '', string $title = '', string $license = '')
    {
        $this->set_url($url);
        $this->set_caption($caption);
        $this->set_geo_location($geo_location);
        $this->set_title($title);
        $this->set_license($license);
    }
    public function set_url(string $url = ''): static
    {
        $this->url = $url;
        return $this;
    }
    public function set_caption(string $caption = ''): static
    {
        $this->caption = $caption;
        return $this;
    }
    public function set_geo_location(string $geo_location = ''): static
    {
        $this->geo_location = $geo_location;
        return $this;
    }
    public function set_title(string $title = ''): static
    {
        $this->title = $title;
        return $this;
    }
    public function set_license(string $license = ''): static
    {
        $this->license = $license;
        return $this;
    }
}