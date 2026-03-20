<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Tags;

use Carbon\Carbon;
use DateTimeInterface;
class News
{
    public const OPTION_ACCESS_SUB = 'Subscription';
    public const OPTION_ACCESS_REG = 'Registration';
    public const OPTION_GENRES_PR = 'PressRelease';
    public const OPTION_GENRES_SATIRE = 'Satire';
    public const OPTION_GENRES_BLOG = 'Blog';
    public const OPTION_GENRES_OPED = 'OpEd';
    public const OPTION_GENRES_OPINION = 'Opinion';
    public const OPTION_GENRES_UG = 'UserGenerated';
    public string $name;
    public string $language;
    public string $title;
    public Carbon $publication_date;
    public array $options;
    public function __construct(string $name, string $language, string $title, DateTimeInterface $publication_date, array $options = [])
    {
        $this->set_name($name)->set_language($language)->set_title($title)->set_publication_date($publication_date)->set_options($options);
    }
    public function set_name(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    public function set_language(string $language): static
    {
        $this->language = $language;
        return $this;
    }
    public function set_title(string $title): static
    {
        $this->title = $title;
        return $this;
    }
    public function set_publication_date(DateTimeInterface $publication_date): static
    {
        $this->publication_date = Carbon::instance($publication_date);
        return $this;
    }
    public function set_options(array $options): static
    {
        $this->options = $options;
        return $this;
    }
}