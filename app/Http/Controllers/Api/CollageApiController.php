<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StickerCategory;
use App\Models\BackgroundCategory;
use App\Models\Doodle;
use App\Models\Font;

class CollageApiController extends Controller
{
    public function get_stickers(Request $request)
    {
        $full_url = url('upload');

        $categories = StickerCategory::where('is_active', 1)->with('stickers')->orderBy('row_order', 'ASC')->get();

        $data = $categories->map(function ($category) use ($full_url) {
            $allStickers = [];
            $allStickersFullUrl = [];

            foreach ($category->stickers as $sticker) {
                if (is_array($sticker->images)) {
                    foreach ($sticker->images as $img) {
                        $relative = 'sticker/' . rawurlencode($category->name) . '/stickers/' . rawurlencode($img);
                        $allStickers[] = $relative;
                        $allStickersFullUrl[] = $full_url . '/' . $relative;
                    }
                }
            }

            $thumbnail = 'sticker/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image);

            return [
                'id' => $category->id,
                'name' => $category->name,
                'type' => $category->is_premium ? 'pro' : 'free',
                'thumbnail' => $thumbnail,
                'thumbnail_full_url' => $full_url . '/' . $thumbnail,
                'stickers' => $allStickers,
                'stickers_full_url' => $allStickersFullUrl,
            ];
        })->filter(function ($item) {
            return count($item['stickers']) > 0;
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Stickers fetched successfully',
            'data' => $data
        ]);
    }

    public function get_fonts(Request $request)
    {
        $full_url = url('upload');

        $fonts = Font::all();

        $data = $fonts->map(function ($font) use ($full_url) {
            $fontPreview = $font->font_preview ? 'font/' . rawurlencode($font->name) . '/' . rawurlencode($font->font_preview) : null;
            $fileUrl = 'font/' . rawurlencode($font->name) . '/' . rawurlencode($font->file);

            return [
                'id' => $font->id,
                'name' => $font->name,
                'type' => $font->type,
                'font_preview' => $fontPreview,
                'font_preview_full_url' => $fontPreview ? $full_url . '/' . $fontPreview : null,
                'file_url' => $fileUrl,
                'file_url_full_url' => $full_url . '/' . $fileUrl,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Fonts fetched successfully',
            'data' => $data
        ]);
    }

    public function get_doodles(Request $request)
    {
        $full_url = url('upload');

        $doodles = Doodle::orderBy('row_order', 'ASC')->orderBy('id', 'ASC')->get();

        $data = $doodles->map(function ($doodle) use ($full_url) {
            $image = null;
            // Check if it's a JSON string (legacy) or simple string
            if ($doodle->image) {
                $decoded = json_decode($doodle->image, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Legacy array support: take first image
                    $imgOne = $decoded[0] ?? null;
                    if ($imgOne) {
                        $image = 'doodle/' . rawurlencode($doodle->name) . '/' . rawurlencode($imgOne);
                    }
                } else {
                    // New single string format
                    $image = 'doodle/' . rawurlencode($doodle->name) . '/' . rawurlencode($doodle->image);
                }
            }

            return [
                'id' => $doodle->id,
                'name' => $doodle->name,
                'type' => $doodle->type,
                'doodle_type' => $doodle->doodle_type,
                'image' => $image,
                'image_full_url' => $image ? $full_url . '/' . $image : null,
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'Doodles fetched successfully',
            'data' => $data
        ]);
    }

    public function get_backgrounds(Request $request)
    {
        $full_url = url('upload');

        $categories = BackgroundCategory::where('is_active', 1)->with('backgrounds')->orderBy('row_order', 'ASC')->get();

        $data = $categories->map(function ($category) use ($full_url) {
            $allBackgrounds = [];
            $allBackgroundsFullUrl = [];

            foreach ($category->backgrounds as $background) {
                if (is_array($background->images)) {
                    foreach ($background->images as $item) {
                        $imgName    = is_array($item) ? $item['image']       : $item;
                        $imgPremium = is_array($item) ? ($item['is_premium'] ?? 0) : 0;
                        $relative = 'background/' . rawurlencode($category->name) . '/backgrounds/' . rawurlencode($imgName);
                        $allBackgrounds[] = ['path' => $relative, 'is_premium' => $imgPremium];
                        $allBackgroundsFullUrl[] = ['path' => $full_url . '/' . $relative, 'is_premium' => $imgPremium];
                    }
                }
            }

            $thumbnail = 'background/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image);

            return [
                'id'                   => $category->id,
                'name'                 => $category->name,
                'type'                 => $category->is_premium ? 'pro' : 'free',
                'thumbnail'            => $thumbnail,
                'thumbnail_full_url'   => $full_url . '/' . $thumbnail,
                'backgrounds'          => $allBackgrounds,
                'backgrounds_full_url' => $allBackgroundsFullUrl,
            ];
        })->filter(function ($item) {
            return count($item['backgrounds']) > 0;
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Backgrounds fetched successfully',
            'data' => $data
        ]);
    }

}
