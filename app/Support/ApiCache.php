<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

class ApiCache
{
    public const TTL = 600;

    public const KEY_STICKERS          = 'api.stickers';
    public const KEY_FONTS             = 'api.fonts';
    public const KEY_DOODLES           = 'api.doodles';
    public const KEY_BACKGROUNDS       = 'api.backgrounds';
    public const KEY_FRAME_CATEGORIES  = 'api.frame_categories';
    public const KEY_FILTERS           = 'api.filters';
    public const KEY_FRAMES_VERSION    = 'api.frames.version';

    public static function frameByCategoryKey(int $categoryId): string
    {
        $version = (int) Cache::get(self::KEY_FRAMES_VERSION, 0);
        return "api.frames.v{$version}.{$categoryId}";
    }

    public static function flushStickers(): void
    {
        Cache::forget(self::KEY_STICKERS . '.payload');
    }

    public static function flushFonts(): void
    {
        Cache::forget(self::KEY_FONTS . '.payload');
    }

    public static function flushDoodles(): void
    {
        Cache::forget(self::KEY_DOODLES . '.payload');
    }

    public static function flushBackgrounds(): void
    {
        Cache::forget(self::KEY_BACKGROUNDS . '.payload');
    }

    public static function flushFilters(): void
    {
        Cache::forget(self::KEY_FILTERS);
    }

    public static function flushFrames(): void
    {
        Cache::forget(self::KEY_FRAME_CATEGORIES);
        $current = (int) Cache::get(self::KEY_FRAMES_VERSION, 0);
        Cache::forever(self::KEY_FRAMES_VERSION, $current + 1);
    }
}
