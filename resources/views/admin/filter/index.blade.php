@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-filter-variant text-primary" style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Filter Management</h3>
                                <small class="text-muted">Manage filters</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $filters->total() }} Filters
                            </div>
                            <a href="{{ route('filters.import') }}" class="btn btn-info btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem; margin-right: 10px;">
                                <i class="mdi mdi-upload btn-icon-prepend"></i> Import CSV
                            </a>
                            <a href="{{ route('filters.create') }}" class="btn btn-primary btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-plus btn-icon-prepend"></i> Add Filter
                            </a>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="success-alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <!-- Top Controls -->
                    <div class="d-flex flex-nowrap align-items-center mb-3" style="gap: 1.25rem;">
                            <label class="d-flex align-items-center mb-0" style="gap: 0.5rem;">
                                <span style="font-weight: normal; white-space: nowrap;">Show</span>
                                <select id="per_page" class="form-control form-control-sm" style="width:65px;height:32px;padding-right:22px;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 6px center;background-size:12px;appearance:none;-webkit-appearance:none;">
                                    <option value="10" {{ (isset($perPage) ? $perPage : request('per_page', 10)) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ (isset($perPage) ? $perPage : request('per_page')) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ (isset($perPage) ? $perPage : request('per_page')) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ (isset($perPage) ? $perPage : request('per_page')) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <span style="font-weight: normal; white-space: nowrap;">entries</span>
                            </label>
                            <select id="category-filter" class="form-control form-control-sm" style="height:32px;width:160px;flex-shrink:0;padding-right:22px;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 6px center;background-size:12px;appearance:none;-webkit-appearance:none;">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($categoryId) && $categoryId == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        <div class="search-wrapper position-relative" style="flex:1; min-width:180px; max-width:280px; margin-left:auto;">
                            <i class="mdi mdi-magnify text-muted position-absolute" style="left:10px; top:50%; transform:translateY(-50%); font-size:16px; pointer-events:none; line-height:1;"></i>
                            <input type="search" id="search-input" class="form-control form-control-sm"
                                placeholder="Search filters..."
                                value="{{ isset($search) ? $search : request('search') }}"
                                style="height:32px; padding-left:32px; padding-right:32px;">
                            <i class="mdi mdi-close text-muted search-clear position-absolute" style="right:10px; top:50%; transform:translateY(-50%); font-size:14px; cursor:pointer; line-height:1; display:{{ (isset($search) && $search) ? 'inline-block' : 'none' }};"></i>
                        </div>
                    </div>

                    <div id="table-data">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category</th>
                                        <th>Filter Name</th>
                                        <th>Values</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($filters as $filter)
                                        <tr>
                                            <td>{{ $filter->id }}</td>
                                            <td>{{ $filter->category->name ?? 'N/A' }}</td>
                                            <td>{{ $filter->name }}</td>
                                            <td>
                                                <small>
                                                    S: {{ $filter->saturation }}, B: {{ $filter->brightness }}, C:
                                                    {{ $filter->contrast }}<br>
                                                    R: {{ $filter->red }}, G: {{ $filter->green }}, B: {{ $filter->blue }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input change-type"
                                                        id="customSwitch{{ $filter->id }}" data-id="{{ $filter->id }}"
                                                        {{ $filter->type == 'pro' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customSwitch{{ $filter->id }}">
                                                        {{ $filter->type == 'pro' ? 'Pro' : 'Free' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('filters.edit', $filter->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>

                                                <button class="btn btn-danger btn-sm delete-btn" data-url="{{ route('filters.destroy', $filter->id) }}">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-3 align-items-center">
                            <div class="col-sm-6">
                                <div class="dataTables_info" id="order-listing_info" role="status" aria-live="polite">
                                    Showing {{ $filters->firstItem() ?? 0 }} to {{ $filters->lastItem() ?? 0 }} of
                                    {{ $filters->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="order-listing_paginate">
                                    {!! $filters->appends(request()->query())->links('pagination::bootstrap-4') !!}
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

            // Toggle Change Type
            $(document).on('change', '.change-type', function() {
                var type = $(this).prop('checked') ? 'pro' : 'free';
                var id = $(this).data('id');
                var label = $(this).siblings('label');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('filters.change-type') }}",
                    data: {
                        'type': type,
                        'id': id,
                        '_token': "{{ csrf_token() }}"
                    },
                    success: function(data) {
                        if(data.success) {
                            label.text(type == 'pro' ? 'Pro' : 'Free');
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Status updated successfully',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    }
                });
            });

            // State management using sessionStorage
            var STATE_KEY = 'filter_module_state';

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

            var searchTimer;

            function fetch_data(page = 1, search = '', per_page = 10, save = true) {
                var category_id = $('#category-filter').val();
                if (save) {
                    saveState(search, per_page, page);
                }

                $.ajax({
                    url: "{{ route('filters.index') }}",
                    data: { page: page, search: search, per_page: per_page, category_id: category_id },
                    success: function (data) {
                        $('#table-data').html($(data).find('#table-data').html());
                    }
                });
            }

            if (initialState.page > 1 || initialState.search !== '' || parseInt(initialState.per_page) !== 10) {
                fetch_data(initialState.page, initialState.search, initialState.per_page, false);
            }

            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                var url = new URL($(this).attr('href'), window.location.origin);
                var page = url.searchParams.get("page") || 1;
                var search = $('#search-input').val();
                var per_page = $('#per_page').val();
                fetch_data(page, search, per_page);
            });

            $('#search-input').on('keyup', function () {
                var val = $(this).val();
                $('.search-clear').css('display', val ? 'inline-block' : 'none');
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    fetch_data(1, $('#search-input').val(), $('#per_page').val());
                }, 300);
            });

            $(document).on('click', '.search-clear', function () {
                $('#search-input').val('');
                $(this).css('display', 'none');
                fetch_data(1, '', $('#per_page').val());
            });

            $('#per_page').on('change', function () {
                var search = $('#search-input').val();
                var per_page = $(this).val();
                fetch_data(1, search, per_page);
            });

            $('#category-filter').on('change', function () {
                var search = $('#search-input').val();
                var per_page = $('#per_page').val();
                fetch_data(1, search, per_page);
            });

            $(document).on('click', '.delete-btn', function (e) {
                e.preventDefault();
                var url = $(this).data('url');

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
                                        'Filter has been deleted.',
                                        'success'
                                    );
                                    var state = loadState();
                                    fetch_data(state.page, state.search, state.per_page, false);
                                } else {
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