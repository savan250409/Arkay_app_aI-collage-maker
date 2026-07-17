<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Photoshoot;
use App\Models\PhotoshootCategory;
use App\Support\ApiCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PhotoshootApiController extends Controller
{
    /**
     * Get all active photoshoot categories (image + name).
     */
    public function get_photoshoot_category()
    {
        $full_url = url('upload');

        $data = Cache::remember(ApiCache::KEY_PHOTOSHOOT_CATEGORIES . '.payload', ApiCache::TTL, function () {
            return PhotoshootCategory::where('is_active', 1)
                ->orderBy('sort_order')
                ->orderBy('id', 'DESC')
                ->get(['id', 'name', 'image'])
                ->map(function ($category) {
                    return [
                        'id'         => $category->id,
                        'name'       => $category->name,
                        '_thumbnail' => 'photoshoot/' . rawurlencode($category->name) . '/category image/' . rawurlencode($category->image),
                    ];
                })
                ->values()
                ->all();
        });

        $data = array_map(function ($category) use ($full_url) {
            return [
                'id'                 => $category['id'],
                'name'               => $category['name'],
                'thumbnail_full_url' => $full_url . '/' . $category['_thumbnail'],
            ];
        }, $data);

        return response()->json([
            'status' => true,
            'message' => 'Categories fetched successfully',
            'data' => $data,
        ], 200);
    }

    /**
     * Get photoshoots inside a category (paginated).
     *
     * Params:
     *  - category_id (required)
     *  - country     (optional) lowercase code. When supplied, that country's
     *                items are returned first, followed by the global (no
     *                country) items. When omitted, only the global items.
     *  - page        (optional) page number. Page size comes from
     *                config('photoshoot.per_page') (.env PHOTOSHOOT_API_PER_PAGE).
     */
    public function get_photoshoot_by_category_id(Request $request)
    {
        $full_url = url('upload');

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:photoshoot_categories,id',
            'country'     => 'nullable|string|max:5',
            'page'        => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = PhotoshootCategory::find($request->category_id, ['id', 'name', 'is_active']);

        if (!$category || !$category->is_active) {
            return response()->json([
                'status' => false,
                'message' => 'Category not found or inactive',
                'data' => null,
            ], 404);
        }

        $country = $request->filled('country') ? strtolower(trim($request->country)) : null;

        // Pull only the group rows we need (country row first, then global).
        $groups = Photoshoot::where('photoshoot_category_id', $category->id)
            ->get(['country', 'items'])
            ->keyBy('country');

        $ordered = [];
        if ($country) {
            if (isset($groups[$country])) {
                $ordered[] = $groups[$country];
            }
        }
        if (isset($groups[Photoshoot::GLOBAL_COUNTRY])) {
            $ordered[] = $groups[Photoshoot::GLOBAL_COUNTRY];
        }

        // Flatten the items in group order.
        $all = [];
        foreach ($ordered as $group) {
            $code = $group->country === Photoshoot::GLOBAL_COUNTRY ? null : $group->country;
            foreach ((array) $group->items as $item) {
                $all[] = [
                    'name'           => $item['name'] ?? '',
                    'hashtag'        => $item['hashtag'] ?? '',
                    'country'        => $code,
                    'image_full_url' => $full_url . '/photoshoot/' . rawurlencode($category->name) . '/photoshoots/' . rawurlencode($item['image'] ?? ''),
                ];
            }
        }

        // Paginate the flattened array.
        $perPage = (int) config('photoshoot.per_page', 20);
        $total = count($all);
        $page = max(1, (int) $request->input('page', 1));
        $lastPage = $total > 0 ? (int) ceil($total / $perPage) : 1;
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        return response()->json([
            'status' => true,
            'message' => 'Category photoshoots fetched successfully',
            'category' => [
                'id'   => $category->id,
                'name' => $category->name,
            ],
            'pagination' => [
                'current_page' => $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => $lastPage,
                'has_more'     => $page < $lastPage,
            ],
            'data' => array_values($items),
        ], 200);
    }
}
