@extends('layouts.admin')

@section('content')
    <style>
        .cat-dropzone { border: 2px dashed #b66dff; background: #faf7ff; border-radius: 8px; padding: 18px; min-height: 150px;
            cursor: pointer; transition: background .2s, border-color .2s; text-align: center; }
        .cat-dropzone.zone-active { background: #ede0ff; border-color: #8a3df0; }
        .cat-dropzone .cat-prompt { pointer-events: none; user-select: none; color: #7a4fd0; }
        .cat-dropzone.has-image .cat-prompt { font-size: 12px; padding-top: 8px; margin-top: 8px; border-top: 1px dashed #d9c2ff; }
        #cat-preview { max-width: 140px; max-height: 140px; object-fit: contain; border: 1px solid #eee; border-radius: 6px; background: #fff; }
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
                                    {{ isset($photoshootCategory) ? 'Edit Photoshoot Category' : 'Add New Photoshoot Category' }}
                                </h3>
                                <small
                                    class="text-muted">{{ isset($photoshootCategory) ? 'Update existing category' : 'Create a new Photoshoot Category' }}</small>
                            </div>
                        </div>
                        <a href="{{ session('photoshoot_cat_list_url', route('photoshoot-categories.index')) }}" class="btn btn-light text-primary"
                            style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Categories
                        </a>
                    </div>
                    <form class="forms-sample" id="cat-form"
                        action="{{ isset($photoshootCategory) ? route('photoshoot-categories.update', $photoshootCategory->id) : route('photoshoot-categories.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($photoshootCategory))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Category Name"
                                value="{{ old('name', $photoshootCategory->name ?? '') }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Thumbnail Image <small class="text-warning">Only .webp images are allowed • drag &amp; drop or click</small></label>
                            @php
                                $currentImage = isset($photoshootCategory) && $photoshootCategory->image
                                    ? asset('upload/photoshoot/' . $photoshootCategory->name . '/category image/' . $photoshootCategory->image)
                                    : '';
                            @endphp
                            <div class="cat-dropzone {{ $currentImage ? 'has-image' : '' }}" id="cat-zone">
                                <img id="cat-preview" src="{{ $currentImage }}" alt="" style="{{ $currentImage ? '' : 'display:none;' }}">
                                <div class="cat-prompt">
                                    <i class="mdi mdi-cloud-upload" style="font-size: 34px;"></i>
                                    <div style="font-weight: 600;">Drag &amp; drop a .webp image here</div>
                                    <small class="text-muted">or click to browse</small>
                                </div>
                            </div>
                            <input type="file" id="cat-image" name="image" accept=".webp, image/webp" style="display:none;">
                            @error('image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $photoshootCategory->is_active ?? 1) ? 'checked' : '' }}>
                                    Active
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-gradient-primary me-2">{{ isset($photoshootCategory) ? 'Update' : 'Submit' }}</button>
                        <a href="{{ session('photoshoot_cat_list_url', route('photoshoot-categories.index')) }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            var zone = document.getElementById('cat-zone');
            var input = document.getElementById('cat-image');
            var preview = document.getElementById('cat-preview');
            var isEdit = {{ isset($photoshootCategory) ? 'true' : 'false' }};

            function isWebp(f) {
                return f && (f.type === 'image/webp' || /\.webp$/i.test(f.name || ''));
            }

            function setFile(file) {
                if (!isWebp(file)) {
                    Swal.fire({ icon: 'warning', title: 'Only .webp images are allowed', timer: 2500, showConfirmButton: false });
                    return;
                }
                var dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                var reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = '';
                    zone.classList.add('has-image');
                };
                reader.readAsDataURL(file);
            }

            zone.addEventListener('click', function () { input.click(); });
            input.addEventListener('change', function () {
                if (this.files && this.files[0]) setFile(this.files[0]);
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
                var files = (e.dataTransfer && e.dataTransfer.files) || [];
                if (files.length) setFile(files[0]);
            });

            // On add, require an image before submitting.
            document.getElementById('cat-form').addEventListener('submit', function (e) {
                if (!isEdit && (!input.files || input.files.length === 0)) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Thumbnail required', text: 'Please add a .webp thumbnail image.' });
                }
            });
        });
    </script>
@endsection
