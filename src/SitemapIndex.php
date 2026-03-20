<?php

declare (strict_types=1);
namespace Spatie\Sitemap;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Tags\Sitemap;
use Spatie\Sitemap\Tags\Tag;
use Symfony\Component\Http_Foundation\Response as SymfonyResponse;
class Sitemap_Index implements Renderable, Responsable
{
    /** @var Sitemap[] */
    protected array $tags = [];
    protected ?string $stylesheet_url = null;
    public static function create(): static
    {
        return new static();
    }
    public function set_stylesheet(string $url): static
    {
        $this->stylesheet_url = $url;
        return $this;
    }
    public function add(string|Sitemap $tag): static
    {
        if (is_string($tag)) {
            $tag = Sitemap::create($tag);
        }
        $this->tags[] = $tag;
        return $this;
    }
    public function get_sitemap(string $url): ?Sitemap
    {
        return collect($this->tags)->first(fn(Tag $tag) => $tag->get_type() === 'sitemap' && $tag->url === $url);
    }
    public function has_sitemap(string $url): bool
    {
        return (bool) $this->get_sitemap($url);
    }
    public function render(): string
    {
        $tags = $this->tags;
        $stylesheet_url = $this->stylesheet_url;
        return view('sitemap::sitemapIndex/index')->with(compact('tags', 'stylesheetUrl'))->render();
    }
    public function write_to_file(string $path): static
    {
        file_put_contents($path, $this->render());
        return $this;
    }
    public function write_to_disk(string $disk, string $path, bool $public = false): static
    {
        $visibility = $public ? 'public' : 'private';
        Storage::disk($disk)->put($path, $this->render(), $visibility);
        return $this;
    }
    public function to_response($request): Symfony_Response
    {
        return Response::make($this->render(), 200, ['Content-Type' => 'text/xml']);
    }
}