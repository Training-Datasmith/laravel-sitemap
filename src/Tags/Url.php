<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Tags;

use Carbon\Carbon;
use DateTimeInterface;
class Url extends Tag
{
    public const CHANGE_FREQUENCY_ALWAYS = 'always';
    public const CHANGE_FREQUENCY_HOURLY = 'hourly';
    public const CHANGE_FREQUENCY_DAILY = 'daily';
    public const CHANGE_FREQUENCY_WEEKLY = 'weekly';
    public const CHANGE_FREQUENCY_MONTHLY = 'monthly';
    public const CHANGE_FREQUENCY_YEARLY = 'yearly';
    public const CHANGE_FREQUENCY_NEVER = 'never';
    public ?Carbon $last_modification_date = null;
    public ?string $change_frequency = null;
    public ?float $priority = null;
    /** @var Alternate[] */
    public array $alternates = [];
    /** @var Image[] */
    public array $images = [];
    /** @var Video[] */
    public array $videos = [];
    /** @var News[] */
    public array $news = [];
    public static function create(string $url): static
    {
        return new static($url);
    }
    public function __construct(public string $url)
    {
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
    public function set_change_frequency(string $change_frequency): static
    {
        $this->change_frequency = $change_frequency;
        return $this;
    }
    public function set_priority(float $priority): static
    {
        $this->priority = max(0, min($priority, 1));
        return $this;
    }
    public function add_alternate(string $url, string $locale = ''): static
    {
        $this->alternates[] = new Alternate($url, $locale);
        return $this;
    }
    public function add_image(string $url, string $caption = '', string $geo_location = '', string $title = '', string $license = ''): static
    {
        $this->images[] = new Image($url, $caption, $geo_location, $title, $license);
        return $this;
    }
    public function add_video(string $thumbnail_loc, string $title, string $description, ?string $content_loc = null, ?string $player_loc = null, array $options = [], array $allow = [], array $deny = [], array $tags = []): static
    {
        $this->videos[] = new Video($thumbnail_loc, $title, $description, $content_loc, $player_loc, $options, $allow, $deny, $tags);
        return $this;
    }
    public function add_news(string $name, string $language, string $title, DateTimeInterface $publication_date, array $options = []): static
    {
        $this->news[] = new News($name, $language, $title, $publication_date, $options);
        return $this;
    }
    public function path(): string
    {
        return parse_url($this->url, PHP_URL_PATH) ?? '';
    }
    public function segments(?int $index = null): array|string|null
    {
        $segments = collect(explode('/', $this->path()))->filter(fn($value) => $value !== '')->values()->to_array();
        if (!is_null($index)) {
            return $this->segment($index);
        }
        return $segments;
    }
    public function segment(int $index): ?string
    {
        return $this->segments()[$index - 1] ?? null;
    }
}