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
                                    {{ isset($stickerCategory) ? 'Edit Sticker Category' : 'Add New Sticker Category' }}
                                </h3>
                                <small
                                    class="text-muted">{{ isset($stickerCategory) ? 'Update existing category' : 'Create a new Sticker Category' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('sticker-categories.index') }}" class="btn btn-light text-primary"
                            style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Categories
                        </a>
                    </div>
                    <form class="forms-sample"
                        action="{{ isset($stickerCategory) ? route('sticker-categories.update', $stickerCategory->id) : route('sticker-categories.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($stickerCategory))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Category Name"
                                value="{{ old('name', $stickerCategory->name ?? '') }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Thumbnail Image</label>
                            <input type="file" name="image" class="file-upload-default" accept=".webp, image/webp" style="display:none">
                            <div class="input-group col-xs-12">
                                <input type="text" class="form-control file-upload-info" disabled
                                    placeholder="Upload Image">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-gradient-primary"
                                        type="button">Upload</button>
                                </span>
                            </div>
                            @if(isset($stickerCategory) && $stickerCategory->image)
                                <div class="mt-2">
                                    <img src="{{ asset('upload/sticker/' . $stickerCategory->name . '/category-thumbnail-image/' . $stickerCategory->image) }}"
                                        alt="Current Image" style="width: 100px;">
                                </div>
                            @endif
                            @error('image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>



                        <div class="form-group">
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                    <input type="checkbox" name="is_premium" class="form-check-input" value="1" {{ old('is_premium', $stickerCategory->is_premium ?? 1) ? 'checked' : '' }}>
                                    Premium (Pro)
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                            <div class="form-check form-check-flat form-check-primary">
                                <label class="form-check-label">
                                    <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $stickerCategory->is_active ?? 1) ? 'checked' : '' }}>
                                    Active
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn btn-gradient-primary me-2">{{ isset($stickerCategory) ? 'Update' : 'Submit' }}</button>
                        <a href="{{ route('sticker-categories.index') }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.file-upload-browse').on('click', function () {
                var file = $(this).parent().parent().parent().find('.file-upload-default');
                file.trigger('click');
            });
            $('.file-upload-default').on('change', function () {
                $(this).parent().find('.form-control').val($(this).val().replace(/C:\\fakepath\\/i, ''));

                if (this.files && this.files[0]) {
                    var reader = new FileReader();
                    var input = $(this);
                    reader.onload = function (e) {
                        var previewImg = input.closest('.form-group').find('img');
                        if (previewImg.length > 0) {
                            previewImg.attr('src', e.target.result);
                        } else {
                            var imgHtml = '<div class="mt-2"><img src="' + e.target.result + '" style="width: 100px;"></div>';
                            input.closest('.form-group').append(imgHtml);
                        }
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        });
    </script>
@endsection