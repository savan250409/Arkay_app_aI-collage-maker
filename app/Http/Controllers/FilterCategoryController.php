<?php

namespace App\Http\Controllers;

use App\Models\FilterCategory;
use App\Support\UniqueNamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FilterCategoryController extends Controller
{
    public function index(Request $request)
    {
        session(['filter_cat_list_url' => $request->fullUrl()]);

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
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:webp',
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = UniqueNamer::uniqueName('filter_categories', 'name', $request->name);
        $path = public_path('upload/filter_category/' . $categoryName . '/category-thumbnail-image');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $imageName = UniqueNamer::uniqueFile($path, $request->file('image')->getClientOriginalName());
        $request->file('image')->move($path, $imageName);

        FilterCategory::create([
            'name' => $categoryName,
            'image' => $imageName,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect(session('filter_cat_list_url', route('filter-categories.index')))->with('success', 'Filter Category created successfully.');
    }

    public function edit(FilterCategory $filterCategory)
    {
        return view('admin.filter_category.edit', compact('filterCategory'));
    }

    public function update(Request $request, FilterCategory $filterCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:webp',
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = UniqueNamer::uniqueName('filter_categories', 'name', $request->name, $filterCategory->id);

        $imageName = $filterCategory->image;

        if ($filterCategory->name !== $categoryName) {
            $oldPath = public_path('upload/filter_category/' . $filterCategory->name);
            $newFolderPath = public_path('upload/filter_category/' . $categoryName);
            if (File::exists($oldPath) && !File::exists($newFolderPath)) {
                File::move($oldPath, $newFolderPath);
            }
        }

        $targetPath = public_path('upload/filter_category/' . $categoryName . '/category-thumbnail-image');

        if ($request->hasFile('image')) {
            if (!File::exists($targetPath)) {
                File::makeDirectory($targetPath, 0777, true, true);
            }

            $oldImagePath = $targetPath . '/' . $filterCategory->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $imageName = UniqueNamer::uniqueFile($targetPath, $request->file('image')->getClientOriginalName());
            $request->file('image')->move($targetPath, $imageName);
        }

        $filterCategory->update([
            'name' => $categoryName,
            'image' => $imageName ?? $filterCategory->image,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect(session('filter_cat_list_url', route('filter-categories.index')))->with('success', 'Filter Category updated successfully.');
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
