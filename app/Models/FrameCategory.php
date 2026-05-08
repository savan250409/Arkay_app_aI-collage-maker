<?php

namespace App\Models;

use App\Support\ApiCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FrameCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'is_active', 'row_order'];

    protected static function booted(): void
    {
        static::saved(fn () => ApiCache::flushFrames());
        static::deleted(fn () => ApiCache::flushFrames());
    }

    public function frames()
    {
        return $this->hasMany(Frame::class);
    }
}
