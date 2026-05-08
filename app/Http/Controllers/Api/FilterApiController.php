<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FilterCategory;
use App\Support\ApiCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FilterApiController extends Controller
{
    public function getAllFilters(Request $request)
    {
        try {
            $full_url = url('upload');

            $categoryData = Cache::remember(ApiCache::KEY_FILTERS . '.payload', ApiCache::TTL, function () {
                return FilterCategory::where('is_active', 1)
                    ->with(['filters' => function ($query) {
                        $query->orderBy('id', 'desc')
                            ->select(['id', 'filter_category_id', 'name', 'type', 'saturation', 'brightness', 'contrast', 'red', 'green', 'blue']);
                    }])
                    ->orderBy('id', 'desc')
                    ->get(['id', 'name', 'image', 'is_active'])
                    ->map(function ($category) {
                        return [
                            'id'                       => $category->id,
                            'name'                     => $category->name,
                            '_category_thumbnail_image' => 'filter_category/' . rawurlencode($category->name) . '/category-thumbnail-image/' . rawurlencode($category->image),
                            'filters' => $category->filters->map(function ($filter) {
                                return [
                                    'id'         => $filter->id,
                                    'name'       => $filter->name,
                                    'type'       => $filter->type,
                                    'saturation' => (double) $filter->saturation,
                                    'brightness' => (double) $filter->brightness,
                                    'contrast'   => (double) $filter->contrast,
                                    'red'        => (double) $filter->red,
                                    'green'      => (double) $filter->green,
                                    'blue'       => (double) $filter->blue,
                                ];
                            })->all(),
                        ];
                    })
                    ->filter(fn ($item) => count($item['filters']) > 0)
                    ->values()
                    ->all();
            });

            $categoryData = array_map(function ($category) use ($full_url) {
                return [
                    'id'                                  => $category['id'],
                    'name'                                => $category['name'],
                    'category_thumbnail_image_full_url'   => $full_url . '/' . $category['_category_thumbnail_image'],
                    'filters'                             => $category['filters'],
                ];
            }, $categoryData);

            return response()->json([
                'status' => true,
                'categories' => $categoryData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}
