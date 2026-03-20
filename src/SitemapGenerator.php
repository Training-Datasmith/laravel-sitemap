<?php

declare (strict_types=1);
namespace Spatie\Sitemap;

use Closure;
use Illuminate\Support\Collection;
use Spatie\Browsershot\Browsershot;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\Crawl_Profiles\Crawl_Profile;
use Spatie\Crawler\Crawl_Response;
use Spatie\Crawler\Java_Script_Renderers\Browsershot_Renderer;
use Spatie\Sitemap\Crawler\Profile;
use Spatie\Sitemap\Tags\Url;
class Sitemap_Generator
{
    protected Collection $sitemaps;
    protected string $url_to_be_crawled;
    /** @var callable */
    protected $should_crawl;
    /** @var callable */
    protected $has_crawled;
    protected ?Closure $configure_crawler_callback = null;
    protected int $concurrency = 10;
    protected int $maximum_tags_per_sitemap = 0;
    protected ?int $maximum_crawl_count = null;
    public static function create(string $url_to_be_crawled): static
    {
        return app(static::class)->set_url($url_to_be_crawled);
    }
    public function __construct()
    {
        $this->sitemaps = new Collection([new Sitemap()]);
        $this->has_crawled = fn(Url $url, ?Crawl_Response $response = null): \Spatie\Sitemap\Tags\Url => $url;
    }
    public function configure_crawler(Closure $closure): static
    {
        $this->configure_crawler_callback = $closure;
        return $this;
    }
    public function set_concurrency(int $concurrency): static
    {
        $this->concurrency = $concurrency;
        return $this;
    }
    public function set_maximum_crawl_count(int $maximum_crawl_count): static
    {
        $this->maximum_crawl_count = $maximum_crawl_count;
        return $this;
    }
    public function max_tags_per_sitemap(int $maximum_tags_per_sitemap = 50000): static
    {
        $this->maximum_tags_per_sitemap = $maximum_tags_per_sitemap;
        return $this;
    }
    public function set_url(string $url_to_be_crawled): static
    {
        $this->url_to_be_crawled = $url_to_be_crawled;
        return $this;
    }
    public function should_crawl(callable $should_crawl): static
    {
        $this->should_crawl = $should_crawl;
        return $this;
    }
    public function has_crawled(callable $has_crawled): static
    {
        $this->has_crawled = $has_crawled;
        return $this;
    }
    public function get_sitemap(): Sitemap
    {
        $crawler = Crawler::create($this->url_to_be_crawled, config('sitemap.guzzle_options', []));
        if (config('sitemap.execute_javascript')) {
            if ($chrome_binary_path = config('sitemap.chrome_binary_path')) {
                $browsershot = new Browsershot();
                $browsershot->set_chrome_path($chrome_binary_path);
                $crawler->execute_java_script(new Browsershot_Renderer($browsershot));
            } else {
                $crawler->execute_java_script();
            }
        }
        if (!is_null($this->maximum_crawl_count)) {
            $crawler->limit($this->maximum_crawl_count);
        }
        $crawler->crawl_profile($this->get_crawl_profile())->concurrency($this->concurrency)->on_crawled(function (string $url, Crawl_Response $response): void {
            $sitemap_url = ($this->has_crawled)(Url::create($url), $response);
            if ($this->should_start_new_sitemap_file()) {
                $this->sitemaps->push(new Sitemap());
            }
            if ($sitemap_url) {
                $this->sitemaps->last()->add($sitemap_url);
            }
        });
        if ($this->configure_crawler_callback) {
            ($this->configure_crawler_callback)($crawler);
        }
        $crawler->start();
        return $this->sitemaps->first();
    }
    public function write_to_file(string $path): static
    {
        $sitemap = $this->get_sitemap();
        if ($this->maximum_tags_per_sitemap) {
            $sitemap = Sitemap_Index::create();
            $file_format = str_replace('.xml', '_%d.xml', $path);
            $url_format = str_replace('.xml', '_%d.xml', $this->to_url_path($path));
            $this->sitemaps->each(function (Sitemap $item, int $key) use ($sitemap, $file_format, $url_format): void {
                $item->write_to_file(sprintf($file_format, $key));
                $sitemap->add(sprintf($url_format, $key));
            });
        }
        $sitemap->write_to_file($path);
        return $this;
    }
    protected function to_url_path(string $file_path): string
    {
        $public_path = rtrim(public_path(), '/') . '/';
        if (str_starts_with($file_path, $public_path)) {
            return '/' . substr($file_path, strlen($public_path));
        }
        return '/' . basename($file_path);
    }
    protected function get_crawl_profile(): Crawl_Profile
    {
        $should_crawl = function (string $url) {
            if (parse_url($url, PHP_URL_HOST) !== parse_url($this->url_to_be_crawled, PHP_URL_HOST)) {
                return false;
            }
            if (!is_callable($this->should_crawl)) {
                return true;
            }
            return ($this->should_crawl)($url);
        };
        $profile_class = config('sitemap.crawl_profile', Profile::class);
        $profile = new $profile_class($this->url_to_be_crawled);
        if (method_exists($profile, 'shouldCrawlCallback')) {
            $profile->should_crawl_callback($should_crawl);
        }
        return $profile;
    }
    protected function should_start_new_sitemap_file(): bool
    {
        if (!$this->maximum_tags_per_sitemap) {
            return false;
        }
        $current_number_of_tags = count($this->sitemaps->last()->get_tags());
        return $current_number_of_tags >= $this->maximum_tags_per_sitemap;
    }
}