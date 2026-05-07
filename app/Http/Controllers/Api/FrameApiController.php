<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FrameCategory;
use App\Models\Frame;
use Illuminate\Support\Facades\Validator;

class FrameApiController extends Controller
{
    public function get_frame_category()
    {
        $full_url = url('upload');

        $categories = FrameCategory::where('is_active', 1)
            ->orderBy('row_order', 'DESC')
            ->with([
                'frames' => function ($query) {
                    $query->orderBy('row_order', 'DESC');
                }
            ])->get();

        $data = $categories->map(function ($category) use ($full_url) {
            $last6Images = [];

            foreach ($category->frames as $frame) {
                if (is_array($frame->images)) {
                    $images = array_reverse($frame->images);
                    $types = array_reverse($frame->image_types ?? []);
                    $thumbnails = array_reverse($frame->frame_thumbnail ?? []);

                    foreach ($images as $key => $img) {
                        if (count($last6Images) >= 6) {
                            break 2;
                        }

                        if (!is_string($img)) {
                            continue;
                        }

                        $counts = array_reverse($frame->image_input_counts ?? []);
                        $relativePath = 'frame/' . rawurlencode($category->name) . '/frame/' . rawurlencode($img);
                        $thumbRelative = isset($thumbnails[$key]) ? 'frame/' . rawurlencode($category->name) . '/frame_thumbnail_image/' . rawurlencode($thumbnails[$key]) : null;

                        $last6Images[] = [
                            'url' => $relativePath,
                            'url_full_url' => $full_url . '/' . $relativePath,
                            'type' => $types[$key] ?? 'free',
                            'image_input_count' => $counts[$key] ?? 1,
                            'frame_thumbnail' => $thumbRelative,
                            'frame_thumbnail_full_url' => $thumbRelative ? $full_url . '/' . $thumbRelative : null,
                        ];
                    }
                }
            }

            $thumbnail = 'frame/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image);

            return [
                'id' => $category->id,
                'name' => $category->name,
                'thumbnail' => $thumbnail,
                'thumbnail_full_url' => $full_url . '/' . $thumbnail,
                'frames' => $last6Images
            ];
        })->filter(function ($item) {
            return count($item['frames']) > 0;
        })->values();

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $data
        ], 200);
    }

    public function get_frame_by_category_id(Request $request)
    {
        $full_url = url('upload');

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:frame_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = FrameCategory::with([
            'frames' => function ($query) {
                $query->orderBy('row_order', 'DESC');
            }
        ])
            ->where('id', $request->category_id)
            ->where('is_active', 1)
            ->first();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found or inactive',
                'data' => null
            ], 404);
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
                    $thumbRelative = isset($thumbnails[$key]) ? 'frame/' . rawurlencode($category->name) . '/frame_thumbnail_image/' . rawurlencode($thumbnails[$key]) : null;

                    $allImages[] = [
                        'url' => $relativePath,
                        'url_full_url' => $full_url . '/' . $relativePath,
                        'type' => $frame->image_types[$key] ?? 'free',
                        'image_input_count' => $frame->image_input_counts[$key] ?? 1,
                        'frame_thumbnail' => $thumbRelative,
                        'frame_thumbnail_full_url' => $thumbRelative ? $full_url . '/' . $thumbRelative : null,
                    ];
                }
            }
        }

        $thumbnail = 'frame/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image);

        return response()->json([
            'status' => true,
            'message' => 'Category frames fetched successfully',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'thumbnail' => $thumbnail,
                'thumbnail_full_url' => $full_url . '/' . $thumbnail,
                'frames' => $allImages
            ]
        ], 200);
    }
}
