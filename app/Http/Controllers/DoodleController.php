<?php

namespace App\Http\Controllers;

use App\Models\Doodle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DoodleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse|string
     */
    public function index(Request $request)
    {
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.doodle.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:doodles,name',
            'image' => 'required|image|mimes:webp',
            'type' => 'required|in:free,pro',
            'doodle_type' => 'required|in:line,image'
        ]);

        $path = public_path('upload/doodle/' . $request->name);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $imageName = $request->file('image')->getClientOriginalName();
        $request->file('image')->move($path, $imageName);

        Doodle::create([
            'name' => $request->name,
            'image' => $imageName,
            'type' => $request->type,
            'doodle_type' => $request->doodle_type
        ]);

        return redirect()->route('doodles.index')->with('success', 'Doodle created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doodle  $doodle
     * @return \Illuminate\View\View
     */
    public function edit(Doodle $doodle)
    {
        return view('admin.doodle.form', compact('doodle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doodle  $doodle
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Doodle $doodle)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:doodles,name,' . $doodle->id,
            'image' => 'nullable|image|mimes:webp',
            'type' => 'required|in:free,pro',
            'doodle_type' => 'required|in:line,image'
        ]);

        $oldPath = public_path('upload/doodle/' . $doodle->name);
        $newPath = public_path('upload/doodle/' . $request->name);

        // Handle folder rename if name changed
        if ($doodle->name !== $request->name) {
            if (File::exists($oldPath)) {
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

            $imageName = $request->file('image')->getClientOriginalName();
            $request->file('image')->move($newPath, $imageName);
        }

        $doodle->update([
            'name' => $request->name,
            'image' => $imageName,
            'type' => $request->type,
            'doodle_type' => $request->doodle_type
        ]);

        return redirect()->route('doodles.index')->with('success', 'Doodle updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doodle  $doodle
     * @return \Illuminate\Http\JsonResponse
     */
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
