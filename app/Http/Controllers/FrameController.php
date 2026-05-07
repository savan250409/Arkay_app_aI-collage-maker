<?php

namespace App\Http\Controllers;

use App\Models\Frame;
use App\Models\FrameCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FrameController extends Controller
{
    public function index(Request $request)
    {
        session(['frame_list_url' => $request->fullUrl()]);

        $search = $request->input('search', '');
        $categoryId = $request->input('category_id', '');
        $perPage = $request->input('per_page', 10);

        $categories = FrameCategory::orderBy('name')->get();

        $query = Frame::with('category');

        if ($search) {
            $query->whereHas('category', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        if ($categoryId) {
            $query->where('frame_category_id', $categoryId);
        }

        $frames = $query->orderBy('row_order', 'DESC')->paginate($perPage);
        $allFrames = Frame::with('category')->orderBy('row_order', 'DESC')->get();
        $frames->appends(['search' => $search, 'per_page' => $perPage, 'category_id' => $categoryId]);

        if ($request->ajax()) {
            return view('admin.frame.index', compact('frames', 'allFrames', 'categories', 'categoryId', 'search', 'perPage'))->render();
        }

        return view('admin.frame.index', compact('frames', 'allFrames', 'categories', 'categoryId', 'search', 'perPage'));
    }

    public function create()
    {
        $categories = FrameCategory::all();
        return view('admin.frame.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'frame_category_id' => 'required|exists:frame_categories,id|unique:frames,frame_category_id',
            'indices' => 'required|array',
            'images' => 'required|array',
            'images.*' => 'image|mimes:webp',
            'frame_thumbnail' => 'nullable|array',
            'frame_thumbnail.*' => 'image|mimes:webp',
            'counts' => 'required|array',
            'types' => 'required|array'
        ], [
            'images.*.mimes' => 'Only .webp images are allowed.',
            'frame_thumbnail.*.mimes' => 'Only .webp images are allowed.'
        ]);

        $category = FrameCategory::findOrFail($request->frame_category_id);
        $categoryFolder = public_path('upload/frame/' . $category->name);

        if (!File::exists($categoryFolder)) {
            File::makeDirectory($categoryFolder, 0777, true, true);
        }

        $frameFolder = $categoryFolder . '/frame';
        if (!File::exists($frameFolder)) {
            File::makeDirectory($frameFolder, 0777, true, true);
        }

        $thumbFolder = $categoryFolder . '/frame_thumbnail_image';
        if (!File::exists($thumbFolder)) {
            File::makeDirectory($thumbFolder, 0777, true, true);
        }

        $storedImages = [];
        $storedCounts = [];
        $storedTypes = [];
        $storedThumbnails = [];

        foreach ($request->indices as $key => $index) {
            if ($request->hasFile("images.$index")) {
                $image = $request->file("images")[$index];
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move($frameFolder, $imageName);
                $storedImages[] = $imageName;
                $storedCounts[] = $request->counts[$key];
                $storedTypes[] = $request->types[$key];

                if ($request->hasFile("frame_thumbnail.$index")) {
                    $thumb = $request->file("frame_thumbnail")[$index];
                    $thumbName = 'thumb_' . time() . '_' . $thumb->getClientOriginalName();
                    $thumb->move($thumbFolder, $thumbName);
                    $storedThumbnails[] = $thumbName;
                } else {
                    $storedThumbnails[] = null;
                }
            }
        }

        Frame::create([
            'frame_category_id' => $request->frame_category_id,
            'images' => $storedImages,
            'image_input_counts' => $storedCounts,
            'image_types' => $storedTypes,
            'frame_thumbnail' => $storedThumbnails,
            'row_order' => (Frame::max('row_order') ?? 0) + 1
        ]);

        return redirect(session('frame_list_url', route('frames.index')))->with('success', 'Frame added successfully');
    }

    public function edit(Frame $frame)
    {
        $categories = FrameCategory::all();
        return view('admin.frame.form', compact('frame', 'categories'));
    }

    public function update(Request $request, Frame $frame)
    {
        $request->validate([
            'frame_category_id' => 'required|exists:frame_categories,id|unique:frames,frame_category_id,' . $frame->id,
            'indices' => 'required|array',
            'item_type' => 'required|array',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:webp',
            'frame_thumbnail' => 'nullable|array',
            'frame_thumbnail.*' => 'image|mimes:webp',
            'existing_images' => 'nullable|array',
            'existing_thumbnails' => 'nullable|array',
            'counts' => 'required|array',
            'types' => 'required|array'
        ]);

        $category = FrameCategory::findOrFail($request->frame_category_id);
        $categoryFolder = public_path('upload/frame/' . $category->name);
        $frameFolder = $categoryFolder . '/frame';
        $thumbFolder = $categoryFolder . '/frame_thumbnail_image';

        if (!File::exists($frameFolder)) {
            File::makeDirectory($frameFolder, 0777, true, true);
        }
        if (!File::exists($thumbFolder)) {
            File::makeDirectory($thumbFolder, 0777, true, true);
        }

        $storedImages = [];
        $storedCounts = [];
        $storedTypes = [];
        $storedThumbnails = [];

        // Track what existing images we are keeping
        $keptExistingImages = [];
        $keptExistingThumbnails = [];

        foreach ($request->indices as $key => $index) {
            $type = $request->item_type[$key];
            if ($type == 'new') {
                if ($request->hasFile("images.$index")) {
                    $image = $request->file("images")[$index];
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move($frameFolder, $imageName);
                    $storedImages[] = $imageName;

                    if ($request->hasFile("frame_thumbnail.$index")) {
                        $thumb = $request->file("frame_thumbnail")[$index];
                        $thumbName = 'thumb_' . time() . '_' . $thumb->getClientOriginalName();
                        $thumb->move($thumbFolder, $thumbName);
                        $storedThumbnails[] = $thumbName;
                    } else {
                        $storedThumbnails[] = null;
                    }
                }
            } else {
                $existingImage = $request->existing_images[$key] ?? null;

                if ($request->hasFile("images.$index")) {
                    // New image uploaded, so we don't keep the existing one
                    $image = $request->file("images")[$index];
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $image->move($frameFolder, $imageName);
                    $storedImages[] = $imageName;
                } else {
                    $storedImages[] = $existingImage;
                    if ($existingImage) {
                        $keptExistingImages[] = $existingImage;
                    }
                }

                $existingThumb = $request->existing_thumbnails[$key] ?? null;

                if ($request->hasFile("frame_thumbnail.$index")) {
                    // New thumbnail uploaded
                    $thumb = $request->file("frame_thumbnail")[$index];
                    $thumbName = 'thumb_' . time() . '_' . $thumb->getClientOriginalName();
                    $thumb->move($thumbFolder, $thumbName);
                    $storedThumbnails[] = $thumbName;
                } else {
                    $storedThumbnails[] = $existingThumb;
                    if ($existingThumb) {
                        $keptExistingThumbnails[] = $existingThumb;
                    }
                }
            }
            $storedCounts[] = $request->counts[$key] ?? 1;
            $storedTypes[] = $request->types[$key] ?? 'free';
        }

        // Find images that were in the database but are no longer in the kept list
        $originalImages = $frame->images ?? [];
        $originalThumbnails = $frame->frame_thumbnail ?? [];

        $oldCategory = $frame->category;
        $oldCategoryFolder = $oldCategory ? public_path('upload/frame/' . $oldCategory->name) : null;
        $oldFrameFolder = $oldCategoryFolder ? $oldCategoryFolder . '/frame' : $frameFolder;
        $oldThumbFolder = $oldCategoryFolder ? $oldCategoryFolder . '/frame_thumbnail_image' : $thumbFolder;

        foreach ($originalImages as $origIndex => $origImage) {
            if ($origImage) {
                if (in_array($origImage, $keptExistingImages)) {
                    if ($oldFrameFolder !== $frameFolder && File::exists($oldFrameFolder . '/' . $origImage)) {
                        File::move($oldFrameFolder . '/' . $origImage, $frameFolder . '/' . $origImage);
                    }
                } else {
                    if (File::exists($oldFrameFolder . '/' . $origImage)) {
                        File::delete($oldFrameFolder . '/' . $origImage);
                    }
                }
            }
        }

        foreach ($originalThumbnails as $origIndex => $origThumb) {
            if ($origThumb) {
                if (in_array($origThumb, $keptExistingThumbnails)) {
                    if ($oldThumbFolder !== $thumbFolder && File::exists($oldThumbFolder . '/' . $origThumb)) {
                        File::move($oldThumbFolder . '/' . $origThumb, $thumbFolder . '/' . $origThumb);
                    }
                } else {
                    if (File::exists($oldThumbFolder . '/' . $origThumb)) {
                        File::delete($oldThumbFolder . '/' . $origThumb);
                    }
                }
            }
        }

        $frame->update([
            'frame_category_id' => $request->frame_category_id,
            'images' => $storedImages,
            'image_input_counts' => $storedCounts,
            'image_types' => $storedTypes,
            'frame_thumbnail' => $storedThumbnails
        ]);

        return redirect(session('frame_list_url', route('frames.index')))->with('success', 'Frame updated successfully');
    }

    public function destroy(Frame $frame)
    {
        $category = $frame->category;
        if ($category) {
            foreach ($frame->images as $image) {
                $imagePath = public_path('upload/frame/' . $category->name . '/frame/' . $image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            if (is_array($frame->frame_thumbnail)) {
                foreach ($frame->frame_thumbnail as $thumb) {
                    if ($thumb) {
                        $thumbPath = public_path('upload/frame/' . $category->name . '/frame_thumbnail_image/' . $thumb);
                        if (File::exists($thumbPath)) {
                            File::delete($thumbPath);
                        }
                    }
                }
            }
        }

        $frame->delete();
        return response()->json(['success' => true]);
    }

    public function updateOrder(Request $request)
    {
        $order = $request->order;
        $totalCount = count($order);
        foreach ($order as $index => $id) {
            Frame::where('id', $id)->update(['row_order' => $totalCount - $index]);
        }
        return response()->json(['success' => true]);
    }
}
