@extends('layouts.admin')

@section('content')
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
                                    {{ isset($background) ? 'Edit Background' : 'Add New Background' }}
                                </h3>
                                <small class="text-muted">{{ isset($background) ? 'Update existing background' : 'Create a new Background' }}</small>
                            </div>
                        </div>
                        <a href="{{ session('bg_list_url', route('backgrounds.index')) }}" class="btn btn-light text-primary"
                            style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Backgrounds
                        </a>
                    </div>

                    <form class="forms-sample"
                        action="{{ isset($background) ? route('backgrounds.update', $background->id) : route('backgrounds.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($background))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="background_category_id">Category</label>
                            <select class="form-control" id="background_category_id" name="background_category_id" required style="
                                appearance: none;
                                -webkit-appearance: none;
                                -moz-appearance: none;
                                background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');
                                background-repeat: no-repeat;
                                background-position: right 10px center;
                                background-size: 12px;
                                padding-right: 30px;">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        data-is-premium="{{ $category->is_premium }}"
                                        {{ (old('background_category_id') == $category->id || (isset($background) && $background->background_category_id == $category->id)) ? 'selected' : '' }}>
                                        {{ $category->name }} {{ $category->is_premium ? '(Pro)' : '(Free)' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('background_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Image Grid -->
                        <style>
                            .upload-zone {
                                border: 2px dashed #b66dff;
                                background: #faf7ff;
                                border-radius: 6px;
                                padding: 14px;
                                transition: background 0.2s ease, border-color 0.2s ease;
                                cursor: pointer;
                                min-height: 160px;
                            }
                            .upload-zone.zone-active {
                                background: #ede0ff;
                                border-color: #8a3df0;
                            }
                            .upload-zone .zone-prompt {
                                pointer-events: none;
                                user-select: none;
                            }
                            .upload-zone.has-images .zone-prompt {
                                padding: 10px 0 4px !important;
                                border-top: 1px dashed #d9c2ff;
                                margin-top: 8px;
                            }
                            .upload-zone.has-images .zone-prompt .zone-icon { font-size: 22px !important; }
                            .upload-zone.has-images .zone-prompt .zone-title { font-size: 13px !important; margin: 0 !important; }
                            .upload-zone.has-images .zone-prompt .zone-sub { font-size: 11px !important; }
                        </style>
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="mb-0">Background Images (Drag &amp; drop to upload • Drag handle to reorder) <small class="text-warning">Only .webp images are allowed</small></label>
                                <div>
                                    @if(isset($background) && !empty($background->images))
                                        <button type="button" class="btn btn-outline-danger btn-sm" id="delete-all-btn">
                                            <i class="mdi mdi-delete"></i> Delete All
                                        </button>
                                    @endif
                                </div>
                            </div>

                            <input type="file" id="multi-input" multiple style="display: none;" accept=".webp, image/webp">

                            <div id="image-upload-zone" class="upload-zone">
                                <div class="zone-prompt text-center py-4">
                                    <i class="mdi mdi-cloud-upload zone-icon" style="font-size: 48px; color: #b66dff;"></i>
                                    <p class="zone-title mb-1 mt-2" style="color: #555; font-weight: 500;">Drag &amp; drop images here</p>
                                    <small class="zone-sub text-muted">or click anywhere in this area to browse files</small>
                                </div>

                                <div id="sortable-images" class="row">
                                @if(isset($background) && !empty($background->images))
                                    @foreach($background->images as $item)
                                        @php
                                            $imgName   = is_array($item) ? $item['image']      : $item;
                                            $imgPremium = is_array($item) ? ($item['is_premium'] ?? 0) : 0;
                                        @endphp
                                        <div class="col-6 col-sm-4 col-md-2 mb-3 image-input-row">
                                            <div class="card border p-1 h-100 shadow-sm position-relative">
                                                <input type="hidden" name="item_type[]" value="existing">
                                                <input type="hidden" name="existing_images[]" value="{{ $imgName }}">
                                                <input type="hidden" name="existing_premiums[]" value="{{ $imgPremium }}" class="existing-premium-val">

                                                <span class="drag-handle position-absolute" title="Drag to reorder"
                                                    style="top: 4px; left: 4px; width: 22px; height: 22px; background: rgba(0,0,0,0.55); color:#fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: grab; z-index: 10;">
                                                    <i class="mdi mdi-drag" style="font-size: 14px; margin: 0;"></i>
                                                </span>

                                                <div class="d-flex justify-content-center align-items-center"
                                                    style="height: 100px; background: #f8f9fa; border-radius: 4px; overflow: hidden;">
                                                    <img src="{{ asset('upload/background/' . $background->category->name . '/backgrounds/' . $imgName) }}"
                                                        alt="Image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                                </div>

                                                <div class="text-center mt-1 mb-1" style="font-size: 11px;">
                                                    <div class="form-check form-check-flat form-check-primary d-inline-block" style="margin: 0; padding-left: 0;">
                                                        <label class="form-check-label" style="font-size: 11px;">
                                                            <input type="checkbox" class="form-check-input image-premium-toggle"
                                                                {{ $imgPremium ? 'checked' : '' }}>
                                                            Pro
                                                            <i class="input-helper"></i>
                                                        </label>
                                                    </div>
                                                </div>

                                                <button type="button"
                                                    class="btn btn-danger btn-sm p-0 rounded-circle remove-btn position-absolute"
                                                    style="top: 5px; right: 5px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; z-index: 10;">
                                                    <i class="mdi mdi-close" style="font-size: 14px; margin: 0;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                </div>
                            </div>

                            @error('images')
                                <small class="text-danger d-block mt-2">{{ $message }}</small>
                            @enderror
                            @foreach($errors->get('images.*') as $messages)
                                @foreach($messages as $message)
                                    <small class="text-danger d-block">{{ $message }}</small>
                                @endforeach
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-gradient-primary me-2">{{ isset($background) ? 'Update' : 'Submit' }}</button>
                            <a href="{{ session('bg_list_url', route('backgrounds.index')) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function () {
            var categoryIsPremium = false;

            // When category changes, auto-toggle all Pro checkboxes — no alert shown
            function syncCategoryPremium() {
                var selected = $('#background_category_id option:selected');
                categoryIsPremium = (selected.data('is-premium') == 1);

                $('.image-premium-toggle').each(function () {
                    if (categoryIsPremium) {
                        $(this).prop('checked', true).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });

                // Keep existing_premiums hidden inputs in sync
                syncExistingPremiums();
            }

            function syncExistingPremiums() {
                $('.image-input-row').each(function () {
                    var toggle = $(this).find('.image-premium-toggle');
                    var hiddenVal = $(this).find('.existing-premium-val');
                    if (hiddenVal.length) {
                        hiddenVal.val(toggle.is(':checked') ? 1 : 0);
                    }
                });
            }

            $('#background_category_id').on('change', syncCategoryPremium);
            syncCategoryPremium();

            // Keep hidden value in sync when toggle changes
            $(document).on('change', '.image-premium-toggle', function () {
                var row = $(this).closest('.image-input-row');
                var hiddenVal = row.find('.existing-premium-val');
                if (hiddenVal.length) {
                    hiddenVal.val($(this).is(':checked') ? 1 : 0);
                }
            });

            // Sortable
            var el = document.getElementById('sortable-images');
            new Sortable(el, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
            });

            var uploadZone = document.getElementById('image-upload-zone');
            var multiInput = document.getElementById('multi-input');

            function updateZoneState() {
                if ($('#sortable-images .image-input-row').length > 0) {
                    $(uploadZone).addClass('has-images');
                } else {
                    $(uploadZone).removeClass('has-images');
                }
            }

            function processFiles(files) {
                if (!files || files.length === 0) return;
                var rejected = 0;
                Array.from(files).forEach(function (file) {
                    if (!file) return;
                    var isWebp = (file.type === 'image/webp') || /\.webp$/i.test(file.name || '');
                    if (isWebp) {
                        addImageCard(file);
                    } else {
                        rejected++;
                    }
                });
                if (rejected > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Only .webp images are allowed',
                        text: rejected + ' file(s) were skipped because they are not .webp.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            }

            uploadZone.addEventListener('click', function (e) {
                if ($(e.target).closest('.image-input-row').length) return;
                multiInput.click();
            });

            multiInput.addEventListener('change', function () {
                processFiles(this.files);
                this.value = '';
            });

            ['dragenter', 'dragover'].forEach(function (ev) {
                uploadZone.addEventListener(ev, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    uploadZone.classList.add('zone-active');
                });
            });
            uploadZone.addEventListener('dragleave', function (e) {
                e.preventDefault();
                e.stopPropagation();
                if (e.relatedTarget && uploadZone.contains(e.relatedTarget)) return;
                uploadZone.classList.remove('zone-active');
            });
            uploadZone.addEventListener('drop', function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploadZone.classList.remove('zone-active');
                var files = (e.dataTransfer && e.dataTransfer.files) || [];
                processFiles(files);
            });

            updateZoneState();

            var newImageCounter = 0;

            function addImageCard(file) {
                var reader = new FileReader();
                var currentIndex = newImageCounter++;
                reader.onload = function (e) {
                    var isChecked = categoryIsPremium ? 'checked' : '';
                    var isDisabled = categoryIsPremium ? 'disabled' : '';

                    var html = `<div class="col-6 col-sm-4 col-md-2 mb-3 image-input-row">
                        <div class="card border p-1 h-100 shadow-sm position-relative">
                            <input type="hidden" name="item_type[]" value="new">

                            <span class="drag-handle position-absolute" title="Drag to reorder"
                                style="top: 4px; left: 4px; width: 22px; height: 22px; background: rgba(0,0,0,0.55); color:#fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: grab; z-index: 10;">
                                <i class="mdi mdi-drag" style="font-size: 14px; margin: 0;"></i>
                            </span>

                            <div class="d-flex justify-content-center align-items-center" style="height: 100px; background: #f8f9fa; border-radius: 4px; overflow: hidden;">
                                <img src="${e.target.result}" alt="Image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>

                            <div class="text-center mt-1 mb-1" style="font-size: 11px;">
                                <div class="form-check form-check-flat form-check-primary d-inline-block" style="margin: 0; padding-left: 0;">
                                    <label class="form-check-label" style="font-size: 11px;">
                                        <input type="checkbox" class="form-check-input image-premium-toggle new-premium-toggle"
                                            name="new_image_premiums[${currentIndex}]" value="1" ${isChecked} ${isDisabled}>
                                        Pro
                                        <i class="input-helper"></i>
                                    </label>
                                </div>
                            </div>

                            <button type="button" class="btn btn-danger btn-sm p-0 rounded-circle remove-btn position-absolute"
                                style="top: 5px; right: 5px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; z-index: 10;">
                                <i class="mdi mdi-close" style="font-size: 14px; margin: 0;"></i>
                            </button>

                            <input type="file" name="images[]" class="d-none hidden-file-input">
                        </div>
                    </div>`;

                    var newRow = $(html);
                    var fileInput = newRow.find('.hidden-file-input')[0];
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    fileInput.files = dataTransfer.files;

                    newRow.css({
                        'opacity': 0,
                        'transform': 'translateY(-10px)',
                        'transition': 'all 0.35s ease'
                    });
                    $('#sortable-images').prepend(newRow);
                    setTimeout(function () {
                        newRow.css({ 'opacity': 1, 'transform': 'translateY(0)' });
                    }, 20);
                    updateZoneState();
                }
                reader.readAsDataURL(file);
            }

            $(document).on('click', '.remove-btn', function () {
                var row = $(this).closest('.image-input-row');
                row.css({
                    'transition': 'all 0.3s ease',
                    'opacity': 0,
                    'transform': 'scale(0.85)'
                });
                setTimeout(function () {
                    row.remove();
                    updateZoneState();
                }, 300);
            });

            $('#delete-all-btn').click(function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will remove all existing images. Click Update to save changes.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, clear all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#sortable-images input[value="existing"]').closest('.image-input-row').remove();
                        Swal.fire('Cleared!', 'Click Update to save.', 'success');
                    }
                });
            });
        });
    </script>
@endsection
