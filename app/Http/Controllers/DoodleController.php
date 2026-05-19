<?php

namespace App\Http\Controllers;

use App\Models\Doodle;
use App\Support\UniqueNamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DoodleController extends Controller
{
    public function index(Request $request)
    {
        session(['doodle_list_url' => $request->fullUrl()]);

        $query = Doodle::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $perPage = $request->input('per_page', 10);
        $doodles = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return view('admin.doodle.index', compact('doodles'))->render();
        }

        return view('admin.doodle.index', compact('doodles'));
    }

    public function create()
    {
        return view('admin.doodle.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:webp',
            'type' => 'required|in:free,pro',
            'doodle_type' => 'required|in:line,image'
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $name = UniqueNamer::uniqueName('doodles', 'name', $request->name);
        $path = public_path('upload/doodle/' . $name);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $imageName = UniqueNamer::uniqueFile($path, $request->file('image')->getClientOriginalName());
        $request->file('image')->move($path, $imageName);

        Doodle::create([
            'name' => $name,
            'image' => $imageName,
            'type' => $request->type,
            'doodle_type' => $request->doodle_type
        ]);

        return redirect(session('doodle_list_url', route('doodles.index')))->with('success', 'Doodle created successfully.');
    }

    public function edit(Doodle $doodle)
    {
        return view('admin.doodle.form', compact('doodle'));
    }

    public function update(Request $request, Doodle $doodle)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:webp',
            'type' => 'required|in:free,pro',
            'doodle_type' => 'required|in:line,image'
        ], [
            'image.mimes' => 'Only .webp images are allowed.'
        ]);

        $name = UniqueNamer::uniqueName('doodles', 'name', $request->name, $doodle->id);

        $oldPath = public_path('upload/doodle/' . $doodle->name);
        $newPath = public_path('upload/doodle/' . $name);

        // Handle folder rename if name changed
        if ($doodle->name !== $name) {
            if (File::exists($oldPath) && !File::exists($newPath)) {
                File::move($oldPath, $newPath);
            } else {
                if (!File::exists($newPath)) {
                    File::makeDirectory($newPath, 0777, true, true);
                }
            }
        } else {
            if (!File::exists($newPath)) {
                File::makeDirectory($newPath, 0777, true, true);
            }
        }

        $imageName = $doodle->image;

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image
            $oldImagePath = $newPath . '/' . $doodle->image;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }

            $imageName = UniqueNamer::uniqueFile($newPath, $request->file('image')->getClientOriginalName());
            $request->file('image')->move($newPath, $imageName);
        }

        $doodle->update([
            'name' => $name,
            'image' => $imageName,
            'type' => $request->type,
            'doodle_type' => $request->doodle_type
        ]);

        return redirect(session('doodle_list_url', route('doodles.index')))->with('success', 'Doodle updated successfully.');
    }

    public function destroy(Doodle $doodle)
    {
        $path = public_path('upload/doodle/' . $doodle->name);
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        $doodle->delete();
        return response()->json(['success' => true]);
    }

    public function changeType(Request $request)
    {
        $doodle = Doodle::find($request->id);
        if ($doodle) {
            $doodle->type = $request->type;
            $doodle->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function updateOrder(Request $request)
    {
        $order = $request->order;
        $totalCount = count($order);
        foreach ($order as $index => $id) {
            Doodle::where('id', $id)->update(['row_order' => $totalCount - $index]);
        }
        \App\Support\ApiCache::flushDoodles();
        return response()->json(['success' => true]);
    }
}
