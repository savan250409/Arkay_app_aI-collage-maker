@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-creation text-primary" style="font-size: 2rem; margin-right: 10px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">
                                    {{ isset($doodle) ? 'Edit Doodle' : 'Add New Doodle' }}
                                </h3>
                                <small
                                    class="text-muted">{{ isset($doodle) ? 'Update existing doodle' : 'Create a new Doodle' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('doodles.index') }}" class="btn btn-light text-primary"
                            style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Doodles
                        </a>
                    </div>
                    <form class="forms-sample"
                        action="{{ isset($doodle) ? route('doodles.update', $doodle->id) : route('doodles.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($doodle))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name', $doodle->name ?? '') }}" placeholder="Doodle Name" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Type</label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="hidden" name="type" value="free">
                                    <input type="checkbox" class="form-check-input" name="type" value="pro" {{ (old('type', $doodle->type ?? 'pro') == 'pro') ? 'checked' : '' }}>
                                    Premium (Pro)
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Doodle Type</label>
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="doodle_type" value="image" {{ (old('doodle_type', $doodle->doodle_type ?? 'image') == 'image') ? 'checked' : '' }}>
                                            Image
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="doodle_type" value="line" {{ (old('doodle_type', $doodle->doodle_type ?? '') == 'line') ? 'checked' : '' }}>
                                            Line
                                            <i class="input-helper"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('doodle_type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Doodle Image</label>
                            @if(isset($doodle) && $doodle->image)
                                <div class="mb-2">
                                    @php
                                        $imageToShow = $doodle->image;
                                        if (is_array($doodle->image)) {
                                            $imageToShow = $doodle->image[0] ?? null;
                                        } elseif (is_string($doodle->image)) {
                                            $decoded = json_decode($doodle->image, true);
                                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                $imageToShow = $decoded[0] ?? null;
                                            }
                                        }
                                    @endphp
                                    @if($imageToShow)
                                        <small class="text-muted">Current image: {{ $imageToShow }}</small>
                                        <br>
                                        <img src="{{ asset('upload/doodle/' . $doodle->name . '/' . $imageToShow) }}" width="100">
                                    @endif
                                </div>
                            @endif
                            <input type="file" name="image" class="file-upload-default" style="display:none"
                                accept=".webp, image/webp" {{ !isset($doodle) ? 'required' : '' }}>
                            <div class="input-group col-xs-12">
                                <span class="input-group-append">
                                    <button class="file-upload-browse btn btn-light" type="button"
                                        style="border: 1px solid #ebedf2;">Choose Image</button>
                                </span>
                                <input type="text" class="form-control file-upload-info" disabled
                                    placeholder="No file chosen" style="background: #fff;">
                            </div>
                            @error('image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <button type="submit"
                                class="btn btn-gradient-primary me-2">{{ isset($doodle) ? 'Update' : 'Submit' }}</button>
                            <a href="{{ route('doodles.index') }}" class="btn btn-light">Cancel</a>
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
            $('.file-upload-browse').on('click', function () {
                var file = $(this).closest('.form-group').find('.file-upload-default');
                file.trigger('click');
            });

            $('.file-upload-default').on('change', function () {
                $(this).closest('.form-group').find('.file-upload-info').val($(this).val().replace(/C:\\fakepath\\/i, ''));
            });
        });
    </script>
@endsection