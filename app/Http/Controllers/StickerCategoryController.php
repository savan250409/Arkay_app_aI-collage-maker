<?php

namespace App\Http\Controllers;

use App\Models\Sticker;
use App\Models\StickerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StickerCategoryController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $search = $request->search;
        $perPage = $request->input('per_page', 10);

        if (!$request->ajax() && session('restore_sticker_cat_state') && session()->has('sticker_cat_state')) {
            $state = session('sticker_cat_state');
            $page = $state['page'] ?? 1;
            $search = $state['search'] ?? '';
            $perPage = $state['per_page'] ?? 10;

            \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        } elseif ($request->ajax()) {
            session([
                'sticker_cat_state' => [
                    'page' => $request->page,
                    'search' => $request->search,
                    'per_page' => $request->per_page
                ]
            ]);
        }

        $query = StickerCategory::query();
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        $categories = $query->orderBy('row_order', 'ASC')->paginate($perPage);
        $categories->appends(['search' => $search, 'per_page' => $perPage]);

        if ($request->ajax()) {
            return view('admin.sticker_category.index', compact('categories'))->render();
        }

        return view('admin.sticker_category.index', compact('categories', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.sticker_category.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sticker_categories,name',
            'image' => 'required|image|mimes:webp'
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = $request->name;
        $imageName = $request->file('image')->getClientOriginalName();
        $path = public_path('upload/sticker/' . $categoryName . '/category-thumbnail-image');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $request->file('image')->move($path, $imageName);

        StickerCategory::create([
            'name' => $categoryName,
            'image' => $imageName,
            'is_premium' => $request->has('is_premium') ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'row_order' => StickerCategory::max('row_order') + 1
        ]);

        return redirect()->route('sticker-categories.index')->with('success', 'Category created successfully.')->with('restore_sticker_cat_state', true);
    }

    public function edit(StickerCategory $stickerCategory)
    {
        return view('admin.sticker_category.form', compact('stickerCategory'));
    }

    public function update(Request $request, StickerCategory $stickerCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:sticker_categories,name,' . $stickerCategory->id,
        ]);

        $categoryName = $request->name;
        $newPath = public_path('upload/sticker/' . $categoryName . '/category-thumbnail-image');

        $imageName = $stickerCategory->image;

        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();

            $oldPath = public_path('upload/sticker/' . $stickerCategory->name . '/category-thumbnail-image');
            $oldImagePath = $oldPath . '/' . $stickerCategory->image;

            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            if (!File::exists($newPath)) {
                File::makeDirectory($newPath, 0777, true, true);
            }

            $request->file('image')->move($newPath, $imageName);
        }

        $stickerCategory->update([
            'name' => $categoryName,
            'image' => $imageName ?? $stickerCategory->image,
            'is_premium' => $request->has('is_premium') ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('sticker-categories.index')->with('success', 'Category updated successfully.')->with('restore_sticker_cat_state', true);
    }

    public function destroy(StickerCategory $stickerCategory)
    {
        $stickers = Sticker::where('sticker_category_id', $stickerCategory->id)->get();
        foreach ($stickers as $sticker) {
            if (!empty($sticker->images)) {
                foreach ($sticker->images as $img) {
                    $stickerPath = public_path('upload/sticker/' . $stickerCategory->name . '/stickers/' . $img);
                    if (File::exists($stickerPath)) {
                        File::delete($stickerPath);
                    }
                }
            }
            $sticker->delete();
        }

        $thumbnailPath = public_path('upload/sticker/' . $stickerCategory->name . '/category-thumbnail-image/' . $stickerCategory->image);
        if (File::exists($thumbnailPath)) {
            File::delete($thumbnailPath);
        }
        $categoryFolder = public_path('upload/sticker/' . $stickerCategory->name);
        if (File::exists($categoryFolder)) {
            File::deleteDirectory($categoryFolder);
        }

        $stickerCategory->delete();
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $category = StickerCategory::find($request->id);
        $category->is_premium = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }

    public function updateActiveStatus(Request $request)
    {
        $category = StickerCategory::find($request->id);
        $category->is_active = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }

    public function order()
    {
        $categories = StickerCategory::orderBy('row_order', 'ASC')->get();
        return view('admin.sticker_category.order', compact('categories'));
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $order) {
            StickerCategory::where('id', $order['id'])->update(['row_order' => $order['row_order']]);
        }
        \App\Support\ApiCache::flushStickers();
        return response()->json(['success' => true]);
    }
}
