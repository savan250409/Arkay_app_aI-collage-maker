@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-sm-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-format-font text-primary" style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Font Management</h3>
                                <small class="text-muted">Manage fonts</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $fonts->total() }} Fonts
                            </div>
                            <a href="{{ route('fonts.create') }}" class="btn btn-primary btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-plus btn-icon-prepend"></i> Add Font
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
                                <span style="font-weight: normal; ">Search:</span>
                                <input type="search" id="search-input" class="form-control form-control-sm ml-2"
                                    placeholder="Search fonts..." aria-controls="order-listing"
                                    value="{{ request('search') }}" style="width: 200px;">
                            </label>
                        </div>
                    </div>

                    <div id="fonts-table">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Font Name</th>
                                        <th>Font File</th>
                                        <th>Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($fonts as $font)
                                        <tr>
                                            <td>{{ $font->id }}</td>
                                            <td>{{ $font->name }}</td>
                                            <td>{{ $font->file }}</td>
                                            <td>
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input change-type"
                                                        id="customSwitch{{ $font->id }}" data-id="{{ $font->id }}"
                                                        {{ $font->type == 'pro' ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="customSwitch{{ $font->id }}">
                                                        {{ $font->type == 'pro' ? 'Pro' : 'Free' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('fonts.edit', $font->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm delete-btn"
                                                    data-id="{{ $font->id }}">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">No fonts found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3 align-items-center">
                            <div class="col-sm-6">
                                <div class="dataTables_info" id="order-listing_info" role="status" aria-live="polite">
                                    Showing {{ $fonts->firstItem() ?? 0 }} to {{ $fonts->lastItem() ?? 0 }} of
                                    {{ $fonts->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="order-listing_paginate">
                                    {!! $fonts->appends(request()->query())->links('pagination::bootstrap-4') !!}
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
                // State management using sessionStorage
                var STATE_KEY = 'font_module_state';

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

                // Fetch data immediately if state is non-default to restore user's previous view
                if(initialState.page > 1 || initialState.search !== '' || parseInt(initialState.per_page) !== 10) {
                    fetchFonts(initialState.search, initialState.per_page, initialState.page, false);
                }

                // Search Event
                $('#search-input').on('keyup', function () {
                    var search = $(this).val();
                    var perPage = $('#per_page').val();
                    fetchFonts(search, perPage, 1);
                });

                // Per Page Event
                $('#per_page').on('change', function () {
                    var search = $('#search-input').val();
                    var perPage = $(this).val();
                    fetchFonts(search, perPage, 1);
                });

                // Pagination Click Event
                $(document).on('click', '.pagination a', function(event) {
                    event.preventDefault();
                    var url = new URL($(this).attr('href'), window.location.origin);
                    var page = url.searchParams.get('page') || 1;
                    var search = $('#search-input').val();
                    var perPage = $('#per_page').val();
                    fetchFonts(search, perPage, page);
                });

                // Toggle Change Type
                $(document).on('change', '.change-type', function() {
                    var type = $(this).prop('checked') ? 'pro' : 'free';
                    var id = $(this).data('id');
                    var label = $(this).siblings('label');

                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "{{ route('fonts.change-type') }}",
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

                function fetchFonts(search = '', perPage = 10, page = 1, save = true) {
                    if (save) {
                        saveState(search, perPage, page);
                    }
                    
                    var queryData = { search: search, per_page: perPage, page: page };
                    
                    $.ajax({
                        url: '{{ route('fonts.index') }}',
                        type: 'GET',
                        data: queryData,
                        success: function (data) {
                            $('#fonts-table').html($(data).find('#fonts-table').html());
                        }
                    });
                }

                $(document).on('click', '.delete-btn', function () {
                    var fontId = $(this).data('id');
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
                                text: "This will permanently delete the font file and its folder. Are you really sure?",
                                icon: 'error',
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, delete everything!'
                            }).then((secondResult) => {
                                if (secondResult.isConfirmed) {
                                    $.ajax({
                                        url: '/fonts/' + fontId,
                                        type: 'DELETE',
                                        data: {
                                            _token: '{{ csrf_token() }}'
                                        },
                                        success: function (response) {
                                            if (response.success) {
                                                Swal.fire(
                                                    'Deleted!',
                                                    'Font and its files have been deleted.',
                                                    'success'
                                                );
                                                var state = loadState();
                                                fetchFonts(state.search, state.per_page, state.page, false);
                                            }
                                        }
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endsection