<?php

declare (strict_types=1);
namespace Spatie\Sitemap;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Tag;
use Spatie\Sitemap\Tags\Url;
use Symfony\Component\Http_Foundation\Response as SymfonyResponse;
class Sitemap implements Renderable, Responsable
{
    /** @var Url[] */
    protected array $tags = [];
    protected int $maximum_tags_per_sitemap = 0;
    protected ?string $stylesheet_url = null;
    public static function create(): static
    {
        return new static();
    }
    public function max_tags_per_sitemap(int $maximum_tags_per_sitemap = 50000): static
    {
        $this->maximum_tags_per_sitemap = $maximum_tags_per_sitemap;
        return $this;
    }
    public function set_stylesheet(string $url): static
    {
        $this->stylesheet_url = $url;
        return $this;
    }
    public function add(string|Url|Sitemapable|iterable $tag): static
    {
        if (is_object($tag) && array_key_exists(Sitemapable::class, class_implements($tag))) {
            $tag = $tag->to_sitemap_tag();
        }
        if (is_iterable($tag)) {
            foreach ($tag as $item) {
                $this->add($item);
            }
            return $this;
        }
        if (is_string($tag) && trim($tag) === '') {
            return $this;
        }
        if (is_string($tag)) {
            $tag = Url::create($tag);
        }
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        return $this;
    }
    public function get_tags(): array
    {
        return $this->tags;
    }
    public function get_url(string $url): ?Url
    {
        return collect($this->tags)->first(fn(Tag $tag) => $tag->get_type() === 'url' && $tag->url === $url);
    }
    public function has_url(string $url): bool
    {
        return (bool) $this->get_url($url);
    }
    public function render(): string
    {
        $tags = collect($this->tags)->unique('url')->filter();
        $stylesheet_url = $this->stylesheet_url;
        return view('sitemap::sitemap')->with(compact('tags', 'stylesheetUrl'))->render();
    }
    public function write_to_file(string $path): static
    {
        if (!$this->should_split()) {
            file_put_contents($path, $this->render());
            return $this;
        }
        foreach ($this->build_split_sitemaps($path, basename($path)) as $file_path => $xml) {
            file_put_contents($file_path, $xml);
        }
        return $this;
    }
    public function write_to_disk(string $disk, string $path, bool $public = false): static
    {
        $visibility = $public ? 'public' : 'private';
        if (!$this->should_split()) {
            Storage::disk($disk)->put($path, $this->render(), $visibility);
            return $this;
        }
        foreach ($this->build_split_sitemaps($path) as $file_path => $xml) {
            Storage::disk($disk)->put($file_path, $xml, $visibility);
        }
        return $this;
    }
    /**
     * @return array<string, string> Map of file paths to rendered XML content.
     *                               The index sitemap is keyed by the original path.
     */
    protected function build_split_sitemaps(string $path, ?string $url_path = null): array
    {
        $url_path ??= $path;
        $index = new Sitemap_Index();
        if ($this->stylesheet_url) {
            $index->set_stylesheet($this->stylesheet_url);
        }
        $file_format = str_replace('.xml', '_%d.xml', $path);
        $url_format = str_replace('.xml', '_%d.xml', $url_path);
        $files = [];
        foreach ($this->chunk_tags() as $key => $chunk) {
            $chunk_sitemap = Sitemap::create();
            if ($this->stylesheet_url) {
                $chunk_sitemap->set_stylesheet($this->stylesheet_url);
            }
            foreach ($chunk as $tag) {
                $chunk_sitemap->add($tag);
            }
            $chunk_file_path = sprintf($file_format, $key);
            $files[$chunk_file_path] = $chunk_sitemap->render();
            $index->add(sprintf($url_format, $key));
        }
        $files[$path] = $index->render();
        return $files;
    }
    protected function should_split(): bool
    {
        return $this->maximum_tags_per_sitemap > 0 && count($this->tags) > $this->maximum_tags_per_sitemap;
    }
    protected function chunk_tags(): array
    {
        return collect($this->tags)->unique('url')->filter()->chunk($this->maximum_tags_per_sitemap)->to_array();
    }
    public function to_response($request): Symfony_Response
    {
        return Response::make($this->render(), 200, ['Content-Type' => 'text/xml']);
    }
    public function sort(): static
    {
        sort($this->tags);
        return $this;
    }
}