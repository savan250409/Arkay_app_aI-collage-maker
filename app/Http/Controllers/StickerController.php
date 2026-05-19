<?php

namespace App\Http\Controllers;

use App\Models\Sticker;
use App\Models\StickerCategory;
use App\Support\UniqueNamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StickerController extends Controller
{
    public function index(Request $request)
    {
        session(['sticker_list_url' => $request->fullUrl()]);

        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id', '');

        $categories = StickerCategory::orderBy('name')->get();

        $query = Sticker::with('category');
        if ($search) {
            $query->whereHas('category', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        if ($categoryId) {
            $query->where('sticker_category_id', $categoryId);
        }
        $stickers = $query->latest()->paginate($perPage);
        $stickers->appends(['search' => $search, 'per_page' => $perPage, 'category_id' => $categoryId]);

        if ($request->ajax()) {
            return view('admin.sticker.index', compact('stickers', 'categories', 'categoryId'))->render();
        }

        return view('admin.sticker.index', compact('stickers', 'search', 'perPage', 'categories', 'categoryId'));
    }

    public function create()
    {
        $categories = StickerCategory::all();
        return view('admin.sticker.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sticker_category_id' => 'required|exists:sticker_categories,id|unique:stickers,sticker_category_id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:webp'
        ], [
            'images.*.mimes' => 'Only .webp images are allowed.'
        ]);

        $category = StickerCategory::find($request->sticker_category_id);
        $categoryName = $category->name;
        $path = public_path('upload/sticker/' . $categoryName . '/stickers');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $storedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = UniqueNamer::uniqueFile($path, $image->getClientOriginalName());
                $image->move($path, $imageName);
                $storedImages[] = $imageName;
            }
        }

        Sticker::create([
            'sticker_category_id' => $request->sticker_category_id,
            'images' => $storedImages
        ]);

        return redirect(session('sticker_list_url', route('stickers.index')))->with('success', 'Sticker created successfully.');
    }

    public function edit(Sticker $sticker)
    {
        $categories = StickerCategory::all();
        return view('admin.sticker.form', compact('sticker', 'categories'));
    }

    public function update(Request $request, Sticker $sticker)
    {
        $request->validate([
            'sticker_category_id' => 'required|exists:sticker_categories,id|unique:stickers,sticker_category_id,' . $sticker->id,
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:webp',
            'existing_images' => 'nullable|array',
            'item_type' => 'nullable|array'
        ], [
            'images.*.mimes' => 'Only .webp images are allowed.'
        ]);

        $category = StickerCategory::find($request->sticker_category_id);
        $categoryName = $category->name;
        $path = public_path('upload/sticker/' . $categoryName . '/stickers');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $finalImages = [];

        $existingImageIndex = 0;
        $newImageIndex = 0;
        $existingImagesInput = $request->existing_images ?? [];
        $newImagesInput = $request->file('images') ?? [];

        if ($request->has('item_type')) {
            foreach ($request->item_type as $index => $type) {
                if ($type == 'existing') {
                    if (isset($existingImagesInput[$existingImageIndex])) {
                        $imageName = $existingImagesInput[$existingImageIndex];
                        $finalImages[] = $imageName;
                        $existingImageIndex++;
                    }
                } elseif ($type == 'new') {
                    if (isset($newImagesInput[$newImageIndex])) {
                        $image = $newImagesInput[$newImageIndex];
                        $imageName = UniqueNamer::uniqueFile($path, $image->getClientOriginalName());
                        $image->move($path, $imageName);

                        $finalImages[] = $imageName;
                        $newImageIndex++;
                    }
                }
            }
        }

        $originalImages = $sticker->images ?? [];
        $removedImages = array_diff($originalImages, $finalImages);

        foreach ($removedImages as $removedImage) {
            $imagePath = public_path('upload/sticker/' . $sticker->category->name . '/stickers/' . $removedImage);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        if ($sticker->sticker_category_id != $request->sticker_category_id) {
            $oldCategory = $sticker->category;
            $oldPathBase = public_path('upload/sticker/' . $oldCategory->name . '/stickers/');
            foreach ($finalImages as $img) {
                if (File::exists($oldPathBase . $img)) {
                    File::move($oldPathBase . $img, $path . '/' . $img);
                }
            }
        }

        $sticker->update([
            'sticker_category_id' => $request->sticker_category_id,
            'images' => $finalImages
        ]);

        return redirect(session('sticker_list_url', route('stickers.index')))->with('success', 'Sticker updated successfully.');
    }

    public function destroy(Sticker $sticker)
    {
        $category = $sticker->category;
        if (!empty($sticker->images)) {
            foreach ($sticker->images as $image) {
                $path = public_path('upload/sticker/' . $category->name . '/stickers/' . $image);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }
        }

        $sticker->delete();
        return response()->json(['success' => true]);
    }
}
