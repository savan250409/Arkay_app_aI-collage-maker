<?php

namespace App\Http\Controllers;

use App\Models\FrameCategory;
use App\Models\Frame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FrameCategoryController extends Controller
{
    public function index(Request $request)
    {
        session(['frame_category_list_url' => $request->fullUrl()]);

        $query = FrameCategory::query();
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $perPage = $request->input('per_page', 10);
        $categories = $query->orderBy('row_order', 'DESC')->paginate($perPage);

        $allCategories = FrameCategory::orderBy('row_order', 'DESC')->get();

        if ($request->ajax()) {
            return view('admin.frame_category.index', compact('categories', 'allCategories'))->render();
        }

        return view('admin.frame_category.index', compact('categories', 'allCategories'));
    }

    public function create()
    {
        return view('admin.frame_category.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:frame_categories,name',
            'image' => 'required|image|mimes:webp'
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = $request->name;
        $imageName = $request->file('image')->getClientOriginalName();
        $path = public_path('upload/frame/' . $categoryName . '/category-thumbnail-image');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $request->file('image')->move($path, $imageName);

        FrameCategory::create([
            'name' => $categoryName,
            'image' => $imageName,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'row_order' => (FrameCategory::max('row_order') ?? 0) + 1
        ]);

        return redirect(session('frame_category_list_url', route('frame-categories.index')))->with('success', 'Category created successfully.');
    }

    public function edit(FrameCategory $frameCategory)
    {
        return view('admin.frame_category.form', compact('frameCategory'));
    }

    public function update(Request $request, FrameCategory $frameCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:frame_categories,name,' . $frameCategory->id,
            'image' => 'nullable|image|mimes:webp'
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = $request->name;
        $oldCategoryDir = public_path('upload/frame/' . $frameCategory->name);
        $newCategoryDir = public_path('upload/frame/' . $categoryName);

        if ($frameCategory->name !== $categoryName && File::exists($oldCategoryDir)) {
            if (File::exists($newCategoryDir)) {
                return redirect()->back()->withErrors(['name' => 'A folder for the new category name already exists. Please choose a different name or clear the old folder.']);
            }
            $moved = File::moveDirectory($oldCategoryDir, $newCategoryDir);
            if (!$moved) {
                return redirect()->back()->withErrors(['name' => 'Failed to rename the category folder. It might be in use by another program.']);
            }
        }

        $newPath = public_path('upload/frame/' . $categoryName . '/category-thumbnail-image');

        $imageName = $frameCategory->image;

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();

            $oldImagePath = $newPath . '/' . $frameCategory->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            if (!File::exists($newPath)) {
                File::makeDirectory($newPath, 0777, true, true);
            }

            $request->file('image')->move($newPath, $imageName);
        }

        $frameCategory->update([
            'name' => $categoryName,
            'image' => $imageName ?? $frameCategory->image,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        return redirect(session('frame_category_list_url', route('frame-categories.index')))->with('success', 'Category updated successfully.');
    }

    public function destroy(FrameCategory $frameCategory)
    {
        $frames = Frame::where('frame_category_id', $frameCategory->id)->get();
        foreach ($frames as $frame) {
            if (!empty($frame->images)) {
                foreach ($frame->images as $image) {
                    $frameImagePath = public_path('upload/frame/' . $frameCategory->name . '/frame/' . $image);
                    if (File::exists($frameImagePath)) {
                        File::delete($frameImagePath);
                    }
                }
            }
            $frame->delete();
        }

        $thumbnailPath = public_path('upload/frame/' . $frameCategory->name . '/category-thumbnail-image/' . $frameCategory->image);
        if (File::exists($thumbnailPath)) {
            File::delete($thumbnailPath);
        }
        $categoryDirectory = public_path('upload/frame/' . $frameCategory->name);
        if (File::exists($categoryDirectory)) {
            File::deleteDirectory($categoryDirectory);
        }

        $frameCategory->delete();
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $category = FrameCategory::find($request->id);
        $category->is_active = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }
    public function updateOrder(Request $request)
    {
        foreach ($request->order as $order) {
            FrameCategory::where('id', $order['id'])->update(['row_order' => $order['row_order']]);
        }
        \App\Support\ApiCache::flushFrames();
        return response()->json(['success' => true]);
    }
}
