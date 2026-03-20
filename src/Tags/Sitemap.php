<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Tags;

use Carbon\Carbon;
use DateTimeInterface;
class Sitemap extends Tag
{
    public Carbon $last_modification_date;
    public static function create(string $url): static
    {
        return new static($url);
    }
    public function __construct(public string $url)
    {
        $this->last_modification_date = Carbon::now();
    }
    public function set_url(string $url = ''): static
    {
        $this->url = $url;
        return $this;
    }
    public function set_last_modification_date(DateTimeInterface $last_modification_date): static
    {
        $this->last_modification_date = Carbon::instance($last_modification_date);
        return $this;
    }
    public function path(): string
    {
        return parse_url($this->url, PHP_URL_PATH) ?? '';
    }
}