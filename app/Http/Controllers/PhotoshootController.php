<?php

namespace App\Http\Controllers;

use App\Models\Photoshoot;
use App\Models\PhotoshootCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class PhotoshootController extends Controller
{
    public function index(Request $request)
    {
        session(['photoshoot_list_url' => $request->fullUrl()]);

        $search = $request->input('search', '');
        $perPage = $request->input('per_page', 10);
        $categoryId = $request->input('category_id', '');
        $country = $request->input('country', '');

        $categories = PhotoshootCategory::orderBy('name')->get();
        $countries = Photoshoot::countries();

        $query = Photoshoot::with('category');
        if ($search) {
            $query->whereHas('category', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
        if ($categoryId) {
            $query->where('photoshoot_category_id', $categoryId);
        }
        if ($country === 'global') {
            $query->where('country', Photoshoot::GLOBAL_COUNTRY);
        } elseif ($country !== '') {
            $query->where('country', $country);
        }
        $groups = $query->latest()->paginate($perPage);
        $groups->appends([
            'search' => $search,
            'per_page' => $perPage,
            'category_id' => $categoryId,
            'country' => $country,
        ]);

        if ($request->ajax()) {
            return view('admin.photoshoot.index', compact('groups', 'categories', 'categoryId', 'countries', 'country'))->render();
        }

        return view('admin.photoshoot.index', compact('groups', 'search', 'perPage', 'categories', 'categoryId', 'countries', 'country'));
    }

    public function create()
    {
        $categories = PhotoshootCategory::orderBy('name')->get();
        $countries = Photoshoot::countries();
        return view('admin.photoshoot.form', compact('categories', 'countries'));
    }

    /**
     * Dispatch to the row-by-row or bulk create flow.
     */
    public function store(Request $request)
    {
        if ($request->input('mode') === 'bulk') {
            return $this->storeBulk($request);
        }

        return $this->storeRows($request);
    }

    /**
     * Row-by-row create. Each entry = one image + name + hashtag + chosen
     * countries. Every entry is appended into the (category, country) group row
     * for each selected country; if none is picked it goes into the global ('')
     * row. A category therefore keeps at most 6 rows (5 countries + 1 global).
     */
    private function storeRows(Request $request)
    {
        $request->validate([
            'photoshoot_category_id' => 'required|exists:photoshoot_categories,id',
            'images' => 'required|array|min:1',
            'images.*' => ['required', $this->webpRule()],
            'rows' => 'required|array|min:1',
            'rows.*.name' => 'required|string|max:255',
            'rows.*.hashtag' => 'required|string|max:255',
            'rows.*.country' => 'nullable|array',
            'rows.*.country.*' => 'nullable|string|max:5',
        ], [
            'images.required' => 'Please add at least one photoshoot image.',
            'rows.*.name.required' => 'Name is required for every row.',
            'rows.*.hashtag.required' => 'Hashtag is required for every row.',
        ]);

        $category = PhotoshootCategory::findOrFail($request->photoshoot_category_id);
        $path = $this->categoryPath($category->name);

        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $images = $request->file('images', []);
        $rows = $request->input('rows', []);
        $used = [];

        // Build the batch per country group, preserving the on-screen row order
        // (which the admin can drag to arrange).
        $batchByCountry = [];
        $created = 0;
        foreach ($images as $idx => $image) {
            if (!$image) {
                continue;
            }

            $imageName = $this->reserveImageName($path, $image->getClientOriginalName(), $used);
            $image->move($path, $imageName);

            $row = $rows[$idx] ?? [];
            $item = [
                'name' => $this->cleanName($row['name'] ?? ''),
                'hashtag' => $row['hashtag'] ?? '',
                'image' => $imageName,
            ];

            // Which country groups this item belongs to (global if none).
            $targets = $this->validCountries($row['country'] ?? []);
            if (empty($targets)) {
                $targets = [Photoshoot::GLOBAL_COUNTRY];
            }

            foreach ($targets as $countryCode) {
                $batchByCountry[$countryCode][] = $item;
            }
            $created++;
        }

        // Prepend the batch (shown order) before any existing items in the group.
        foreach ($batchByCountry as $countryCode => $items) {
            $group = Photoshoot::firstOrNew([
                'photoshoot_category_id' => $category->id,
                'country' => (string) $countryCode,
            ]);
            $group->items = array_merge($items, $group->items ?? []);
            $group->save();
        }

        return redirect(session('photoshoot_list_url', route('photoshoots.index')))
            ->with('success', $created . ' photoshoot(s) added successfully.');
    }

    /**
     * Bulk create. Many images uploaded at once, with all names and all
     * hashtags pasted as line-separated lists, and the country picked once for
     * the whole batch. Images/names/hashtags are paired positionally; the
     * three counts must match or the request is rejected with a warning.
     */
    private function storeBulk(Request $request)
    {
        $request->validate([
            'photoshoot_category_id' => 'required|exists:photoshoot_categories,id',
            'bulk_images' => 'required|array|min:1',
            'bulk_images.*' => ['required', $this->webpRule()],
            'bulk_name' => 'required|string',
            'bulk_hashtag' => 'required|string',
            'bulk_country' => 'nullable|array',
            'bulk_country.*' => 'nullable|string|max:5',
        ], [
            'bulk_images.required' => 'Please select at least one image.',
            'bulk_name.required' => 'Please enter the names (one per line).',
            'bulk_hashtag.required' => 'Please enter the hashtags (one per line).',
        ]);

        $images = array_values($request->file('bulk_images', []));
        $names = $this->splitLines($request->input('bulk_name'));
        $hashtags = $this->splitLines($request->input('bulk_hashtag'));

        // Counts must be equal, otherwise the positional pairing is ambiguous.
        if (count($images) !== count($names) || count($images) !== count($hashtags)) {
            return back()->withInput()->withErrors([
                'bulk_images' => 'Counts must be equal — images: ' . count($images)
                    . ', names: ' . count($names) . ', hashtags: ' . count($hashtags) . '.',
            ]);
        }

        $category = PhotoshootCategory::findOrFail($request->photoshoot_category_id);
        $path = $this->categoryPath($category->name);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $targets = $this->validCountries($request->input('bulk_country', []));
        if (empty($targets)) {
            $targets = [Photoshoot::GLOBAL_COUNTRY];
        }

        $used = [];
        $batch = [];
        $created = 0;
        foreach ($images as $i => $image) {
            $imageName = $this->reserveImageName($path, $image->getClientOriginalName(), $used);
            $image->move($path, $imageName);

            $batch[] = [
                'name' => $this->cleanName($names[$i]),
                'hashtag' => $hashtags[$i],
                'image' => $imageName,
            ];
            $created++;
        }

        // Prepend the batch (shown order) before any existing items in each group.
        foreach ($targets as $countryCode) {
            $group = Photoshoot::firstOrNew([
                'photoshoot_category_id' => $category->id,
                'country' => $countryCode,
            ]);
            $group->items = array_merge($batch, $group->items ?? []);
            $group->save();
        }

        return redirect(session('photoshoot_list_url', route('photoshoots.index')))
            ->with('success', $created . ' photoshoot(s) added successfully.');
    }

    public function edit(Photoshoot $photoshoot)
    {
        $countries = Photoshoot::countries();
        $photoshoot->load('category');
        return view('admin.photoshoot.form', compact('photoshoot', 'countries'));
    }

    /**
     * Rebuild a group's items array: keep the existing items the admin left in
     * place (with edited name/hashtag), any single item rows added, plus any
     * bulk-added batch (many images + line-separated names/hashtags), in order.
     */
    public function update(Request $request, Photoshoot $photoshoot)
    {
        $request->validate([
            'item_type' => 'nullable|array',
            'item_type.*' => 'in:existing,new',
            'item_name' => 'nullable|array',
            'item_name.*' => 'required|string|max:255',
            'item_hashtag' => 'nullable|array',
            'item_hashtag.*' => 'required|string|max:255',
            'existing_image' => 'nullable|array',
            'images' => 'nullable|array',
            'images.*' => [$this->webpRule()],
            'bulk_images' => 'nullable|array',
            'bulk_images.*' => [$this->webpRule()],
            'bulk_name' => 'nullable|string',
            'bulk_hashtag' => 'nullable|string',
        ]);

        $category = $photoshoot->category;
        $path = $this->categoryPath($category->name);
        if (!File::exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }

        $types = $request->input('item_type', []);
        $names = $request->input('item_name', []);
        $hashtags = $request->input('item_hashtag', []);
        $existingImages = $request->input('existing_image', []);
        $newFiles = $request->file('images', []);

        $existingPtr = 0;
        $newPtr = 0;
        $domItems = [];
        $used = [];

        // Items in the exact on-screen order (the admin can drag to reorder).
        foreach ($types as $i => $type) {
            $name = $this->cleanName($names[$i] ?? '');
            $hashtag = $hashtags[$i] ?? '';

            if ($type === 'existing') {
                $image = $existingImages[$existingPtr] ?? null;
                $existingPtr++;
                if (!$image) {
                    continue;
                }
                $used[] = $image;
            } else {
                $file = $newFiles[$newPtr] ?? null;
                $newPtr++;
                if (!$file) {
                    continue;
                }
                $image = $this->reserveImageName($path, $file->getClientOriginalName(), $used);
                $file->move($path, $image);
            }

            $domItems[] = ['name' => $name, 'hashtag' => $hashtag, 'image' => $image];
        }

        // Optional bulk add: many images + line-separated names/hashtags.
        $bulkItems = [];
        $bulkImages = array_values($request->file('bulk_images', []));
        if (!empty($bulkImages)) {
            $bNames = $this->splitLines($request->input('bulk_name'));
            $bHashtags = $this->splitLines($request->input('bulk_hashtag'));

            if (count($bulkImages) !== count($bNames) || count($bulkImages) !== count($bHashtags)) {
                return back()->withInput()->withErrors([
                    'bulk_images' => 'Counts must be equal — images: ' . count($bulkImages)
                        . ', names: ' . count($bNames) . ', hashtags: ' . count($bHashtags) . '.',
                ]);
            }

            foreach ($bulkImages as $i => $file) {
                $image = $this->reserveImageName($path, $file->getClientOriginalName(), $used);
                $file->move($path, $image);
                $bulkItems[] = ['name' => $this->cleanName($bNames[$i]), 'hashtag' => $bHashtags[$i], 'image' => $image];
            }
        }

        // Bulk-added items go on top (newest), then the on-screen arranged items.
        $finalItems = array_merge($bulkItems, $domItems);

        if (empty($finalItems)) {
            return back()->withInput()->withErrors([
                'item_type' => 'A group must have at least one item.',
            ]);
        }

        // Delete image files that were removed and are not used by any other group.
        $oldImages = collect($photoshoot->items ?? [])->pluck('image')->filter()->all();
        $keptImages = collect($finalItems)->pluck('image')->all();
        $removed = array_diff($oldImages, $keptImages);
        foreach ($removed as $img) {
            $this->deleteImageIfUnreferenced($category, $img, $photoshoot->id, $keptImages);
        }
        $this->deleteFolderIfEmpty($this->categoryPath($category->name));

        $photoshoot->update(['items' => $finalItems]);

        return redirect(session('photoshoot_list_url', route('photoshoots.index')))
            ->with('success', 'Photoshoot group updated successfully.');
    }

    public function destroy(Photoshoot $photoshoot)
    {
        $category = $photoshoot->category;
        foreach ((array) ($photoshoot->items ?? []) as $item) {
            if (!empty($item['image'])) {
                $this->deleteImageIfUnreferenced($category, $item['image'], $photoshoot->id, []);
            }
        }

        if ($category) {
            $this->deleteFolderIfEmpty($this->categoryPath($category->name));
        }

        $photoshoot->delete();
        return response()->json(['success' => true]);
    }

    /* ----------------------------------------------------------------------- */

    private function categoryPath(string $categoryName): string
    {
        return public_path('upload/photoshoot/' . $categoryName . '/photoshoots');
    }

    /**
     * Reserve a unique filename inside $path. If the original name is already
     * taken (same category = same folder), a timestamp is appended; a counter
     * is added when several same-named files land in the same second, so images
     * are never overwritten. $used tracks names reserved earlier in the same
     * request (before their files exist on disk yet).
     */
    private function reserveImageName(string $path, string $original, array &$used): string
    {
        if (!$this->nameTaken($path, $original, $used)) {
            $used[] = $original;
            return $original;
        }

        $ext = pathinfo($original, PATHINFO_EXTENSION);
        $base = pathinfo($original, PATHINFO_FILENAME);
        $stamp = time();
        $n = 0;

        do {
            $candidate = $base . '_' . $stamp . ($n > 0 ? '_' . $n : '') . ($ext !== '' ? '.' . $ext : '');
            $n++;
        } while ($this->nameTaken($path, $candidate, $used));

        $used[] = $candidate;
        return $candidate;
    }

    private function nameTaken(string $path, string $name, array $used): bool
    {
        return in_array($name, $used, true) || File::exists($path . '/' . $name);
    }

    /** Remove the folder when it holds fewer than one file (i.e. it is empty). */
    private function deleteFolderIfEmpty(string $path): void
    {
        if (File::isDirectory($path) && count(File::files($path)) < 1) {
            File::deleteDirectory($path);
        }
    }

    /**
     * Validation rule that accepts only .webp images, detecting the format from
     * the file content via GD (getimagesize) instead of the server MIME/finfo
     * guesser — which misidentifies webp on some Apache/Windows setups and
     * wrongly rejects genuine .webp uploads. Falls back to the .webp extension.
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

    /** Strip a leading bullet marker ("- ") from a name. */
    private function cleanName($name): string
    {
        return preg_replace('/^-\s+/', '', trim((string) $name));
    }

    /** Split a textarea value into trimmed, non-empty lines. */
    private function splitLines($text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $text);
        $lines = array_map('trim', $lines);
        return array_values(array_filter($lines, fn ($l) => $l !== ''));
    }

    /** De-duplicated list of valid lowercase country codes. */
    private function validCountries($selected): array
    {
        $allowed = array_keys(Photoshoot::countries());
        $valid = [];
        foreach ((array) $selected as $c) {
            $c = strtolower(trim((string) $c));
            if ($c !== '' && in_array($c, $allowed, true) && !in_array($c, $valid, true)) {
                $valid[] = $c;
            }
        }
        return $valid;
    }

    /**
     * Delete a physical image only when no other group row in the same category
     * (nor the surviving items of the current row) still references it — images
     * can be shared across several country groups.
     */
    private function deleteImageIfUnreferenced($category, string $image, int $currentRowId, array $keptImages): void
    {
        if (!$category) {
            return;
        }

        if (in_array($image, $keptImages, true)) {
            return;
        }

        $usedElsewhere = Photoshoot::where('photoshoot_category_id', $category->id)
            ->where('id', '!=', $currentRowId)
            ->get()
            ->contains(function ($group) use ($image) {
                return collect($group->items ?? [])->pluck('image')->contains($image);
            });

        if ($usedElsewhere) {
            return;
        }

        $file = $this->categoryPath($category->name) . '/' . $image;
        if (File::exists($file)) {
            File::delete($file);
        }
    }
}
