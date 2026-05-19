@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-pencil-circle-outline text-primary"
                                style="font-size: 2rem; margin-right: 10px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Edit Filter</h3>
                                <small class="text-muted">Update existing filter</small>
                            </div>
                        </div>
                        <a href="{{ session('filter_list_url', route('filters.index')) }}" class="btn btn-light text-primary"
                            style="background-color: #e9ecef;">
                            <i class="mdi mdi-arrow-left mr-1"></i> Back to Filters
                        </a>
                    </div>
                    <form class="forms-sample" action="{{ route('filters.update', $filter->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="filter_category_id">Category</label>
                            <select class="form-control" id="filter_category_id" name="filter_category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('filter_category_id', $filter->filter_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('filter_category_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="name">Filter Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Filter Name"
                                value="{{ old('name', $filter->name) }}" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Type</label>
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="hidden" name="type" value="free">
                                    <input type="checkbox" class="form-check-input" name="type" value="pro" {{ (old('type', $filter->type ?? 'pro') == 'pro') ? 'checked' : '' }}>
                                    Premium (Pro)
                                    <i class="input-helper"></i>
                                </label>
                            </div>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="saturation">Saturation</label>
                                    <input type="number" step="0.1" class="form-control" id="saturation" name="saturation"
                                        value="{{ old('saturation', $filter->saturation) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brightness">Brightness</label>
                                    <input type="number" step="0.1" class="form-control" id="brightness" name="brightness"
                                        value="{{ old('brightness', $filter->brightness) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contrast">Contrast</label>
                                    <input type="number" step="0.1" class="form-control" id="contrast" name="contrast"
                                        value="{{ old('contrast', $filter->contrast) }}" required>
                                </div>
                            </div>
                            <!-- Separator or just continue -->
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="red">Red</label>
                                    <input type="number" step="0.1" class="form-control" id="red" name="red"
                                        value="{{ old('red', $filter->red) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="green">Green</label>
                                    <input type="number" step="0.1" class="form-control" id="green" name="green"
                                        value="{{ old('green', $filter->green) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="blue">Blue</label>
                                    <input type="number" step="0.1" class="form-control" id="blue" name="blue"
                                        value="{{ old('blue', $filter->blue) }}" required>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-gradient-primary me-2">Update</button>
                        <a href="{{ session('filter_list_url', route('filters.index')) }}" class="btn btn-light">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection