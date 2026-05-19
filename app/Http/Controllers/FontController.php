<?php

namespace App\Http\Controllers;

use App\Models\Font;
use App\Support\UniqueNamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FontController extends Controller
{
    public function index(Request $request)
    {
        session(['font_list_url' => $request->fullUrl()]);

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
            'name' => 'required|string|max:255',
            'file' => 'required|file',
            'font_preview' => 'nullable|image|mimes:webp',
            'type' => 'required|in:free,pro'
        ], [
            'font_preview.mimes' => 'Only .webp images are allowed.'
        ]);

        $name = UniqueNamer::uniqueName('fonts', 'name', $request->name);
        $path = public_path('upload/font/' . $name);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = UniqueNamer::uniqueFile($path, $file->getClientOriginalName());
            $file->move($path, $fileName);

            $previewName = null;
            if ($request->hasFile('font_preview')) {
                $preview = $request->file('font_preview');
                $previewName = UniqueNamer::uniqueFile($path, $preview->getClientOriginalName());
                $preview->move($path, $previewName);
            }

            Font::create([
                'name' => $name,
                'file' => $fileName,
                'type' => $request->type,
                'font_preview' => $previewName
            ]);
        }

        return redirect(session('font_list_url', route('fonts.index')))->with('success', 'Font created successfully.');
    }

    public function edit(Font $font)
    {
        return view('admin.font.form', compact('font'));
    }

    public function update(Request $request, Font $font)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'file' => 'nullable|file',
            'font_preview' => 'nullable|image|mimes:webp',
            'type' => 'required|in:free,pro'
        ], [
            'font_preview.mimes' => 'Only .webp images are allowed.'
        ]);

        $name = UniqueNamer::uniqueName('fonts', 'name', $request->name, $font->id);

        $updateData = [
            'name' => $name,
            'type' => $request->type
        ];

        if ($font->name !== $name) {
            $oldPath = public_path('upload/font/' . $font->name);
            $newPath = public_path('upload/font/' . $name);

            if (File::exists($oldPath) && !File::exists($newPath)) {
                File::move($oldPath, $newPath);
            }
        }

        if ($request->hasFile('file')) {
            $path = public_path('upload/font/' . $name);

            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            $oldFilePath = $path . '/' . $font->file;

            if (File::exists($oldFilePath)) {
                File::delete($oldFilePath);
            }

            $file = $request->file('file');
            $fileName = UniqueNamer::uniqueFile($path, $file->getClientOriginalName());
            $file->move($path, $fileName);

            $updateData['file'] = $fileName;
        }

        if ($request->hasFile('font_preview')) {
            $path = public_path('upload/font/' . $name);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0777, true, true);
            }

            if ($font->font_preview) {
                $oldPreviewPath = $path . '/' . $font->font_preview;
                if (File::exists($oldPreviewPath)) {
                    File::delete($oldPreviewPath);
                }
            }

            $preview = $request->file('font_preview');
            $previewName = UniqueNamer::uniqueFile($path, $preview->getClientOriginalName());
            $preview->move($path, $previewName);

            $updateData['font_preview'] = $previewName;
        }

        $font->update($updateData);

        return redirect(session('font_list_url', route('fonts.index')))->with('success', 'Font updated successfully.');
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
