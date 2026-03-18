<?php

declare(strict_types=1);

namespace Spatie\Sitemap\Tags;

abstract class Tag
{
    public function getType(): string
    {
        return mb_strtolower(class_basename(static::class));
    }
}
