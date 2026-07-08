@extends('layouts.admin')

@push('plugin-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
@endpush

@push('plugin-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
@endpush

@section('content')
    @php
        $selStyle = "appearance:none;-webkit-appearance:none;-moz-appearance:none;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 10px center;background-size:12px;padding-right:30px;";
    @endphp

    <style>
        .ps-row { background: #f9f9f9; border: 1px solid #eee; border-radius: 8px; }
        .ps-row .form-label-sm, .form-label-sm { font-size: 12px; font-weight: 600; color: #555; margin-bottom: 4px; display: block; }
        .ps-row .dropify-wrapper { margin-bottom: 0; }
        .ps-remove-btn { width: 34px; height: 34px; padding: 0; display: flex; align-items: center; justify-content: center; background-color: #ff6258; border-color: #ff6258; border-radius: 50%; }
        .ps-country-box { display: flex; flex-wrap: wrap; gap: 6px; }
        .ps-country-chk { display: inline-flex; align-items: center; gap: 4px; margin: 0; padding: 4px 9px; font-size: 12px; font-weight: 600;
            border: 1px solid #d9c2ff; border-radius: 20px; background: #fff; color: #555; cursor: pointer; user-select: none; }
        .ps-country-chk input { margin: 0; cursor: pointer; }
        .ps-country-chk:hover { border-color: #b66dff; }
        .ps-thumb { width: 100%; height: 100px; object-fit: contain; background: #fff; border: 1px solid #eee; border-radius: 6px; }
        .ps-drag-handle { cursor: grab; color: #b66dff; font-size: 22px; line-height: 1; display: inline-flex; align-items: center; justify-content: center; }
        .ps-drag-handle:active { cursor: grabbing; }
        .ps-sortable-ghost { opacity: .45; background: #ede0ff !important; }
        .ps-tab { padding: 8px 18px; border: 1px solid #d9c2ff; background: #fff; color: #7a4fd0; font-weight: 600; cursor: pointer; border-radius: 6px; margin-right: 8px; }
        .ps-tab.active { background: #b66dff; color: #fff; border-color: #b66dff; }
        .ps-count-pill { display: inline-block; padding: 3px 10px; border-radius: 14px; font-size: 12px; font-weight: 700; margin-right: 6px; }
        .ps-count-ok { background: #e4f8ec; color: #1e7e46; }
        .ps-count-bad { background: #fde3e1; color: #c0392b; }
        .ps-bulk-preview { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
        .ps-bulk-preview img { width: 56px; height: 56px; object-fit: contain; border: 1px solid #eee; border-radius: 6px; background: #fff; }
        .ps-bulk-panel { border: 1px dashed #d9c2ff; background: #faf7ff; border-radius: 8px; }
        .ps-dropzone { border: 2px dashed #b66dff; background: #faf7ff; border-radius: 8px; padding: 16px; min-height: 120px; cursor: pointer; transition: background .2s, border-color .2s; }
        .ps-dropzone.zone-active { background: #ede0ff; border-color: #8a3df0; }
        .ps-dropzone .ps-dz-prompt { pointer-events: none; user-select: none; text-align: center; color: #7a4fd0; }
        .ps-dropzone.has-files .ps-dz-prompt { font-size: 12px; padding-top: 8px; margin-top: 8px; border-top: 1px dashed #d9c2ff; }
        .ps-dz-item { position: relative; cursor: grab; }
        .ps-dz-item:active { cursor: grabbing; }
        .ps-dz-item img { width: 64px; height: 64px; object-fit: contain; border: 1px solid #eee; border-radius: 6px; background: #fff; }
        .ps-dz-remove { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; border-radius: 50%; background: #ff6258; color: #fff;
            display: flex; align-items: center; justify-content: center; font-size: 14px; line-height: 1; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,.3); }
    </style>

    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-plus-circle-outline text-primary"
                                style="font-size: 2rem; margin-right: 10px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">
                                    {{ isset($photoshoot) ? 'Edit Photoshoot Group' : 'Add New Photoshoot' }}
                                </h3>
                                <small class="text-muted">
                                    {{ isset($photoshoot) ? 'Manage items in this category / country group' : 'Bulk add or add row by row — each chosen country is stored in its own group row' }}
                                </small>
                            </div>
                        </div>
                        <a href="{{ session('photoshoot_list_url', route('photoshoots.index')) }}" class="btn btn-light text-primary"
                            style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Photoshoots
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($photoshoot))
                        {{-- ================= EDIT: manage items + bulk add ================= --}}
                        <form class="forms-sample" id="ps-edit-form" action="{{ route('photoshoots.update', $photoshoot->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            @php $selCountry = (string) old('country', $photoshoot->country); @endphp
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label-sm">Category</label>
                                    <select class="form-control" name="photoshoot_category_id" required style="{{ $selStyle }}">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ (string) old('photoshoot_category_id', $photoshoot->photoshoot_category_id) === (string) $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('photoshoot_category_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label-sm">Country group</label>
                                    <select class="form-control" name="country" style="{{ $selStyle }}">
                                        <option value="" {{ $selCountry === '' ? 'selected' : '' }}>Global (no country)</option>
                                        @foreach($countries as $code => $label)
                                            <option value="{{ $code }}" {{ $selCountry === (string) $code ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-2 mb-2">
                                <label class="mb-0">Items <small class="text-warning">Only .webp images are allowed</small></label>
                                <button type="button" class="btn btn-sm" id="add-item-btn"
                                    style="background:#fff; color:#b66dff; border:1px solid #b66dff;">
                                    <i class="mdi mdi-plus"></i> Add Single Item
                                </button>
                            </div>

                            <div id="items-wrapper">
                                @foreach(($photoshoot->items ?? []) as $item)
                                    <div class="ps-row p-3 mb-3 photoshoot-item">
                                        <input type="hidden" name="item_type[]" value="existing">
                                        <input type="hidden" name="existing_image[]" value="{{ $item['image'] ?? '' }}">
                                        <div class="row align-items-center">
                                            <div class="col-auto d-flex align-items-center" style="align-self:center;">
                                                <span class="ps-drag-handle" title="Drag to reorder"><i class="mdi mdi-drag-vertical"></i></span>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label-sm">Image</label>
                                                <img class="ps-thumb"
                                                    src="{{ asset('upload/photoshoot/' . ($photoshoot->category->name ?? 'default') . '/photoshoots/' . ($item['image'] ?? '')) }}"
                                                    alt="image">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label-sm">Name</label>
                                                <input type="text" class="form-control" name="item_name[]" value="{{ $item['name'] ?? '' }}" required>
                                            </div>
                                            <div class="col">
                                                <label class="form-label-sm">Hashtag</label>
                                                <input type="text" class="form-control" name="item_hashtag[]" value="{{ $item['hashtag'] ?? '' }}" required>
                                            </div>
                                            <div class="col-auto d-flex justify-content-center" style="margin-top: 26px;">
                                                <button type="button" class="btn btn-danger remove-item-btn shadow-sm ps-remove-btn" title="Remove item">
                                                    <i class="mdi mdi-close" style="font-size: 18px; margin: 0; line-height: 1;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Bulk add-items panel --}}
                            <div class="ps-bulk-panel p-3 mt-3">
                                <label class="mb-2 d-block" style="font-weight:600; color:#7a4fd0;">
                                    <i class="mdi mdi-upload-multiple"></i> Bulk add more items
                                    <small class="text-muted">(optional • upload many images and paste one name / hashtag per line)</small>
                                </label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Images (.webp — drag &amp; drop or click)</label>
                                        <div class="ps-dropzone" id="edit-bulk-zone" style="min-height:96px; padding:12px;">
                                            <div class="ps-bulk-preview" id="edit-bulk-preview"></div>
                                            <div class="ps-dz-prompt">
                                                <i class="mdi mdi-cloud-upload" style="font-size:28px;"></i>
                                                <div style="font-weight:600; font-size:12px;">Drag &amp; drop or click</div>
                                            </div>
                                        </div>
                                        <input type="file" id="edit-bulk-images" name="bulk_images[]" multiple accept=".webp, image/webp" style="display:none;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Names (one per line)</label>
                                        <textarea id="edit-bulk-name" name="bulk_name" class="form-control" rows="4" placeholder="name 1&#10;name 2&#10;name 3"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Hashtags (one per line)</label>
                                        <textarea id="edit-bulk-hashtag" name="bulk_hashtag" class="form-control" rows="4" placeholder="#tag1&#10;#tag2&#10;#tag3"></textarea>
                                    </div>
                                </div>
                                <div id="edit-bulk-count" class="mt-2"></div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
                                <a href="{{ session('photoshoot_list_url', route('photoshoots.index')) }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    @else
                        {{-- ================= CREATE: bulk / row tabs ================= --}}
                        <form class="forms-sample" id="ps-create-form" action="{{ route('photoshoots.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="mode" id="form-mode" value="bulk">

                            <div class="form-group">
                                <label for="photoshoot_category_id">Category</label>
                                <select class="form-control" id="photoshoot_category_id" name="photoshoot_category_id" required
                                    style="{{ $selStyle }}">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('photoshoot_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('photoshoot_category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-flex mt-3 mb-3">
                                <span class="ps-tab active" data-mode="bulk">Bulk Add</span>
                                <span class="ps-tab" data-mode="row">Row by Row</span>
                            </div>

                            {{-- BULK pane --}}
                            <div id="pane-bulk">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Images (.webp — drag &amp; drop or click)</label>
                                        <div class="ps-dropzone" id="bulk-zone">
                                            <div class="ps-bulk-preview" id="bulk-preview"></div>
                                            <div class="ps-dz-prompt">
                                                <i class="mdi mdi-cloud-upload" style="font-size:34px;"></i>
                                                <div style="font-weight:600;">Drag &amp; drop .webp images here</div>
                                                <small class="text-muted">or click to browse (you can add more anytime)</small>
                                            </div>
                                        </div>
                                        <input type="file" id="bulk-images" name="bulk_images[]" multiple accept=".webp, image/webp" style="display:none;">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Names (one per line)</label>
                                        <textarea id="bulk-name" name="bulk_name" class="form-control" rows="6" placeholder="name 1&#10;name 2&#10;name 3">{{ old('bulk_name') }}</textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-sm">Hashtags (one per line)</label>
                                        <textarea id="bulk-hashtag" name="bulk_hashtag" class="form-control" rows="6" placeholder="#tag1&#10;#tag2&#10;#tag3">{{ old('bulk_hashtag') }}</textarea>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label-sm">Country <small class="text-muted">(optional • one or more — applies to the whole batch)</small></label>
                                    <div class="ps-country-box">
                                        @foreach($countries as $code => $label)
                                            <label class="ps-country-chk">
                                                <input type="checkbox" name="bulk_country[]" value="{{ $code }}"> {{ $label }}
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div id="bulk-count" class="mt-3"></div>
                            </div>

                            {{-- ROW pane --}}
                            <div id="pane-row" style="display:none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="mb-0">Photoshoot Entries
                                        <small class="text-warning">Only .webp images are allowed • pick one or more countries per row</small>
                                    </label>
                                    <button type="button" class="btn btn-sm" id="add-row-btn"
                                        style="background:#fff; color:#b66dff; border:1px solid #b66dff;">
                                        <i class="mdi mdi-plus"></i> Add Row
                                    </button>
                                </div>
                                <div id="rows-wrapper"></div>
                                <button type="button" class="btn btn-sm mt-2" id="add-row-btn-bottom"
                                    style="background:#fff; color:#b66dff; border:1px solid #b66dff;">
                                    <i class="mdi mdi-plus"></i> Add Row
                                </button>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-gradient-primary me-2">Submit</button>
                                <a href="{{ session('photoshoot_list_url', route('photoshoots.index')) }}" class="btn btn-light">Cancel</a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script>
        // Shared helpers
        function psLines(text) {
            return (text || '').split(/\r\n|\r|\n/).map(function (s) { return s.trim(); }).filter(function (s) { return s !== ''; });
        }
        function psWebpFilter(fileList) {
            var kept = [], rejected = 0;
            Array.prototype.forEach.call(fileList || [], function (f) {
                if (f && (f.type === 'image/webp' || /\.webp$/i.test(f.name || ''))) kept.push(f);
                else rejected++;
            });
            return { kept: kept, rejected: rejected };
        }
        function psRenderCount(el, imgN, nameN, hashN) {
            var ok = (imgN === nameN && imgN === hashN && imgN > 0);
            var cls = ok ? 'ps-count-ok' : 'ps-count-bad';
            el.innerHTML =
                '<span class="ps-count-pill ' + cls + '">Images: ' + imgN + '</span>' +
                '<span class="ps-count-pill ' + cls + '">Names: ' + nameN + '</span>' +
                '<span class="ps-count-pill ' + cls + '">Hashtags: ' + hashN + '</span>' +
                (ok ? '<span class="text-success" style="font-weight:600;">✓ matched</span>'
                    : '<span class="text-danger" style="font-weight:600;">✗ counts must be equal</span>');
            return ok;
        }
        // Drag & drop multi-file zone. Accumulates .webp files, keeps a hidden
        // <input type=file multiple> in sync via DataTransfer, renders removable
        // thumbnails, and calls onChange whenever the set changes.
        function psSetupDropzone(opts) {
            var zone = opts.zone, input = opts.input, preview = opts.preview, onChange = opts.onChange || function () {};
            var nameEl = opts.nameEl || null, hashEl = opts.hashEl || null;
            var files = [];

            function sameFile(a, b) {
                return a.name === b.name && a.size === b.size && a.lastModified === b.lastModified;
            }
            function render() {
                preview.innerHTML = '';
                files.forEach(function (f, idx) {
                    var item = document.createElement('div');
                    item.className = 'ps-dz-item';
                    item.setAttribute('data-pos', idx);
                    item.title = 'Drag to reorder';
                    var img = document.createElement('img');
                    var reader = new FileReader();
                    reader.onload = function (e) { img.src = e.target.result; };
                    reader.readAsDataURL(f);
                    var rm = document.createElement('span');
                    rm.className = 'ps-dz-remove';
                    rm.innerHTML = '&times;';
                    rm.addEventListener('click', function (e) {
                        e.stopPropagation();
                        var pos = parseInt(item.getAttribute('data-pos'), 10);
                        files.splice(pos, 1);
                        removeLine(nameEl, pos); removeLine(hashEl, pos);
                        sync();
                    });
                    item.appendChild(img); item.appendChild(rm);
                    preview.appendChild(item);
                });
            }
            function sync() {
                var dt = new DataTransfer();
                files.forEach(function (f) { dt.items.add(f); });
                input.files = dt.files;
                if (files.length > 0) zone.classList.add('has-files'); else zone.classList.remove('has-files');
                render();
                onChange(files);
            }
            function addFiles(list) {
                var r = psWebpFilter(list);
                if (r.rejected > 0) Swal.fire({ icon: 'warning', title: 'Only .webp images are allowed', text: r.rejected + ' file(s) skipped.', timer: 2500, showConfirmButton: false });
                r.kept.forEach(function (f) {
                    if (!files.some(function (x) { return sameFile(x, f); })) files.push(f);
                });
                sync();
            }
            // Keep name/hashtag lines paired with the images when reordering/removing.
            function meaningfulLines(el) {
                if (!el) return null;
                var raw = (el.value || '').split(/\r\n|\r|\n/);
                while (raw.length && raw[raw.length - 1].trim() === '') raw.pop();
                return raw;
            }
            function reorderLines(el, order) {
                var raw = meaningfulLines(el);
                if (!raw || raw.length !== order.length) return;
                el.value = order.map(function (i) { return raw[i]; }).join('\n');
            }
            function removeLine(el, pos) {
                var raw = meaningfulLines(el);
                if (!raw || pos >= raw.length) return;
                raw.splice(pos, 1);
                el.value = raw.join('\n');
            }

            zone.addEventListener('click', function (e) {
                if (e.target.closest('.ps-dz-remove')) return;
                if (e.target.closest('.ps-dz-item')) return; // don't open browse when clicking a thumbnail
                input.click();
            });

            // Drag-to-reorder thumbnails (fallback mode so it doesn't clash with file drop).
            if (window.Sortable) {
                Sortable.create(preview, {
                    animation: 150,
                    forceFallback: true,
                    ghostClass: 'ps-sortable-ghost',
                    onEnd: function () {
                        var order = Array.prototype.map.call(preview.children, function (n) {
                            return parseInt(n.getAttribute('data-pos'), 10);
                        });
                        var old = files.slice();
                        files = order.map(function (i) { return old[i]; });
                        reorderLines(nameEl, order);
                        reorderLines(hashEl, order);
                        sync();
                    }
                });
            }
            input.addEventListener('change', function () {
                addFiles(this.files);
            });
            ['dragenter', 'dragover'].forEach(function (ev) {
                zone.addEventListener(ev, function (e) { e.preventDefault(); e.stopPropagation(); zone.classList.add('zone-active'); });
            });
            zone.addEventListener('dragleave', function (e) {
                e.preventDefault(); e.stopPropagation();
                if (e.relatedTarget && zone.contains(e.relatedTarget)) return;
                zone.classList.remove('zone-active');
            });
            zone.addEventListener('drop', function (e) {
                e.preventDefault(); e.stopPropagation();
                zone.classList.remove('zone-active');
                addFiles((e.dataTransfer && e.dataTransfer.files) || []);
            });

            return { getFiles: function () { return files; } };
        }

        $(document).ready(function () {
            @if(isset($photoshoot))
                /* ---------- EDIT ---------- */
                var itemIndex = 0;
                function newItemHtml(idx) {
                    var imgId = 'ps-new-' + idx;
                    return `
                    <div class="ps-row p-3 mb-3 photoshoot-item">
                        <input type="hidden" name="item_type[]" value="new">
                        <div class="row align-items-start">
                            <div class="col-auto d-flex align-items-center" style="margin-top: 26px;">
                                <span class="ps-drag-handle" title="Drag to reorder"><i class="mdi mdi-drag-vertical"></i></span>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-sm">Image <small class="text-warning">.webp only</small></label>
                                <input type="file" id="${imgId}" name="images[]" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="100" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-sm">Name</label>
                                <input type="text" class="form-control" name="item_name[]" placeholder="Name" required>
                            </div>
                            <div class="col">
                                <label class="form-label-sm">Hashtag</label>
                                <input type="text" class="form-control" name="item_hashtag[]" placeholder="#hashtag" required>
                            </div>
                            <div class="col-auto d-flex justify-content-center" style="margin-top: 26px;">
                                <button type="button" class="btn btn-danger remove-item-btn shadow-sm ps-remove-btn" title="Remove item">
                                    <i class="mdi mdi-close" style="font-size: 18px; margin: 0; line-height: 1;"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
                }
                $('#add-item-btn').on('click', function () {
                    itemIndex++;
                    var imgId = 'ps-new-' + itemIndex;
                    var $row = $(newItemHtml(itemIndex));
                    // Add new item on top so it shows first (drag to change).
                    $('#items-wrapper').prepend($row);
                    $('#' + imgId).dropify({ imgFileExtensions: ['webp'] });
                });
                if (window.Sortable) {
                    Sortable.create(document.getElementById('items-wrapper'), { handle: '.ps-drag-handle', animation: 150, ghostClass: 'ps-sortable-ghost' });
                }
                $(document).on('click', '.remove-item-btn', function () {
                    var $row = $(this).closest('.photoshoot-item');
                    $row.slideUp(180, function () { $row.remove(); });
                });

                // Bulk add-items live count + webp filter
                var editCountEl = document.getElementById('edit-bulk-count');
                function editRefresh() {
                    var imgN = editDz.getFiles().length,
                        nameN = psLines($('#edit-bulk-name').val()).length,
                        hashN = psLines($('#edit-bulk-hashtag').val()).length;
                    if (imgN === 0 && nameN === 0 && hashN === 0) { editCountEl.innerHTML = ''; return true; }
                    return psRenderCount(editCountEl, imgN, nameN, hashN);
                }
                var editDz = psSetupDropzone({
                    zone: document.getElementById('edit-bulk-zone'),
                    input: document.getElementById('edit-bulk-images'),
                    preview: document.getElementById('edit-bulk-preview'),
                    nameEl: document.getElementById('edit-bulk-name'),
                    hashEl: document.getElementById('edit-bulk-hashtag'),
                    onChange: editRefresh
                });
                $('#edit-bulk-name, #edit-bulk-hashtag').on('input', editRefresh);

                $('#ps-edit-form').on('submit', function (e) {
                    var itemCount = $('#items-wrapper .photoshoot-item').length;
                    var hasBulk = editDz.getFiles().length > 0 || psLines($('#edit-bulk-name').val()).length > 0 || psLines($('#edit-bulk-hashtag').val()).length > 0;
                    if (itemCount === 0 && !hasBulk) {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Nothing to save', text: 'Keep at least one item or bulk-add some.', });
                        return;
                    }
                    if (hasBulk && !editRefresh()) {
                        e.preventDefault();
                        Swal.fire({ icon: 'warning', title: 'Counts do not match', text: 'Bulk images, names and hashtags must be equal in count.' });
                    }
                });
            @else
                /* ---------- CREATE: tabs ---------- */
                function setMode(mode) {
                    $('#form-mode').val(mode);
                    $('.ps-tab').removeClass('active');
                    $('.ps-tab[data-mode="' + mode + '"]').addClass('active');
                    if (mode === 'bulk') {
                        $('#pane-bulk').show(); $('#pane-row').hide();
                        $('#pane-bulk :input').prop('disabled', false);
                        $('#pane-row :input').prop('disabled', true);
                    } else {
                        $('#pane-row').show(); $('#pane-bulk').hide();
                        $('#pane-row :input').prop('disabled', false);
                        $('#pane-bulk :input').prop('disabled', true);
                        if ($('.photoshoot-row').length === 0) addRow();
                    }
                }
                $('.ps-tab').on('click', function () { setMode($(this).data('mode')); });

                /* Bulk pane logic */
                var bulkCountEl = document.getElementById('bulk-count');
                function bulkRefresh() {
                    return psRenderCount(bulkCountEl, bulkDz.getFiles().length, psLines($('#bulk-name').val()).length, psLines($('#bulk-hashtag').val()).length);
                }
                var bulkDz = psSetupDropzone({
                    zone: document.getElementById('bulk-zone'),
                    input: document.getElementById('bulk-images'),
                    preview: document.getElementById('bulk-preview'),
                    nameEl: document.getElementById('bulk-name'),
                    hashEl: document.getElementById('bulk-hashtag'),
                    onChange: bulkRefresh
                });
                $('#bulk-name, #bulk-hashtag').on('input', bulkRefresh);

                /* Row pane logic */
                var rowIndex = 0;
                function rowHtml(idx) {
                    var imgId = 'ps-img-' + idx;
                    return `
                    <div class="ps-row p-3 mb-3 photoshoot-row">
                        <div class="row align-items-start">
                            <div class="col-auto d-flex align-items-center" style="margin-top: 28px;">
                                <span class="ps-drag-handle" title="Drag to reorder"><i class="mdi mdi-drag-vertical"></i></span>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-sm">Image <small class="text-warning">.webp only</small></label>
                                <input type="file" id="${imgId}" name="images[${idx}]" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="110" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm">Name</label>
                                <input type="text" class="form-control" name="rows[${idx}][name]" placeholder="Name" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm">Hashtag</label>
                                <input type="text" class="form-control" name="rows[${idx}][hashtag]" placeholder="#hashtag" required>
                            </div>
                            <div class="col">
                                <label class="form-label-sm">Country <small class="text-muted">(optional • one or more)</small></label>
                                <div class="ps-country-box">
                                    @foreach($countries as $code => $label)
                                        <label class="ps-country-chk">
                                            <input type="checkbox" name="rows[${idx}][country][]" value="{{ $code }}"> {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-auto d-flex justify-content-center" style="margin-top: 28px;">
                                <button type="button" class="btn btn-danger remove-row-btn shadow-sm ps-remove-btn" title="Remove row">
                                    <i class="mdi mdi-close" style="font-size: 18px; margin: 0; line-height: 1;"></i>
                                </button>
                            </div>
                        </div>
                    </div>`;
                }
                function addRow(prepend) {
                    rowIndex++;
                    var imgId = 'ps-img-' + rowIndex;
                    var $row = $(rowHtml(rowIndex));
                    if (prepend) { $('#rows-wrapper').prepend($row); } else { $('#rows-wrapper').append($row); }
                    $('#' + imgId).dropify({ imgFileExtensions: ['webp'] });
                    toggleRemoveButtons();
                }
                if (window.Sortable) {
                    Sortable.create(document.getElementById('rows-wrapper'), { handle: '.ps-drag-handle', animation: 150, ghostClass: 'ps-sortable-ghost' });
                }
                function toggleRemoveButtons() {
                    var rows = $('.photoshoot-row');
                    rows.find('.remove-row-btn').css('visibility', rows.length <= 1 ? 'hidden' : 'visible');
                }
                $('#add-row-btn, #add-row-btn-bottom').on('click', function () {
                    // Add new row on top so it shows first (drag to reorder).
                    addRow(true);
                    $('html, body').animate({ scrollTop: $('#rows-wrapper').offset().top - 130 }, 300);
                });
                $(document).on('click', '.remove-row-btn', function () {
                    var $row = $(this).closest('.photoshoot-row');
                    $row.slideUp(200, function () { $row.remove(); toggleRemoveButtons(); });
                });

                /* Submit validation */
                $('#ps-create-form').on('submit', function (e) {
                    var mode = $('#form-mode').val();
                    if (mode === 'bulk') {
                        if (bulkDz.getFiles().length === 0) {
                            e.preventDefault();
                            Swal.fire({ icon: 'warning', title: 'No images selected', text: 'Please select at least one .webp image.' });
                            return;
                        }
                        if (!bulkRefresh()) {
                            e.preventDefault();
                            Swal.fire({ icon: 'warning', title: 'Counts do not match', text: 'Images, names and hashtags must be equal in count.' });
                        }
                    }
                });

                // default mode
                setMode('bulk');
            @endif
        });
    </script>
@endsection
