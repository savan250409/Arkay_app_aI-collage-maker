@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-camera-iris text-primary"
                                style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Photoshoot Category Management</h3>
                                <small class="text-muted">Manage photoshoot categories</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $categories->total() }} Categories
                            </div>
                            <button type="button" id="open-index-modal" class="btn btn-outline-primary btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-sort btn-icon-prepend"></i> Index
                            </button>
                            <a href="{{ route('photoshoot-categories.create') }}" class="btn btn-primary btn-sm btn-icon-text"
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
                        <div class="dataTables_length">
                            <label class="d-flex align-items-center mb-0">
                                <span style="font-weight: normal;">Show</span>
                                <select id="per_page" class="form-control form-control-sm mx-2"
                                    style="width:65px;height:32px;padding-right:22px;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 6px center;background-size:12px;appearance:none;-webkit-appearance:none;">
                                    <option value="10" {{ (isset($perPage) ? $perPage : request('per_page', 10)) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ (isset($perPage) ? $perPage : request('per_page')) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ (isset($perPage) ? $perPage : request('per_page')) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ (isset($perPage) ? $perPage : request('per_page')) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span style="font-weight: normal;">entries </span>
                            </label>
                        </div>
                        <div class="dataTables_filter">
                            <label class="d-flex align-items-center mb-0" style="gap: 0.8rem;">
                                <span style="font-weight: normal; ">Search:</span>
                                <input type="search" id="search-input" class="form-control form-control-sm ml-2"
                                    placeholder="Search categories..."
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
                                                <img src="{{ asset('upload/photoshoot/' . $category->name . '/category image/' . $category->image) }}"
                                                    alt="image" style="width: 50px; height: 50px; border-radius: 0;">
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
                                                <a href="{{ route('photoshoot-categories.edit', $category->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $category->id }}"
                                                    data-url="{{ route('photoshoot-categories.destroy', $category->id) }}">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-3 align-items-center">
                            <div class="col-sm-6">
                                <div class="dataTables_info" role="status" aria-live="polite">
                                    Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of
                                    {{ $categories->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $categories->appends(request()->query())->links('pagination::bootstrap-4') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @isset($allCategories)
        {{-- Reorder popup: drag categories; top = shown first (admin list + API). --}}
        <div class="modal fade" id="index-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title mb-0" style="font-weight:bold;">
                            <i class="mdi mdi-sort-variant mr-1 text-primary"></i> Category Index Order
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3" style="font-size:13px;">
                            Drag to reorder. <b>Top = shown first</b> in the app. New categories appear on top automatically.
                        </p>
                        <ul id="index-sortable" class="list-unstyled mb-0">
                            @foreach($allCategories as $cat)
                                <li class="index-item d-flex align-items-center p-2 mb-2" data-id="{{ $cat->id }}">
                                    <i class="mdi mdi-drag-vertical" style="font-size:22px; color:#b66dff;"></i>
                                    <span class="index-id mx-2">#{{ $cat->id }}</span>
                                    <img src="{{ asset('upload/photoshoot/' . $cat->name . '/category image/' . $cat->image) }}"
                                        alt="" style="width:36px; height:36px; object-fit:cover; border-radius:6px; margin-right:10px;">
                                    <span style="font-weight:600;">{{ $cat->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                        <button type="button" id="save-index-order" class="btn btn-primary">Save Order</button>
                    </div>
                </div>
            </div>
        </div>
    @endisset
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <style>
        #index-sortable .index-item { border: 1px solid #eee; border-radius: 8px; background: #fff; cursor: grab; }
        #index-sortable .index-item:active { cursor: grabbing; }
        #index-sortable .index-ghost { opacity: .45; background: #ede0ff; }
        #index-sortable .index-id { display: inline-block; min-width: 40px; text-align: center; padding: 3px 8px;
            border: 1px solid #e6d9fb; border-radius: 12px; background: #f4eefc; color: #7a4fd0 !important;
            font-size: 12px; font-weight: 700; line-height: 1.4; }
    </style>
    <script>
        $(document).ready(function () {
            // ---- Category reorder popup ----
            $('#open-index-modal').on('click', function () {
                $('#index-modal').modal('show');
            });

            // Close (× / Cancel) bound explicitly, so it works regardless of the
            // template's Bootstrap data-dismiss wiring. Backdrop click also closes.
            $('#index-modal').on('click', '.close, [data-dismiss="modal"]', function () {
                $('#index-modal').modal('hide');
            });
            $('#index-modal').on('click', function (e) {
                if (e.target === this) { $('#index-modal').modal('hide'); }
            });

            var indexEl = document.getElementById('index-sortable');
            if (window.Sortable && indexEl) {
                Sortable.create(indexEl, { animation: 150, ghostClass: 'index-ghost' });
            }

            $('#save-index-order').on('click', function () {
                var order = $('#index-sortable .index-item').map(function () {
                    return $(this).data('id');
                }).get();
                if (!order.length) { return; }

                var $btn = $(this).prop('disabled', true);
                $.ajax({
                    url: "{{ route('photoshoot-categories.update-order') }}",
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', order: order },
                    success: function (res) {
                        if (res && res.success) {
                            Swal.fire({ icon: 'success', title: 'Order saved', timer: 1500, showConfirmButton: false })
                                .then(function () { location.reload(); });
                        } else {
                            $btn.prop('disabled', false);
                        }
                    },
                    error: function () {
                        $btn.prop('disabled', false);
                        Swal.fire({ icon: 'error', title: 'Could not save order', text: 'Please try again.' });
                    }
                });
            });

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
                            text: "This will permanently delete ALL photoshoots and files associated with this category. Are you really sure?",
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
                                    data: { _token: '{{ csrf_token() }}' },
                                    success: function (response) {
                                        if (response.success) {
                                            Swal.fire('Deleted!', 'Category and all associated photoshoots have been deleted.', 'success');
                                            row.remove();
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('change', '.active-status-toggle', function () {
                var id = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;

                $.ajax({
                    url: "{{ route('photoshoot-categories.update-active-status') }}",
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', id: id, status: status },
                    success: function (response) {
                        if (response.success) {
                            const Toast = Swal.mixin({
                                toast: true, position: 'top-end', showConfirmButton: false,
                                timer: 3000, timerProgressBar: true
                            });
                            Toast.fire({ icon: 'success', title: 'Active status updated successfully' });
                        }
                    }
                });
            });

            function fetch_data(page) {
                var search = $('#search-input').val();
                var per_page = $('#per_page').val();

                $.ajax({
                    url: "{{ route('photoshoot-categories.index') }}",
                    data: { page: page, search: search, per_page: per_page },
                    success: function (data) {
                        $('#table-data').html($(data).find('#table-data').html());
                    }
                });
            }

            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                var page = new URL($(this).attr('href')).searchParams.get("page");
                fetch_data(page);
            });

            $(document).on('keyup', '#search-input', function () { fetch_data(1); });
            $(document).on('change', '#per_page', function () { fetch_data(1); });
        });
    </script>
@endsection
