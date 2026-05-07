@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-filter text-primary" style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Filter Category Management</h3>
                                <small class="text-muted">Manage filter categories</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $categories->total() }} Categories
                            </div>
                            <a href="{{ route('filter-categories.create') }}" class="btn btn-primary btn-sm btn-icon-text"
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
                                    class="form-control form-control-sm mx-2" style="width:65px;">
                                    <option>10</option>
                                    <option>25</option>
                                    <option>50</option>
                                    <option>100</option>
                                </select>
                                <span style="font-weight: normal;">entries </span>
                            </label>
                        </div>
                        <div id="order-listing_filter" class="dataTables_filter">
                            <label class="d-flex align-items-center mb-0" style="gap: 0.8rem;">
                                <span style="font-weight: normal;">Search:</span>
                                <input type="search" id="search-input" class="form-control form-control-sm ml-2"
                                    placeholder="Search categories..." aria-controls="order-listing"
                                    value="{{ request('search') }}" style="width: 200px;">
                            </label>
                        </div>
                    </div>

                    <div id="table-data">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
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
                                                <img src="{{ asset('upload/filter_category/' . $category->name . '/category-thumbnail-image/' . $category->image) }}"
                                                    alt="image" class="img-thumbnail" style="width: 50px; height: 50px;">
                                            </td>
                                            <td>
                                                <div class="form-check form-check-flat form-check-primary" style="margin: 0;">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input status-toggle" 
                                                            data-id="{{ $category->id }}" {{ $category->is_active ? 'checked' : '' }}>
                                                        Active
                                                        <i class="input-helper"></i>
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('filter-categories.edit', $category->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>

                                                <button class="btn btn-danger btn-sm delete-btn" data-url="{{ route('filter-categories.destroy', $category->id) }}">Delete</button>
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

            $(document).on('change', '.status-toggle', function () {
                var id = $(this).data('id');
                var status = $(this).is(':checked') ? 1 : 0;
                
                $.ajax({
                    url: "{{ route('filter-categories.update-status') }}",
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
        });
    </script>

    <script>
            // State management using sessionStorage
            var STATE_KEY = 'filter_category_module_state';

            function saveState(search, perPage, page) {
                sessionStorage.setItem(STATE_KEY, JSON.stringify({
                    search: search || '',
                    per_page: perPage || 10,
                    page: page || 1
                }));
            }

            function loadState() {
                var state = sessionStorage.getItem(STATE_KEY);
                if (state) {
                    try { return JSON.parse(state); } catch(e) {}
                }
                return { search: '', per_page: 10, page: 1 };
            }

            var initialState = loadState();

            // Initialize form values
            $('#search-input').val(initialState.search);
            $('#per_page').val(initialState.per_page);

            function fetch_data(page = 1, search = '', per_page = 10, save = true) {
                if (save) {
                    saveState(search, per_page, page);
                }

                $.ajax({
                    url: "{{ route('filter-categories.index') }}",
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

            // Fetch data immediately if state is non-default to restore user's previous view
            if(initialState.page > 1 || initialState.search !== '' || parseInt(initialState.per_page) !== 10) {
                fetch_data(initialState.page, initialState.search, initialState.per_page, false);
            }

            $(document).ready(function () {
                $(document).on('click', '.pagination a', function (event) {
                    event.preventDefault();
                    var url = new URL($(this).attr('href'), window.location.origin);
                    var page = url.searchParams.get("page") || 1;
                    var search = $('#search-input').val();
                    var per_page = $('#per_page').val();
                    fetch_data(page, search, per_page);
                });

                $('#search-input').on('keyup', function () {
                    var search = $(this).val();
                    var per_page = $('#per_page').val();
                    fetch_data(1, search, per_page);
                });

                $('#per_page').on('change', function () {
                    var search = $('#search-input').val();
                    var per_page = $(this).val();
                    fetch_data(1, search, per_page);
                });

                $(document).on('click', '.delete-btn', function (e) {
                    e.preventDefault();
                    var url = $(this).data('url');
                    var form = $(this).closest('form');

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
                                            'Filter Category has been deleted.',
                                            'success'
                                        );
                                        var state = loadState();
                                        fetch_data(state.page, state.search, state.per_page, false);
                                    } else {
                                        // Fallback if backend does a regular redirect instead of JSON
                                        window.location.reload();
                                    }
                                }
                            });
                        }
                    });
                });
            });
    </script>
@endsection