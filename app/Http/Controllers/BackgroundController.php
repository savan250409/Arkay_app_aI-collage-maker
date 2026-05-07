<?php

namespace App\Http\Controllers;

use App\Models\Background;
use App\Models\BackgroundCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BackgroundController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->page ?? 1;
        $search = $request->search;
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->category_id;

        if (!$request->ajax() && session('restore_bg_state') && session()->has('bg_state')) {
            $state = session('bg_state');
            $page = $state['page'] ?? 1;
            $search = $state['search'] ?? '';
            $perPage = $state['per_page'] ?? 10;
            $categoryId = $state['category_id'] ?? '';

            \Illuminate\Pagination\Paginator::currentPageResolver(function () use ($page) {
                return $page;
            });
        } elseif ($request->ajax()) {
            session([
                'bg_state' => [
                    'page' => $request->page,
                    'search' => $request->search,
                    'per_page' => $request->per_page,
                    'category_id' => $request->category_id,
                ]
            ]);
        }

        $categories = BackgroundCategory::orderBy('name')->get();

        $query = Background::with('category');
        if ($search) {
            $query->whereHas('category', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        if ($categoryId) {
            $query->where('background_category_id', $categoryId);
        }
        $backgrounds = $query->latest()->paginate($perPage);
        $backgrounds->appends(['search' => $search, 'per_page' => $perPage, 'category_id' => $categoryId]);

        if ($request->ajax()) {
            return view('admin.background.index', compact('backgrounds', 'categories', 'categoryId'))->render();
        }

        return view('admin.background.index', compact('backgrounds', 'search', 'perPage', 'categories', 'categoryId'));
    }

    public function create()
    {
        $categories = BackgroundCategory::all();
        return view('admin.background.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'background_category_id' => 'required|exists:background_categories,id|unique:backgrounds,background_category_id',
            'images' => 'required|array',
            'images.*' => 'image|mimes:webp'
        ], [
            'images.*.mimes' => 'Only .webp images are allowed.'
        ]);

        $category = BackgroundCategory::find($request->background_category_id);
        $path = public_path('upload/background/' . $category->name . '/backgrounds');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $imagePremiums = $request->image_premium ?? [];
        $storedImages = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $imageName = $image->getClientOriginalName();
                $image->move($path, $imageName);
                $isPremium = $category->is_premium ? 1 : (isset($imagePremiums[$index]) ? 1 : 0);
                $storedImages[] = ['image' => $imageName, 'is_premium' => $isPremium];
            }
        }

        Background::create([
            'background_category_id' => $request->background_category_id,
            'images' => $storedImages
        ]);

        return redirect()->route('backgrounds.index')->with('success', 'Background(s) created successfully.')->with('restore_bg_state', true);
    }

    public function edit(Background $background)
    {
        $categories = BackgroundCategory::all();
        return view('admin.background.form', compact('background', 'categories'));
    }

    public function update(Request $request, Background $background)
    {
        $request->validate([
            'background_category_id' => 'required|exists:background_categories,id|unique:backgrounds,background_category_id,' . $background->id,
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:webp',
            'existing_images' => 'nullable|array',
            'existing_premiums' => 'nullable|array',
            'new_image_premiums' => 'nullable|array',
            'item_type' => 'nullable|array'
        ]);

        $category = BackgroundCategory::find($request->background_category_id);
        $path = public_path('upload/background/' . $category->name . '/backgrounds');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $finalImages = [];
        $existingIdx = 0;
        $newIdx = 0;
        $existingImagesInput = $request->existing_images ?? [];
        $existingPremiumsInput = $request->existing_premiums ?? [];
        $newImagesInput = $request->file('images') ?? [];
        $newPremiumsInput = $request->new_image_premiums ?? [];

        if ($request->has('item_type')) {
            foreach ($request->item_type as $type) {
                if ($type == 'existing') {
                    if (isset($existingImagesInput[$existingIdx])) {
                        $imageName = $existingImagesInput[$existingIdx];
                        $isPremium = $category->is_premium ? 1 : (int)($existingPremiumsInput[$existingIdx] ?? 0);
                        $finalImages[] = ['image' => $imageName, 'is_premium' => $isPremium];
                        $existingIdx++;
                    }
                } elseif ($type == 'new') {
                    if (isset($newImagesInput[$newIdx])) {
                        $image = $newImagesInput[$newIdx];
                        $imageName = $image->getClientOriginalName();
                        $image->move($path, $imageName);
                        $isPremium = $category->is_premium ? 1 : (int)($newPremiumsInput[$newIdx] ?? 0);
                        $finalImages[] = ['image' => $imageName, 'is_premium' => $isPremium];
                        $newIdx++;
                    }
                }
            }
        }

        // Delete removed images
        $originalImages = $background->images ?? [];
        $finalImageNames = array_column($finalImages, 'image');
        foreach ($originalImages as $item) {
            $imgName = is_array($item) ? $item['image'] : $item;
            if (!in_array($imgName, $finalImageNames)) {
                $imagePath = public_path('upload/background/' . $background->category->name . '/backgrounds/' . $imgName);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
        }

        // Move files if category changed
        if ($background->background_category_id != $request->background_category_id) {
            $oldPathBase = public_path('upload/background/' . $background->category->name . '/backgrounds/');
            foreach ($finalImages as $item) {
                $img = $item['image'];
                if (File::exists($oldPathBase . $img)) {
                    File::move($oldPathBase . $img, $path . '/' . $img);
                }
            }
        }

        $background->update([
            'background_category_id' => $request->background_category_id,
            'images' => $finalImages
        ]);

        return redirect()->route('backgrounds.index')->with('success', 'Background updated successfully.')->with('restore_bg_state', true);
    }

    public function destroy(Background $background)
    {
        $category = $background->category;
        if (!empty($background->images)) {
            foreach ($background->images as $item) {
                $imgName = is_array($item) ? $item['image'] : $item;
                $path = public_path('upload/background/' . $category->name . '/backgrounds/' . $imgName);
                if (File::exists($path)) {
                    File::delete($path);
                }
            }
        }

        $background->delete();
        return response()->json(['success' => true]);
    }
}
