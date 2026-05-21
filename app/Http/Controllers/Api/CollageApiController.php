<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BackgroundCategory;
use App\Models\Doodle;
use App\Models\Font;
use App\Models\StickerCategory;
use App\Support\ApiCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CollageApiController extends Controller
{
    public function get_stickers(Request $request)
    {
        $full_url = url('upload');

        $data = Cache::remember(ApiCache::KEY_STICKERS . '.payload', ApiCache::TTL, function () {
            return StickerCategory::where('is_active', 1)
                ->with(['stickers:id,sticker_category_id,images'])
                ->orderBy('row_order', 'ASC')
                ->get(['id', 'name', 'image', 'is_premium', 'row_order'])
                ->map(function ($category) {
                    $allStickers = [];
                    foreach ($category->stickers as $sticker) {
                        if (is_array($sticker->images)) {
                            foreach ($sticker->images as $img) {
                                $allStickers[] = 'sticker/' . rawurlencode($category->name) . '/stickers/' . rawurlencode($img);
                            }
                        }
                    }

                    return [
                        'id'         => $category->id,
                        'name'       => $category->name,
                        'type'       => $category->is_premium ? 'pro' : 'free',
                        '_thumbnail' => 'sticker/' . rawurlencode($category->name) . '/category image/' . rawurlencode($category->image),
                        '_stickers'  => $allStickers,
                    ];
                })
                ->filter(fn ($item) => count($item['_stickers']) > 0)
                ->values()
                ->all();
        });

        $data = array_map(function ($category) use ($full_url) {
            return [
                'id'                  => $category['id'],
                'name'                => $category['name'],
                'type'                => $category['type'],
                'thumbnail_full_url'  => $full_url . '/' . $category['_thumbnail'],
                'stickers_full_url'   => array_map(fn ($p) => $full_url . '/' . $p, $category['_stickers']),
            ];
        }, $data);

        return response()->json([
            'status' => true,
            'message' => 'Stickers fetched successfully',
            'data' => $data,
        ]);
    }

    public function get_fonts(Request $request)
    {
        $full_url = url('upload');

        $data = Cache::remember(ApiCache::KEY_FONTS . '.payload', ApiCache::TTL, function () {
            return Font::select(['id', 'name', 'type', 'font_preview', 'file'])
                ->get()
                ->map(function ($font) {
                    return [
                        'id'             => $font->id,
                        'name'           => $font->name,
                        'type'           => $font->type,
                        '_font_preview'  => $font->font_preview ? 'font/' . rawurlencode($font->name) . '/' . rawurlencode($font->font_preview) : null,
                        '_file_url'      => 'font/' . rawurlencode($font->name) . '/' . rawurlencode($font->file),
                    ];
                })
                ->all();
        });

        $data = array_map(function ($font) use ($full_url) {
            return [
                'id'                      => $font['id'],
                'name'                    => $font['name'],
                'type'                    => $font['type'],
                'font_preview_full_url'   => $font['_font_preview'] ? $full_url . '/' . $font['_font_preview'] : null,
                'file_url_full_url'       => $full_url . '/' . $font['_file_url'],
            ];
        }, $data);

        return response()->json([
            'status' => true,
            'message' => 'Fonts fetched successfully',
            'data' => $data,
        ]);
    }

    public function get_doodles(Request $request)
    {
        $full_url = url('upload');

        $data = Cache::remember(ApiCache::KEY_DOODLES . '.payload', ApiCache::TTL, function () {
            return Doodle::select(['id', 'name', 'type', 'doodle_type', 'image', 'row_order'])
                ->orderBy('row_order', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->map(function ($doodle) {
                    $image = null;
                    if ($doodle->image) {
                        $decoded = json_decode($doodle->image, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $imgOne = $decoded[0] ?? null;
                            if ($imgOne) {
                                $image = 'doodle/' . rawurlencode($doodle->name) . '/' . rawurlencode($imgOne);
                            }
                        } else {
                            $image = 'doodle/' . rawurlencode($doodle->name) . '/' . rawurlencode($doodle->image);
                        }
                    }

                    return [
                        'id'          => $doodle->id,
                        'name'        => $doodle->name,
                        'type'        => $doodle->type,
                        'doodle_type' => $doodle->doodle_type,
                        '_image'      => $image,
                    ];
                })
                ->all();
        });

        $data = array_map(function ($doodle) use ($full_url) {
            return [
                'id'              => $doodle['id'],
                'name'            => $doodle['name'],
                'type'            => $doodle['type'],
                'doodle_type'     => $doodle['doodle_type'],
                'image_full_url'  => $doodle['_image'] ? $full_url . '/' . $doodle['_image'] : null,
            ];
        }, $data);

        return response()->json([
            'status' => true,
            'message' => 'Doodles fetched successfully',
            'data' => $data,
        ]);
    }

    public function get_backgrounds(Request $request)
    {
        $full_url = url('upload');

        $data = Cache::remember(ApiCache::KEY_BACKGROUNDS . '.payload', ApiCache::TTL, function () {
            return BackgroundCategory::where('is_active', 1)
                ->with(['backgrounds:id,background_category_id,images'])
                ->orderBy('row_order', 'ASC')
                ->get(['id', 'name', 'image', 'is_premium', 'row_order'])
                ->map(function ($category) {
                    $items = [];
                    foreach ($category->backgrounds as $background) {
                        if (is_array($background->images)) {
                            foreach ($background->images as $item) {
                                $imgName    = is_array($item) ? $item['image']                  : $item;
                                $imgPremium = is_array($item) ? ($item['is_premium'] ?? 0)      : 0;
                                $items[] = [
                                    'path'       => 'background/' . rawurlencode($category->name) . '/backgrounds/' . rawurlencode($imgName),
                                    'is_premium' => $imgPremium,
                                ];
                            }
                        }
                    }

                    return [
                        'id'           => $category->id,
                        'name'         => $category->name,
                        'type'         => $category->is_premium ? 'pro' : 'free',
                        '_thumbnail'   => 'background/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image),
                        '_backgrounds' => $items,
                    ];
                })
                ->filter(fn ($item) => count($item['_backgrounds']) > 0)
                ->values()
                ->all();
        });

        $data = array_map(function ($category) use ($full_url) {
            return [
                'id'                   => $category['id'],
                'name'                 => $category['name'],
                'type'                 => $category['type'],
                'thumbnail_full_url'   => $full_url . '/' . $category['_thumbnail'],
                'backgrounds_full_url' => array_map(
                    fn ($b) => ['path' => $full_url . '/' . $b['path'], 'is_premium' => $b['is_premium']],
                    $category['_backgrounds']
                ),
            ];
        }, $data);

        return response()->json([
            'status' => true,
            'message' => 'Backgrounds fetched successfully',
            'data' => $data,
        ]);
    }
}
