<?php

namespace App\Http\Controllers;

use App\Models\Photoshoot;
use App\Models\PhotoshootCategory;
use App\Support\UniqueNamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PhotoshootCategoryController extends Controller
{
    public function index(Request $request)
    {
        session(['photoshoot_cat_list_url' => $request->fullUrl()]);

        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);

        $query = PhotoshootCategory::query();
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        $categories = $query->latest()->paginate($perPage);
        $categories->appends(['search' => $search, 'per_page' => $perPage]);

        if ($request->ajax()) {
            return view('admin.photoshoot_category.index', compact('categories'))->render();
        }

        return view('admin.photoshoot_category.index', compact('categories', 'search', 'perPage'));
    }

    public function create()
    {
        return view('admin.photoshoot_category.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => ['required', $this->webpRule()],
        ]);

        $categoryName = UniqueNamer::uniqueName('photoshoot_categories', 'name', $request->name);
        $path = public_path('upload/photoshoot/' . $categoryName . '/category image');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $imageName = UniqueNamer::uniqueFile($path, $request->file('image')->getClientOriginalName());
        $request->file('image')->move($path, $imageName);

        PhotoshootCategory::create([
            'name' => $categoryName,
            'image' => $imageName,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect(session('photoshoot_cat_list_url', route('photoshoot-categories.index')))->with('success', 'Category created successfully.');
    }

    public function edit(PhotoshootCategory $photoshootCategory)
    {
        return view('admin.photoshoot_category.form', compact('photoshootCategory'));
    }

    public function update(Request $request, PhotoshootCategory $photoshootCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => ['nullable', $this->webpRule()],
        ]);

        $categoryName = UniqueNamer::uniqueName('photoshoot_categories', 'name', $request->name, $photoshootCategory->id);

        // Rename the folder if name changed
        if ($photoshootCategory->name !== $categoryName) {
            $oldFolder = public_path('upload/photoshoot/' . $photoshootCategory->name);
            $newFolder = public_path('upload/photoshoot/' . $categoryName);
            if (File::exists($oldFolder) && !File::exists($newFolder)) {
                File::move($oldFolder, $newFolder);
            }
        }

        $newPath = public_path('upload/photoshoot/' . $categoryName . '/category image');
        $imageName = $photoshootCategory->image;

        if ($request->hasFile('image')) {
            if (!File::exists($newPath)) {
                File::makeDirectory($newPath, 0777, true, true);
            }

            $oldImagePath = $newPath . '/' . $photoshootCategory->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $imageName = UniqueNamer::uniqueFile($newPath, $request->file('image')->getClientOriginalName());
            $request->file('image')->move($newPath, $imageName);
        }

        $photoshootCategory->update([
            'name' => $categoryName,
            'image' => $imageName ?? $photoshootCategory->image,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect(session('photoshoot_cat_list_url', route('photoshoot-categories.index')))->with('success', 'Category updated successfully.');
    }

    public function destroy(PhotoshootCategory $photoshootCategory)
    {
        // Cascade deletes the photoshoot rows; remove the whole category folder.
        $categoryFolder = public_path('upload/photoshoot/' . $photoshootCategory->name);
        if (File::exists($categoryFolder)) {
            File::deleteDirectory($categoryFolder);
        }

        $photoshootCategory->delete();
        return response()->json(['success' => true]);
    }

    public function updateActiveStatus(Request $request)
    {
        $category = PhotoshootCategory::find($request->id);
        $category->is_active = $request->status;
        $category->save();

        return response()->json(['success' => true]);
    }

    /**
     * Accept only .webp images, detecting the format from the file content via
     * GD (getimagesize) rather than the server MIME/finfo guesser — which
     * misidentifies webp on some Apache/Windows setups and wrongly rejects
     * genuine .webp uploads. Falls back to the .webp extension.
     */
    private function webpRule(): \Closure
    {
        return function ($attribute, $value, $fail) {
            if (!$value instanceof \Illuminate\Http\UploadedFile || !$value->isValid()) {
                $fail('Only .webp images are allowed.');
                return;
            }

            $info = @getimagesize($value->getPathname());
            $isWebp = $info && isset($info[2]) && $info[2] === IMAGETYPE_WEBP;
            $extWebp = strtolower($value->getClientOriginalExtension()) === 'webp';

            if (!$isWebp && !$extWebp) {
                $fail('Only .webp images are allowed.');
            }
        };
    }
}
