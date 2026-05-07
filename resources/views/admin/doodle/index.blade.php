@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-sm-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-creation text-primary" style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Doodle Management</h3>
                                <small class="text-muted">Manage doodles</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $doodles->total() }} Doodles
                            </div>
                            <button type="button" class="btn btn-info btn-sm btn-icon-text" data-bs-toggle="modal" data-bs-target="#indexModal" style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-sort btn-icon-prepend"></i> Index
                            </button>
                            <a href="{{ route('doodles.create') }}" class="btn btn-primary btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-plus btn-icon-prepend"></i> Add Doodle
                            </a>
                        </div>
                    </div>

                    <!-- Index Modal -->
                    <div class="modal fade" id="indexModal" tabindex="-1" role="dialog" aria-labelledby="indexModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="indexModalLabel">Category Indexing</h5>
                                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-info">
                                        <i class="mdi mdi-information-outline"></i> Drag and drop categories to reorder them. The new order will be saved automatically.
                                    </div>
                                    <ul id="sortable-doodles" class="list-group">
                                        {{-- We will load all doodles via AJAX or pass them if not too many. For Better UX with pagination, AJAX is better but for this task, I'll pass all doodles if possible or just use current page's doodles + logic to fetch all.
                                             Wait, standard implementation usually loads ALL items for sorting.
                                             Let's fetch all doodles for the modal. --}}
                                        @php
                                            $allDoodles = \App\Models\Doodle::orderBy('row_order', 'ASC')->orderBy('id', 'ASC')->get();
                                        @endphp
                                        @foreach($allDoodles as $doodle)
                                            <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $doodle->id }}">
                                                <div class="d-flex align-items-center">
                                                    <i class="mdi mdi-drag-vertical mr-2" style="cursor: move; color: #ccc;"></i>
                                                    @php
                                                        $imageShow = $doodle->image;
                                                        $decode = json_decode($doodle->image, true);
                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decode)) {
                                                            $imageShow = $decode[0] ?? null;
                                                        }
                                                    @endphp
                                                    @if($imageShow)
                                                        <img src="{{ asset('upload/doodle/' . $doodle->name . '/' . $imageShow) }}" alt="img" style="width: 30px; height: 30px; object-fit: contain; margin-right: 10px; border-radius: 4px;">
                                                    @endif
                                                    <span class="font-weight-bold">{{ $loop->iteration }}. {{ $doodle->name }}</span>
                                                </div>
                                                <span class="badge badge-secondary badge-pill">ID: {{ $doodle->id }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
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
                                        placeholder="Search doodles..." aria-controls="order-listing"
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
                                            <th>Doodle Image</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($doodles as $doodle)
                                            <tr>
                                                <td>{{ $doodle->id }}</td>
                                                <td>{{ $doodle->name }}</td>
                                                <td>
                                                    @php
                                                        $imageToShow = $doodle->image;
                                                        // Handle Legacy JSON
                                                        $decoded = json_decode($doodle->image, true);
                                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                                            $imageToShow = $decoded[0] ?? null;
                                                        }
                                                    @endphp

                                                    @if($imageToShow)
                                                        <img src="{{ asset('upload/doodle/' . $doodle->name . '/' . $imageToShow) }}"
                                                            alt="image" class="img-thumbnail"
                                                            style="width: 60px; height: 60px; object-fit: contain; border-radius: 8px;">
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input change-type"
                                                            id="customSwitch{{ $doodle->id }}" data-id="{{ $doodle->id }}"
                                                            {{ $doodle->type == 'pro' ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="customSwitch{{ $doodle->id }}">
                                                            {{ $doodle->type == 'pro' ? 'Pro' : 'Free' }}
                                                        </label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ route('doodles.edit', $doodle->id) }}"
                                                        class="btn btn-warning btn-sm">Edit</a>
                                                    <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $doodle->id }}"
                                                        data-url="{{ route('doodles.destroy', $doodle->id) }}">Delete</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row mt-3 align-items-center">
                                <div class="col-sm-6">
                                    <div class="dataTables_info" id="order-listing_info" role="status" aria-live="polite">
                                        Showing {{ $doodles->firstItem() ?? 0 }} to {{ $doodles->lastItem() ?? 0 }} of
                                        {{ $doodles->total() }} entries
                                    </div>
                                </div>
                                <div class="col-sm-6 d-flex justify-content-end">
                                    <div class="dataTables_paginate paging_simple_numbers" id="order-listing_paginate">
                                        {!! $doodles->appends(request()->query())->links('pagination::bootstrap-4') !!}
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
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('#success-alert').fadeOut('fast');
            }, 5000);

            // Sortable
            $("#sortable-doodles").sortable({
                update: function(event, ui) {
                    var order = [];
                    $('#sortable-doodles li').each(function() {
                        order.push($(this).data('id'));
                    });

                    $.ajax({
                        url: "{{ route('doodles.update-order') }}",
                        method: 'POST',
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                             const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });

                            Toast.fire({
                                icon: 'success',
                                title: 'Order updated successfully'
                            });
                        }
                    });
                }
            });
            $("#sortable-doodles").disableSelection();

            // Toggle Change Type
            $(document).on('change', '.change-type', function() {
                var type = $(this).prop('checked') ? 'pro' : 'free';
                var id = $(this).data('id');
                var label = $(this).siblings('label');

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('doodles.change-type') }}",
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
            var STATE_KEY = 'doodle_module_state';

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
                    url: "{{ route('doodles.index') }}",
                    data: {
                        page: page,
                        search: search,
                        per_page: per_page
                    },
                    success: function (data) {
                        $('#table-data').html($(data).find('#table-data').html());
                        // Re-initialize any plugins if needed after ajax load inside table
                    }
                });
            }

            // Fetch data immediately if state is non-default to restore user's previous view
            if(initialState.page > 1 || initialState.search !== '' || parseInt(initialState.per_page) !== 10) {
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
                var search = $(this).val();
                var per_page = $('#per_page').val();
                fetch_data(1, search, per_page);
            });

            $('#per_page').on('change', function () {
                var search = $('#search-input').val();
                var per_page = $(this).val();
                fetch_data(1, search, per_page);
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
                                        'Doodle and its files have been deleted.',
                                        'success'
                                    );
                                    var state = loadState();
                                    fetch_data(state.page, state.search, state.per_page, false);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection