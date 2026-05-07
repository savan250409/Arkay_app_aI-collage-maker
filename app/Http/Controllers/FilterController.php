<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\FilterCategory;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $categoryId = $request->input('category_id', '');
        $perPage = $request->input('per_page', 10);

        $categories = FilterCategory::orderBy('name')->get();

        $query = Filter::with('category');
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        if ($categoryId) {
            $query->where('filter_category_id', $categoryId);
        }
        $filters = $query->latest()->paginate($perPage);
        $filters->appends(['search' => $search, 'per_page' => $perPage, 'category_id' => $categoryId]);

        if ($request->ajax()) {
            return view('admin.filter.index', compact('filters', 'categories', 'categoryId'))->render();
        }

        return view('admin.filter.index', compact('filters', 'categories', 'categoryId', 'search', 'perPage'));
    }

    public function create()
    {
        $categories = FilterCategory::all();
        return view('admin.filter.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'filter_category_id' => 'required|exists:filter_categories,id',
            'name' => 'required|string|max:255',
            'saturation' => 'required|numeric',
            'brightness' => 'required|numeric',
            'contrast' => 'required|numeric',
            'red' => 'required|numeric',
            'green' => 'required|numeric',
            'blue' => 'required|numeric',
            'type' => 'required|in:free,pro', // Added type validation
        ]);

        Filter::create($request->all());

        return redirect()->route('filters.index')->with('success', 'Filter created successfully.');
    }

    public function edit(Filter $filter)
    {
        $categories = FilterCategory::all();
        return view('admin.filter.edit', compact('filter', 'categories'));
    }

    public function update(Request $request, Filter $filter)
    {
        $request->validate([
            'filter_category_id' => 'required|exists:filter_categories,id',
            'name' => 'required|string|max:255',
            'saturation' => 'required|numeric',
            'brightness' => 'required|numeric',
            'contrast' => 'required|numeric',
            'red' => 'required|numeric',
            'green' => 'required|numeric',
            'blue' => 'required|numeric',
            'type' => 'required|in:free,pro', // Added type validation
        ]);

        $filter->update($request->all());

        return redirect()->route('filters.index')->with('success', 'Filter updated successfully.');
    }

    public function destroy(Filter $filter)
    {
        $filter->delete();
        return response()->json(['success' => true]);
    }

    public function changeType(Request $request)
    {
        $filter = Filter::find($request->id);
        if ($filter) {
            $filter->type = $request->type;
            $filter->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function import()
    {
        return view('admin.filter.import');
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $header = array_shift($csvData); // Remove header

        // Column mapping based on CSV: Category,Filter Name,Saturation,Brightness,Contrast,Red,Green,Blue,Type
        // Indices: 0: Category, 1: Name, 2: Saturation, 3: Brightness, 4: Contrast, 5: Red, 6: Green, 7: Blue, 8: Type

        foreach ($csvData as $row) {
            if (count($row) < 9)
                continue;

            $categoryName = trim($row[0]);
            $filterName = trim($row[1]);

            // Skip if key fields are empty
            if ($categoryName === '' || $filterName === '') {
                continue;
            }

            // Defaults matching the Create Form
            $saturation = trim($row[2]) === '' ? 1.0 : (double) trim($row[2]);
            $brightness = trim($row[3]) === '' ? 0.0 : (double) trim($row[3]);
            $contrast = trim($row[4]) === '' ? 1.0 : (double) trim($row[4]);
            $red = trim($row[5]) === '' ? 1.0 : (double) trim($row[5]);
            $green = trim($row[6]) === '' ? 1.0 : (double) trim($row[6]);
            $blue = trim($row[7]) === '' ? 1.0 : (double) trim($row[7]);

            $type = strtolower(trim($row[8]));
            if ($type !== 'pro' && $type !== 'free') {
                $type = 'free'; // Default to free if invalid
            }

            // Find or Create Category
            $category = FilterCategory::firstOrCreate(
                ['name' => $categoryName],
                [
                    'image' => 'default.png',
                ]
            );

            // Create Filter
            Filter::create([
                'filter_category_id' => $category->id,
                'name' => $filterName,
                'saturation' => $saturation,
                'brightness' => $brightness,
                'contrast' => $contrast,
                'red' => $red,
                'green' => $green,
                'blue' => $blue,
                'type' => $type
            ]);
        }

        return redirect()->route('filters.index')->with('success', 'Filters imported successfully.');
    }
}
