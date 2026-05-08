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
                            <i class="mdi mdi-plus-circle-outline text-primary"
                                style="font-size: 2rem; margin-right: 10px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">
                                    {{ isset($font) ? 'Edit Font' : 'Add New Font' }}
                                </h3>
                                <small
                                    class="text-muted">{{ isset($font) ? 'Update existing font' : 'Create a new font' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('fonts.index') }}" class="btn btn-light text-primary"
                            style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Fonts
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
                        action="{{ isset($font) ? route('fonts.update', array_merge(['font' => $font->id], request()->query())) : route('fonts.store', request()->query()) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($font))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Font Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $font->name ?? '') }}" required placeholder="Enter font name">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Type</label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="hidden" name="type" value="free">
                                    <input type="checkbox" class="form-check-input" name="type" value="pro" {{ (old('type', $font->type ?? 'pro') == 'pro') ? 'checked' : '' }}>
                                    Premium (Pro)
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Font Preview Image</label>
                            @if(isset($font) && $font->font_preview)
                                <div class="mb-2">
                                    <small class="text-muted">Current preview: {{ $font->font_preview }}</small>
                                </div>
                            @endif
                            <input type="file" name="font_preview" class="dropify" accept=".webp, image/webp"
                                data-allowed-file-extensions="webp" data-height="100"
                                data-default-file="{{ isset($font) && $font->font_preview ? asset('upload/font/' . $font->name . '/' . $font->font_preview) : '' }}">
                            @error('font_preview')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Font File (.ttf, .otf)</label>
                            @if(isset($font))
                                <div class="mb-2">
                                    <small class="text-muted">Current file: {{ $font->file }}</small>
                                </div>
                            @endif
                            <input type="file" name="file" class="dropify" accept=".ttf,.otf"
                                data-allowed-file-extensions="ttf otf" data-height="100" {{ !isset($font) ? 'required' : '' }}>
                            @error('file')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit"
                                class="btn btn-gradient-primary me-2">{{ isset($font) ? 'Update' : 'Submit' }}</button>
                            <a href="{{ route('fonts.index') }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Initialize Dropify
            $('.dropify').dropify({
                imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp']
            });

            // Auto-fill Font Name when a file is selected (only if name is currently empty)
            $('input[name="file"]').on('change', function () {
                var file = this.files[0];
                var nameInput = $('#name');
                if (file && nameInput.val() === '') {
                    var fileName = file.name;
                    // Remove extension (.ttf, .otf)
                    var nameWithoutExtension = fileName.substring(0, fileName.lastIndexOf('.')) || fileName;
                    nameInput.val(nameWithoutExtension);
                }
            });
        });
    </script>
@endsection