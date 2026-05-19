<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FrameCategory;
use App\Support\ApiCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class FrameApiController extends Controller
{
    public function get_frame_category()
    {
        $full_url = url('upload');

        $data = Cache::remember(ApiCache::KEY_FRAME_CATEGORIES . '.payload', ApiCache::TTL, function () {
            return FrameCategory::where('is_active', 1)
                ->with(['frames' => function ($query) {
                    $query->orderBy('row_order', 'DESC')
                        ->select(['id', 'frame_category_id', 'images', 'image_types', 'image_input_counts', 'frame_thumbnail', 'row_order']);
                }])
                ->orderBy('row_order', 'DESC')
                ->get(['id', 'name', 'image', 'is_active', 'row_order'])
                ->map(function ($category) {
                    $last6Images = [];

                    foreach ($category->frames as $frame) {
                        if (is_array($frame->images)) {
                            $images = array_reverse($frame->images);
                            $types = array_reverse($frame->image_types ?? []);
                            $thumbnails = array_reverse($frame->frame_thumbnail ?? []);
                            $counts = array_reverse($frame->image_input_counts ?? []);

                            foreach ($images as $key => $img) {
                                if (count($last6Images) >= 6) {
                                    break 2;
                                }
                                if (!is_string($img)) {
                                    continue;
                                }

                                $relativePath = 'frame/' . rawurlencode($category->name) . '/frame/' . rawurlencode($img);
                                $thumbRelative = isset($thumbnails[$key])
                                    ? 'frame/' . rawurlencode($category->name) . '/frame_thumbnail_image/' . rawurlencode($thumbnails[$key])
                                    : null;

                                $last6Images[] = [
                                    '_url'              => $relativePath,
                                    'type'              => $types[$key] ?? 'free',
                                    'image_input_count' => $counts[$key] ?? 1,
                                    '_frame_thumbnail'  => $thumbRelative,
                                ];
                            }
                        }
                    }

                    return [
                        'id'         => $category->id,
                        'name'       => $category->name,
                        '_thumbnail' => 'frame/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image),
                        '_frames'    => $last6Images,
                    ];
                })
                ->filter(fn ($item) => count($item['_frames']) > 0)
                ->values()
                ->all();
        });

        $data = array_map(function ($category) use ($full_url) {
            return [
                'id'                  => $category['id'],
                'name'                => $category['name'],
                'thumbnail_full_url'  => $full_url . '/' . $category['_thumbnail'],
                'frames'              => array_map(function ($f) use ($full_url) {
                    return [
                        'url_full_url'              => $full_url . '/' . $f['_url'],
                        'type'                      => $f['type'],
                        'image_input_count'         => $f['image_input_count'],
                        'frame_thumbnail_full_url'  => $f['_frame_thumbnail'] ? $full_url . '/' . $f['_frame_thumbnail'] : null,
                    ];
                }, $category['_frames']),
            ];
        }, $data);

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $data,
        ], 200);
    }

    public function get_frame_by_category_id(Request $request)
    {
        $full_url = url('upload');

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:frame_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $categoryId = (int) $request->category_id;

        $cached = Cache::remember(ApiCache::frameByCategoryKey($categoryId), ApiCache::TTL, function () use ($categoryId) {
            $category = FrameCategory::with(['frames' => function ($query) {
                $query->orderBy('row_order', 'DESC')
                    ->select(['id', 'frame_category_id', 'images', 'image_types', 'image_input_counts', 'frame_thumbnail', 'row_order']);
            }])
                ->where('id', $categoryId)
                ->where('is_active', 1)
                ->first(['id', 'name', 'image', 'is_active']);

            if (!$category) {
                return null;
            }

            $allImages = [];
            foreach ($category->frames as $frame) {
                if (is_array($frame->images)) {
                    $thumbnails = $frame->frame_thumbnail ?? [];

                    foreach ($frame->images as $key => $img) {
                        if (!is_string($img)) {
                            continue;
                        }
                        $relativePath = 'frame/' . rawurlencode($category->name) . '/frame/' . rawurlencode($img);
                        $thumbRelative = isset($thumbnails[$key])
                            ? 'frame/' . rawurlencode($category->name) . '/frame_thumbnail_image/' . rawurlencode($thumbnails[$key])
                            : null;

                        $allImages[] = [
                            '_url'              => $relativePath,
                            'type'              => $frame->image_types[$key] ?? 'free',
                            'image_input_count' => $frame->image_input_counts[$key] ?? 1,
                            '_frame_thumbnail'  => $thumbRelative,
                        ];
                    }
                }
            }

            return [
                'id'         => $category->id,
                'name'       => $category->name,
                '_thumbnail' => 'frame/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image),
                '_frames'    => $allImages,
            ];
        });

        if ($cached === null) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found or inactive',
                'data' => null,
            ], 404);
        }

        if (empty($cached['_frames'])) {
            return response()->json([
                'status' => false,
                'message' => 'No frames found for this category',
                'data' => null,
            ], 404);
        }

        $payload = [
            'id'                  => $cached['id'],
            'name'                => $cached['name'],
            'thumbnail_full_url'  => $full_url . '/' . $cached['_thumbnail'],
            'frames'              => array_map(function ($f) use ($full_url) {
                return [
                    'url_full_url'              => $full_url . '/' . $f['_url'],
                    'type'                      => $f['type'],
                    'image_input_count'         => $f['image_input_count'],
                    'frame_thumbnail_full_url'  => $f['_frame_thumbnail'] ? $full_url . '/' . $f['_frame_thumbnail'] : null,
                ];
            }, $cached['_frames']),
        ];

        return response()->json([
            'status' => true,
            'message' => 'Category frames fetched successfully',
            'data' => $payload,
        ], 200);
    }
}
