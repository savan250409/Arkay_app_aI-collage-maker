<?php

namespace App\Http\Controllers;

use App\Models\Sticker;
use App\Models\StickerCategory;
use App\Support\UniqueNamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StickerCategoryController extends Controller
{
    public function index(Request $request)
    {
        session(['sticker_cat_list_url' => $request->fullUrl()]);

        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

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
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:webp'
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = UniqueNamer::uniqueName('sticker_categories', 'name', $request->name);
        $path = public_path('upload/sticker/' . $categoryName . '/category-thumbnail-image');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $imageName = UniqueNamer::uniqueFile($path, $request->file('image')->getClientOriginalName());
        $request->file('image')->move($path, $imageName);

        StickerCategory::create([
            'name' => $categoryName,
            'image' => $imageName,
            'is_premium' => $request->has('is_premium') ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'row_order' => StickerCategory::max('row_order') + 1
        ]);

        return redirect(session('sticker_cat_list_url', route('sticker-categories.index')))->with('success', 'Category created successfully.');
    }

    public function edit(StickerCategory $stickerCategory)
    {
        return view('admin.sticker_category.form', compact('stickerCategory'));
    }

    public function update(Request $request, StickerCategory $stickerCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:webp',
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = UniqueNamer::uniqueName('sticker_categories', 'name', $request->name, $stickerCategory->id);

        // Rename the folder if name changed
        if ($stickerCategory->name !== $categoryName) {
            $oldFolder = public_path('upload/sticker/' . $stickerCategory->name);
            $newFolder = public_path('upload/sticker/' . $categoryName);
            if (File::exists($oldFolder) && !File::exists($newFolder)) {
                File::move($oldFolder, $newFolder);
            }
        }

        $newPath = public_path('upload/sticker/' . $categoryName . '/category-thumbnail-image');

        $imageName = $stickerCategory->image;

        if ($request->hasFile('image')) {
            if (!File::exists($newPath)) {
                File::makeDirectory($newPath, 0777, true, true);
            }

            $oldImagePath = $newPath . '/' . $stickerCategory->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $imageName = UniqueNamer::uniqueFile($newPath, $request->file('image')->getClientOriginalName());
            $request->file('image')->move($newPath, $imageName);
        }

        $stickerCategory->update([
            'name' => $categoryName,
            'image' => $imageName ?? $stickerCategory->image,
            'is_premium' => $request->has('is_premium') ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect(session('sticker_cat_list_url', route('sticker-categories.index')))->with('success', 'Category updated successfully.');
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
