@extends('layouts.admin')

@push('plugin-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
@endpush

@push('plugin-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
@endpush

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-plus-circle-outline text-primary" style="font-size: 2rem; margin-right: 10px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">{{ isset($frame) ? 'Edit Frame' : 'Add New Frame' }}</h3>
                                <small class="text-muted">{{ isset($frame) ? 'Update existing frame' : 'Create a new Frame' }}</small>
                            </div>
                        </div>
                        <a href="{{ session('frame_list_url', route('frames.index')) }}" class="btn btn-light text-primary" style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Frames
                        </a>
                    </div>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form class="forms-sample"
                        action="{{ isset($frame) ? route('frames.update', $frame->id) : route('frames.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($frame))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="frame_category_id">Category</label>
                            <select class="form-control" id="frame_category_id" name="frame_category_id" required style="
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
                                    <option value="{{ $category->id }}" {{ (old('frame_category_id') == $category->id || (isset($frame) && $frame->frame_category_id == $category->id)) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('frame_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Combined Sortable Image List -->
                        <div class="form-group mt-4">
                            <label>Frame Images (Drag to Reorder) <small class="text-warning">Only .webp images are allowed</small></label>
                            <div id="sortable-images">
                                @if(isset($frame) && !empty($frame->images))
                                    @foreach($frame->images as $index => $img)
                                        @if(is_string($img))
                                            <div class="image-input-row mb-3 border p-2 rounded" style="background: #f9f9f9;">
                                                <input type="hidden" name="item_type[]" value="existing">
                                                <input type="hidden" name="existing_images[]" value="{{ $img }}">
                                                <input type="hidden" name="existing_thumbnails[]" value="{{ $frame->frame_thumbnail[$index] ?? '' }}">
                                                <input type="hidden" name="indices[]" value="{{ $index }}">
                                                
                                                <div class="row align-items-center">
                                                    <div class="col-md-1 text-center">
                                                        <i class="mdi mdi-drag-vertical drag-handle" style="font-size: 24px; color: #ccc; cursor: grab;" title="Drag to reorder"></i>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Frame</label>
                                                        <input type="file" name="images[{{ $index }}]" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="60"
                                                            data-default-file="{{ asset('upload/frame/' . $frame->category->name . '/frame/' . $img) }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Thumbnail</label>
                                                        <input type="file" name="frame_thumbnail[{{ $index }}]" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="60"
                                                            data-default-file="{{ (isset($frame->frame_thumbnail[$index]) && !empty($frame->frame_thumbnail[$index])) ? asset('upload/frame/' . $frame->category->name . '/frame_thumbnail_image/' . $frame->frame_thumbnail[$index]) : asset('upload/frame/' . $frame->category->name . '/frame/' . $img) }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>Count</label>
                                                        <input type="number" name="counts[]" class="form-control form-control-sm"
                                                            value="{{ $frame->image_input_counts[$index] ?? 1 }}" required>
                                                    </div>
                                                    <div class="col-md-2 d-flex align-items-center justify-content-center" style="margin-top: 25px;">
                                                        <div class="form-check form-check-flat form-check-primary">
                                                            <label class="form-check-label">
                                                                <input type="checkbox" class="form-check-input type-toggle" 
                                                                    {{ ($frame->image_types[$index] ?? 'free') == 'pro' ? 'checked' : '' }}>
                                                                Pro
                                                                <i class="input-helper"></i>
                                                            </label>
                                                            <input type="hidden" name="types[]" value="{{ $frame->image_types[$index] ?? 'free' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 d-flex align-items-center justify-content-center" style="margin-top: 10px;">
                                                        <button type="button" class="btn btn-danger btn-icon-text remove-btn shadow-sm"
                                                            style="border-radius: 50%; width: 34px; height: 34px; padding: 0; display: flex; align-items: center; justify-content: center; background-color: #ff6258; border-color: #ff6258;">
                                                            <i class="mdi mdi-close" style="font-size: 18px; margin: 0; line-height: 1;"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                                
                                {{-- Initial Empty State for Create Mode --}}
                                @if(!isset($frame))
                                    <div class="image-input-row mb-3 border p-2 rounded" style="background: #f9f9f9;">
                                        <input type="hidden" name="item_type[]" value="new">
                                        <input type="hidden" name="indices[]" value="0">
                                        <div class="row align-items-center">
                                            <div class="col-md-1 text-center">
                                                 <i class="mdi mdi-drag-vertical drag-handle" style="font-size: 24px; color: #ccc; cursor: grab;" title="Drag to reorder"></i>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Image</label>
                                                <input type="file" name="images[0]" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="100" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Thumbnail</label>
                                                <input type="file" name="frame_thumbnail[0]" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="100">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Count</label>
                                                <input type="number" name="counts[]" class="form-control" value="1" min="1" required>
                                            </div>
                                            <div class="col-md-2 d-flex align-items-center justify-content-center" style="margin-top: 25px;">
                                                <div class="form-check form-check-flat form-check-primary">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input type-toggle" checked>
                                                        Pro
                                                        <i class="input-helper"></i>
                                                    </label>
                                                    <input type="hidden" name="types[]" value="pro">
                                                </div>
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center justify-content-center" style="margin-top: 10px;">
                                                <button type="button" class="btn btn-danger btn-sm rounded-circle remove-btn shadow-sm"
                                                     style="border-radius: 50%; width: 34px; height: 34px; padding: 0; display: flex; align-items: center; justify-content: center; background-color: #ff6258; border-color: #ff6258; display:none;">
                                                    <i class="mdi mdi-close" style="font-size: 18px; margin: 0; line-height: 1;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary btn-sm mt-2" id="add-image"
                                style="background: #fff; color: #b66dff; border-color: #b66dff;">+ Add Image</button>
                            @error('images')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit"
                                class="btn btn-gradient-primary me-2">{{ isset($frame) ? 'Update' : 'Submit' }}</button>
                            <a href="{{ session('frame_list_url', route('frames.index')) }}" class="btn btn-light">Cancel</a>
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
            $('.dropify').dropify({
                imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp']
            });

            var el = document.getElementById('sortable-images');
            var sortable = new Sortable(el, {
                animation: 150,
                handle: '.drag-handle',
                ghostClass: 'sortable-ghost',
                onEnd: function (evt) {
                }
            });

            $(document).on('change', '.type-toggle', function() {
                var hiddenInput = $(this).closest('.form-check').find('input[type="hidden"]');
                if ($(this).is(':checked')) {
                    hiddenInput.val('pro');
                } else {
                    hiddenInput.val('free');
                }
            });

            var imageInputIndex = {{ isset($frame) ? count($frame->images) : 1 }};
            // Ensure index is high enough to not collide if we start from 0
            imageInputIndex = Math.max(imageInputIndex, 1000); 

            $('#add-image').click(function () {
                imageInputIndex++;
                var uniqueId = 'dropify-new-' + imageInputIndex;
                var uniqueThumbId = 'dropify-thumb-' + imageInputIndex;
                
                var html = `<div class="image-input-row mb-3 border p-2 rounded" style="background: #f9f9f9;">
                                <input type="hidden" name="item_type[]" value="new">
                                <input type="hidden" name="indices[]" value="${imageInputIndex}">
                                <div class="row align-items-center">
                                    <div class="col-md-1 text-center">
                                         <i class="mdi mdi-drag-vertical drag-handle" style="font-size: 24px; color: #ccc; cursor: grab;" title="Drag to reorder"></i>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Image</label>
                                        <input type="file" name="images[${imageInputIndex}]" id="${uniqueId}" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="100" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label>Thumbnail</label>
                                        <input type="file" name="frame_thumbnail[${imageInputIndex}]" id="${uniqueThumbId}" class="dropify" accept=".webp, image/webp" data-allowed-file-extensions="webp" data-height="100">
                                    </div>
                                    <div class="col-md-2">
                                        <label>Count</label>
                                        <input type="number" name="counts[]" class="form-control" value="1" min="1" required>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center justify-content-center" style="margin-top: 25px;">
                                        <div class="form-check form-check-flat form-check-primary">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input type-toggle" checked>
                                                Pro
                                                <i class="input-helper"></i>
                                            </label>
                                            <input type="hidden" name="types[]" value="pro">
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center justify-content-center" style="margin-top: 10px;">
                                        <button type="button" class="btn btn-danger btn-icon-text remove-btn shadow-sm" style="border-radius: 50%; width: 34px; height: 34px; padding: 0; display: flex; align-items: center; justify-content: center; background-color: #ff6258; border-color: #ff6258;">
                                            <i class="mdi mdi-close" style="font-size: 18px; margin: 0; line-height: 1;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                var newRow = $(html);
                newRow.css({
                    'opacity': 0,
                    'transform': 'translateY(-15px)',
                    'transition': 'all 0.4s ease',
                    'background': '#fff3cd'
                });
                $('#sortable-images').prepend(newRow);

                // Scroll the new row into view so user can clearly see it
                $('html, body').animate({
                    scrollTop: newRow.offset().top - 120
                }, 300);

                // Fade-in / slide-down animation
                setTimeout(function(){
                    newRow.css({
                        'opacity': 1,
                        'transform': 'translateY(0)'
                    });
                }, 20);

                // Reset highlight background after a short pause
                setTimeout(function(){
                    newRow.css('background', '#f9f9f9');
                }, 900);

                setTimeout(function(){
                    $('#' + uniqueId).dropify({
                        imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp']
                    });
                    $('#' + uniqueThumbId).dropify({
                        imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp']
                    });
                }, 100);
            });
            $(document).on('click', '.remove-btn', function () {
                var row = $(this).closest('.image-input-row');
                row.css({
                    'transition': 'all 0.35s ease',
                    'opacity': 0,
                    'transform': 'translateX(20px)'
                });
                setTimeout(function(){
                    row.slideUp(250, function(){
                        row.remove();
                    });
                }, 300);
            });
        });
    </script>
@endsection