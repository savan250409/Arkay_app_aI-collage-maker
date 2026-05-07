<?php

namespace App\Http\Controllers;

use App\Models\Font;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FontController extends Controller
{
    public function index(Request $request)
    {
        $query = Font::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $perPage = $request->input('per_page', 10);
        $fonts = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.font.index', compact('fonts'))->render();
        }

        return view('admin.font.index', compact('fonts'));
    }

    public function create()
    {
        return view('admin.font.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fonts,name',
            'file' => 'required|file',
            'font_preview' => 'nullable|image|mimes:webp',
            'type' => 'required|in:free,pro'
        ]);

        $path = public_path('upload/font/' . $request->name);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file->move($path, $fileName);

            $previewName = null;
            if ($request->hasFile('font_preview')) {
                $preview = $request->file('font_preview');
                $previewName = $preview->getClientOriginalName();
                $preview->move($path, $previewName);
            }

            Font::create([
                'name' => $request->name,
                'file' => $fileName,
                'type' => $request->type,
                'font_preview' => $previewName
            ]);
        }

        return redirect()->route('fonts.index')->with('success', 'Font created successfully.');
    }

    public function edit(Font $font)
    {
        return view('admin.font.form', compact('font'));
    }

    public function update(Request $request, Font $font)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fonts,name,' . $font->id,
            'file' => 'nullable|file',
            'font_preview' => 'nullable|image|mimes:webp',
            'type' => 'required|in:free,pro'
        ]);

        $updateData = [
            'name' => $request->name,
            'type' => $request->type
        ];

        if ($request->hasFile('file')) {
            $path = public_path('upload/font/' . $request->name);

            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $oldPath = public_path('upload/font/' . $font->name);
            $oldFilePath = $oldPath . '/' . $font->file;

            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file->move($path, $fileName);

            $updateData['file'] = $fileName;
        }

        if ($request->hasFile('font_preview')) {
            $path = public_path('upload/font/' . $request->name);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            // Only delete old preview if we are uploading a new one
            if ($font->font_preview) {
                $oldPreviewPath = public_path('upload/font/' . $font->name . '/' . $font->font_preview);
                if (File::exists($oldPreviewPath)) {
                    File::delete($oldPreviewPath);
                }
            }

            $preview = $request->file('font_preview');
            $previewName = $preview->getClientOriginalName();
            $preview->move($path, $previewName);

            $updateData['font_preview'] = $previewName;
        }

        if ($font->name !== $request->name) {
            // Handle renaming folder if name changed (and no new file uploaded, handled above partly, but complex if both change)
            // Simplified: The original code only moved if filenot changed.
            // If name changes, we should move the folder.
            $oldPath = public_path('upload/font/' . $font->name);
            $newPath = public_path('upload/font/' . $request->name);

            if (File::exists($oldPath) && !File::exists($newPath)) {
                File::move($oldPath, $newPath);
            }
        }

        $font->update($updateData);

        return redirect()->route('fonts.index')->with('success', 'Font updated successfully.');
    }

    public function destroy(Font $font)
    {
        $folderPath = public_path('upload/font/' . $font->name);
        $filePath = $folderPath . '/' . $font->file;

        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        if (File::exists($folderPath) && empty(File::files($folderPath))) {
            File::deleteDirectory($folderPath);
        }

        $font->delete();
        return response()->json(['success' => true]);
    }

    public function changeType(Request $request)
    {
        $font = Font::find($request->id);
        if ($font) {
            $font->type = $request->type;
            $font->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }
}
