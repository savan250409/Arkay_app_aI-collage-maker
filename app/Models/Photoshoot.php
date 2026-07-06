<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photoshoot extends Model
{
    use HasFactory;

    /**
     * One row = one (category, country) group. `country` is a single lowercase
     * code, or '' for the global row (no country). `items` is an array of
     * { name, hashtag, image } entries belonging to that group.
     */
    protected $fillable = ['photoshoot_category_id', 'country', 'items'];

    protected $casts = [
        'items' => 'array',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushPhotoshoots());
        static::deleted(fn () => ApiCache::flushPhotoshoots());
    }

    public function category()
    {
        return $this->belongsTo(PhotoshootCategory::class, 'photoshoot_category_id');
    }

    /**
     * Country options shown in the dropdown.
     *
     * Displayed exactly in this order (matching the design), but always stored
     * in the database in lowercase via config('photoshoot.countries').
     */
    public static function countries(): array
    {
        return config('photoshoot.countries', []);
    }

    /** Sentinel used for the global (no-country) row. */
    public const GLOBAL_COUNTRY = '';
}
