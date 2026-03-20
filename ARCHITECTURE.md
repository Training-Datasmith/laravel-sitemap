# Architecture: laravel-sitemap

## Purpose
A Laravel package for generating XML sitemaps, sitemap indexes, and crawling a site to build a sitemap automatically. Supports URL, image, video, news, and alternate (hreflang) tags.

## Directory Structure
```
src/
  Sitemap.php               # Fluent sitemap builder — add() URLs, writeToDisk(), toResponse()
  Sitemap_Index.php         # Sitemap index XML builder
  Sitemap_Generator.php     # Crawls a site URL using Guzzle to discover pages automatically
  Sitemap_Service_Provider.php
  Contracts/
    Sitemapable.php         # Interface: toSitemapTag() — Eloquent models can implement this
  Crawler/
    Profile.php             # Spatie Crawler profile — filters crawled URLs
  Tags/
    Tag.php                 # Base class for all sitemap XML elements
    Url.php                 # <url> with loc, lastmod, changefreq, priority
    Image.php               # <image:image> tag
    Video.php               # <video:video> tag
    News.php                # <news:news> tag
    Alternate.php           # <xhtml:link> hreflang alternate tag
    Sitemap.php             # <sitemap> element in a sitemap index
```

## Key Design Decisions
- **Fluent builder** — `Sitemap::create()->add(Url::create('https://...')->...)->writeToDisk('sitemap.xml')`.
- **`Sitemapable` interface** — Eloquent models can implement `toSitemapTag()` returning a `Url` or array of `Url`s, allowing domain objects to declare their own sitemap representation.
- **Crawler-based auto-discovery** — `Sitemap_Generator` uses `spatie/crawler` to recursively visit the site and register all discoverable URLs. The `Profile` class filters which URLs to include.
- **Tag value objects** — each XML element type is a dedicated value object with a fluent builder, keeping construction readable and type-safe.

## Extension Points
- Implement `Sitemapable` on any Eloquent model for model-driven sitemap generation.
- Extend `Profile` to filter crawled URLs (e.g., exclude admin pages, query string URLs).
- Use `Sitemap_Index` to split large sites into multiple sitemap files.

## Dependency Flow
```
Sitemap::create()
  └─ add(Url::create($loc)->lastModified($date)->changeFrequency('weekly'))
  └─ add(Image::create($src)->title('photo'))
  └─ writeToDisk('public/sitemap.xml')
       └─ renders <urlset> XML string → file_put_contents

SitemapGenerator::create($baseUrl)->getSitemap()->writeToDisk(...)
  └─ Crawler → visit all reachable URLs (via Profile filter)
       └─ each URL → Sitemap::add(Url::create($url))
```
