<?php

namespace Spatie\Sitemap\Tags;

use Carbon\Carbon;
use DateTimeInterface;

class Sitemap extends Tag
{
    public Carbon $lastModificationDate;

    public static function create(string $url): static
    {
        return new static($url);
    }

    public function __construct(public string $url)
    {
        $this->lastModificationDate = Carbon::now();
    }

    public function setUrl(string $url = ''): static
    {
        $this->url = $url;

        return $this;
    }

    public function setLastModificationDate(DateTimeInterface $lastModificationDate): static
    {
        $this->lastModificationDate = Carbon::instance($lastModificationDate);

        return $this;
    }

    public function path(): string
    {
        return parse_url($this->url, PHP_URL_PATH) ?? '';
    }
}
