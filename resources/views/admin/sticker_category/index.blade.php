@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-folder-multiple-image text-primary"
                                style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Sticker Category Management</h3>
                                <small class="text-muted">Manage sticker categories</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <a href="{{ route('sticker-categories.order') }}" class="btn btn-info btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-sort-variant btn-icon-prepend"></i> Reorder Categories
                            </a>
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $categories->total() }} Categories
                            </div>
                            <a href="{{ route('sticker-categories.create') }}" class="btn btn-primary btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-plus btn-icon-prepend"></i> Add Category
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: "{{ session('success') }}",
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            });
                        </script>
                    @endif

                    <!-- Top Controls -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="dataTables_length" id="order-listing_length">
                            <label class="d-flex align-items-center mb-0">
                                <span style="font-weight: normal;">Show</span>
                                <select id="per_page" name="order-listing_length" aria-controls="order-listing"
                                    class="form-control form-control-sm mx-2" style="
                                                            width:65px;
                                                            height:32px;
                                                            line-height:32px;
                                                            padding-right:22px;
                                                            background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');
                                                            background-repeat:no-repeat;
                                                            background-position:right 6px center;
                                                            background-size:12px;
                                                            appearance:none;
                                                            -webkit-appearance:none;
                                                            -moz-appearance:none;
                                                        ">
                                    <option value="10" {{ (isset($perPage) ? $perPage : request('per_page', 10)) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ (isset($perPage) ? $perPage : request('per_page')) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ (isset($perPage) ? $perPage : request('per_page')) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ (isset($perPage) ? $perPage : request('per_page')) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span style="font-weight: normal;">entries </span>
                            </label>
                        </div>
                        <div id="order-listing_filter" class="dataTables_filter">
                            <label class="d-flex align-items-center mb-0" style="gap: 0.8rem;">
                                <span style="font-weight: normal; ">Search:</span>
                                <input type="search" id="search-input" class="form-control form-control-sm ml-2"
                                    placeholder="Search categories..." aria-controls="order-listing"
                                    value="{{ isset($search) ? $search : request('search') }}" style="width: 200px;">
                            </label>
                        </div>
                    </div>

                    <div id="table-data">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Thumbnail</th>
                                        <th>Premium</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $category->id }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <img src="{{ asset('upload/sticker/' . $category->name . '/category-thumbnail-image/' . $category->image) }}"
                                                    alt="image" style="width: 50px; height: 50px; border-radius: 0;">
                                            </td>
                                            <td>
                                                <div class="form-check form-check-flat form-check-primary" style="margin: 0;">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input status-toggle" 
                                                            data-id="{{ $category->id }}" {{ $category->is_premium ? 'checked' : '' }}>
                                                        Pro
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-check-flat form-check-primary" style="margin: 0;">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input active-status-toggle" 
                                                            data-id="{{ $category->id }}" {{ $category->is_active ? 'checked' : '' }}>
                                                        Active
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('sticker-categories.edit', $category->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $category->id }}"
                                                    data-url="{{ route('sticker-categories.destroy', $category->id) }}">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-3 align-items-center">
                            <div class="col-sm-6">
                                <div class="dataTables_info" id="order-listing_info" role="status" aria-live="polite">
                                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
                                    {{ $categories->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="order-listing_paginate">
                                    {!! $categories->appends(request()->query())->links('pagination::bootstrap-4') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('#success-alert').fadeOut('fast');
            }, 5000);

            $(document).on('click', '.delete-btn', function () {
                var url = $(this).data('url');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Final Warning!',
                            text: "This will permanently delete ALL stickers and files associated with this category. Are you really sure?",
                            icon: 'error',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Yes, delete everything!'
                        }).then((secondResult) => {
                            if (secondResult.isConfirmed) {
                                $.ajax({
                                    url: url,
                                    type: 'DELETE',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire(
                                                'Deleted!',
                                                'Category and all associated stickers have been deleted.',
                                                'success'
                                            );
                                            row.remove();
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('change', '.status-toggle', function () {
                var id = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;
                
                $.ajax({
                    url: "{{ route('sticker-categories.update-status') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        status: status
                    },
                    success: function (response) {
                        if (response.success) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            
                            Toast.fire({
                                icon: 'success',
                                title: 'Status updated successfully'
                            });
                        }
                    }
                });
            });

            $(document).on('change', '.active-status-toggle', function () {
                var id = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;
                
                $.ajax({
                    url: "{{ route('sticker-categories.update-active-status') }}",
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: id,
                        status: status
                    },
                    success: function (response) {
                        if (response.success) {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            
                            Toast.fire({
                                icon: 'success',
                                title: 'Active status updated successfully'
                            });
                        }
                    }
                });
            });

            function fetch_data(page) {
                var search = $('#search-input').val();
                var per_page = $('#per_page').val();

                $.ajax({
                    url: "{{ route('sticker-categories.index') }}",
                    data: {
                        page: page,
                        search: search,
                        per_page: per_page
                    },
                    success: function (data) {
                        $('#table-data').html($(data).find('#table-data').html());
                    }
                });
            }

            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                var href = $(this).attr('href');
                var url = new URL(href);
                var page = url.searchParams.get("page");
                fetch_data(page);
            });

            $(document).on('keyup', '#search-input', function () {
                fetch_data(1);
            });

            $(document).on('change', '#per_page', function () {
                fetch_data(1);
            });
        });
    </script>
@endsection