<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FilterCategory;
use Illuminate\Http\Request;

class FilterApiController extends Controller
{
    public function getAllFilters(Request $request)
    {
        try {
            // Fetch categories with filters
            $categories = FilterCategory::where('is_active', 1)->with([
                'filters' => function ($query) {
                    $query->orderBy('id', 'desc');
                }
            ])->orderBy('id', 'desc')->get();

            $full_url = url('upload');

            $categoryData = $categories->map(function ($category) use ($full_url) {
                $categoryThumbnailImage = 'filter_category/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image);

                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'category_thumbnail_image' => $categoryThumbnailImage,
                    'category_thumbnail_image_full_url' => $full_url . '/' . $categoryThumbnailImage,
                    'filters' => $category->filters->map(function ($filter) {
                        return [
                            'id' => $filter->id,
                            'name' => $filter->name,
                            'type' => $filter->type,
                            'saturation' => (double) $filter->saturation,
                            'brightness' => (double) $filter->brightness,
                            'contrast' => (double) $filter->contrast,
                            'red' => (double) $filter->red,
                            'green' => (double) $filter->green,
                            'blue' => (double) $filter->blue,
                        ];
                    }),
                ];
            })->filter(function ($item) {
                return count($item['filters']) > 0;
            })->values();

            return response()->json([
                'status' => true,
                'categories' => $categoryData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }
}
