<?php

namespace App\Http\Controllers;

use App\Models\FilterCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FilterCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = FilterCategory::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $perPage = $request->input('per_page', 10);
        $categories = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.filter_category.index', compact('categories'))->render();
        }

        return view('admin.filter_category.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.filter_category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:filter_categories,name',
            'image' => 'required|image|mimes:webp',
        ]);

        $categoryName = $request->name;
        $imageName = $request->file('image')->getClientOriginalName();
        $path = public_path('upload/filter_category/' . $categoryName . '/category-thumbnail-image');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $request->file('image')->move($path, $imageName);

        FilterCategory::create([
            'name' => $categoryName,
            'image' => $imageName,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('filter-categories.index')->with('success', 'Filter Category created successfully.');
    }

    public function edit(FilterCategory $filterCategory)
    {
        return view('admin.filter_category.edit', compact('filterCategory'));
    }

    public function update(Request $request, FilterCategory $filterCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:filter_categories,name,' . $filterCategory->id,
            'image' => 'nullable|image|mimes:webp',
        ]);

        $categoryName = $request->name;
        $newPath = public_path('upload/filter_category/' . $categoryName . '/category-thumbnail-image');

        $imageName = $filterCategory->image;

        // If name changed, move the directory or create new one? 
        // Simpler approach: Create new directory if needed, handle image move if name changed is tricky with folder structure.
        // Let's assume just handling new image upload for now, and if name changes, we move the folder?
        // Cloning the logic from StickerCategoryController roughly, but simplified.

        if ($filterCategory->name !== $categoryName) {
            $oldPath = public_path('upload/filter_category/' . $filterCategory->name);
            $newFolderPath = public_path('upload/filter_category/' . $categoryName);
            if (File::exists($oldPath)) {
                File::move($oldPath, $newFolderPath);
            }
        }

        // Re-evaluate path after potential move
        $targetPath = public_path('upload/filter_category/' . $categoryName . '/category-thumbnail-image');

        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();

            // Delete old image
            $oldImagePath = $targetPath . '/' . $filterCategory->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            if (!File::exists($targetPath)) {
                File::makeDirectory($targetPath, 0777, true, true);
            }

            $request->file('image')->move($targetPath, $imageName);
        }

        $filterCategory->update([
            'name' => $categoryName,
            'image' => $imageName ?? $filterCategory->image,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect()->route('filter-categories.index')->with('success', 'Filter Category updated successfully.');
    }

    public function destroy(FilterCategory $filterCategory)
    {
        $categoryFolder = public_path('upload/filter_category/' . $filterCategory->name);
        if (File::exists($categoryFolder)) {
            File::deleteDirectory($categoryFolder);
        }

        $filterCategory->delete();
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $category = FilterCategory::find($request->id);
        $category->is_active = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }
}
