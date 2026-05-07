<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StickerCategory extends Model
{
    protected $fillable = ['name', 'image', 'is_premium', 'row_order', 'is_active'];

    public function stickers()
    {
        return $this->hasMany(Sticker::class);
    }
}
