<?php

namespace App\Http\Controllers;

use App\Models\Background;
use App\Models\BackgroundCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackgroundCategoryController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $search = $request->search;
        $perPage = $request->input('per_page', 10);

        if (!$request->ajax() && session('restore_bg_cat_state') && session()->has('bg_cat_state')) {
            $state = session('bg_cat_state');
            $page = $state['page'] ?? 1;
            $search = $state['search'] ?? '';
            $perPage = $state['per_page'] ?? 10;

            \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        } elseif ($request->ajax()) {
            session([
                'bg_cat_state' => [
                    'page' => $request->page,
                    'search' => $request->search,
                    'per_page' => $request->per_page
                ]
            ]);
        }

        $query = BackgroundCategory::query();
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        $categories = $query->orderBy('row_order', 'ASC')->paginate($perPage);
        $categories->appends(['search' => $search, 'per_page' => $perPage]);

        if ($request->ajax()) {
            return view('admin.background_category.index', compact('categories'))->render();
        }

        return view('admin.background_category.index', compact('categories', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.background_category.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:background_categories,name',
            'image' => 'required|image|mimes:webp'
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $categoryName = $request->name;
        $imageName = $request->file('image')->getClientOriginalName();
        $path = public_path('upload/background/' . $categoryName . '/category-thumbnail-image');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $request->file('image')->move($path, $imageName);

        BackgroundCategory::create([
            'name' => $categoryName,
            'image' => $imageName,
            'is_premium' => $request->has('is_premium') ? 1 : 0,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'row_order' => BackgroundCategory::max('row_order') + 1
        ]);

        return redirect()->route('background-categories.index')->with('success', 'Category created successfully.')->with('restore_bg_cat_state', true);
    }

    public function edit(BackgroundCategory $backgroundCategory)
    {
        return view('admin.background_category.form', compact('backgroundCategory'));
    }

    public function update(Request $request, BackgroundCategory $backgroundCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:background_categories,name,' . $backgroundCategory->id,
        ]);

        $categoryName = $request->name;
        $newPath = public_path('upload/background/' . $categoryName . '/category-thumbnail-image');

        $imageName = $backgroundCategory->image;

        if ($request->hasFile('image')) {
            $imageName = $request->file('image')->getClientOriginalName();

            $oldPath = public_path('upload/background/' . $backgroundCategory->name . '/category-thumbnail-image');
            $oldImagePath = $oldPath . '/' . $backgroundCategory->image;

            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            if (!File::exists($newPath)) {
                File::makeDirectory($newPath, 0777, true, true);
            }

            $request->file('image')->move($newPath, $imageName);
        }

        $oldIsPremium = $backgroundCategory->is_premium;
        $newIsPremium = $request->has('is_premium') ? 1 : 0;

        $backgroundCategory->update([
            'name' => $categoryName,
            'image' => $imageName ?? $backgroundCategory->image,
            'is_premium' => $newIsPremium,
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        // If category changed to premium, set all backgrounds to premium
        if ($newIsPremium && !$oldIsPremium) {
            Background::where('background_category_id', $backgroundCategory->id)->update(['is_premium' => 1]);
            \App\Support\ApiCache::flushBackgrounds();
        }

        return redirect()->route('background-categories.index')->with('success', 'Category updated successfully.')->with('restore_bg_cat_state', true);
    }

    public function destroy(BackgroundCategory $backgroundCategory)
    {
        $backgrounds = Background::where('background_category_id', $backgroundCategory->id)->get();
        foreach ($backgrounds as $background) {
            if (!empty($background->image)) {
                $bgPath = public_path('upload/background/' . $backgroundCategory->name . '/backgrounds/' . $background->image);
                if (File::exists($bgPath)) {
                    File::delete($bgPath);
                }
            }
            $background->delete();
        }

        $thumbnailPath = public_path('upload/background/' . $backgroundCategory->name . '/category-thumbnail-image/' . $backgroundCategory->image);
        if (File::exists($thumbnailPath)) {
            File::delete($thumbnailPath);
        }
        $categoryFolder = public_path('upload/background/' . $backgroundCategory->name);
        if (File::exists($categoryFolder)) {
            File::deleteDirectory($categoryFolder);
        }

        $backgroundCategory->delete();
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {
        $category = BackgroundCategory::find($request->id);
        $category->is_premium = $request->status;
        $category->save();

        // If category set to premium, auto-set all backgrounds to premium
        if ($request->status == 1) {
            Background::where('background_category_id', $category->id)->update(['is_premium' => 1]);
            \App\Support\ApiCache::flushBackgrounds();
        }

        return response()->json(['success' => true]);
    }

    public function updateActiveStatus(Request $request)
    {
        $category = BackgroundCategory::find($request->id);
        $category->is_active = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }

    public function order()
    {
        $categories = BackgroundCategory::orderBy('row_order', 'ASC')->get();
        return view('admin.background_category.order', compact('categories'));
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->order as $order) {
            BackgroundCategory::where('id', $order['id'])->update(['row_order' => $order['row_order']]);
        }
        \App\Support\ApiCache::flushBackgrounds();
        return response()->json(['success' => true]);
    }
}
