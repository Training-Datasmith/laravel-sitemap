<?php

declare (strict_types=1);
namespace Spatie\Sitemap\Tags;

use InvalidArgumentException;
class Video
{
    public const OPTION_PLATFORM_WEB = 'web';
    public const OPTION_PLATFORM_MOBILE = 'mobile';
    public const OPTION_PLATFORM_TV = 'tv';
    public const OPTION_NO = 'no';
    public const OPTION_YES = 'yes';
    public string $thumbnail_loc;
    public string $title;
    public string $description;
    public ?string $content_loc = null;
    public ?string $player_loc = null;
    public array $options;
    public array $allow;
    public array $deny;
    public array $tags;
    public function __construct(string $thumbnail_loc, string $title, string $description, ?string $content_loc = null, ?string $player_loc = null, array $options = [], array $allow = [], array $deny = [], array $tags = [])
    {
        if ($content_loc === null && $player_loc === null) {
            throw new InvalidArgumentException("It's required to provide either a Content Location or Player Location");
        }
        $this->set_thumbnail_loc($thumbnail_loc)->set_title($title)->set_description($description)->set_content_loc($content_loc)->set_player_loc($player_loc)->set_options($options)->set_allow($allow)->set_deny($deny)->set_tags($tags);
    }
    public function set_thumbnail_loc(string $thumbnail_loc): static
    {
        $this->thumbnail_loc = $thumbnail_loc;
        return $this;
    }
    public function set_title(string $title): static
    {
        $this->title = $title;
        return $this;
    }
    public function set_description(string $description): static
    {
        $this->description = $description;
        return $this;
    }
    public function set_content_loc(?string $content_loc): static
    {
        $this->content_loc = $content_loc;
        return $this;
    }
    public function set_player_loc(?string $player_loc): static
    {
        $this->player_loc = $player_loc;
        return $this;
    }
    public function set_options(array $options): static
    {
        $this->options = $options;
        return $this;
    }
    public function set_allow(array $allow): static
    {
        $this->allow = $allow;
        return $this;
    }
    public function set_deny(array $deny): static
    {
        $this->deny = $deny;
        return $this;
    }
    public function set_tags(array $tags): static
    {
        $this->tags = array_slice($tags, 0, 32);
        // maximum 32 tags allowed
        return $this;
    }
}