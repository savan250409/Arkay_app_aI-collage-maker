@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-sm-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-camera-iris text-primary" style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Photoshoot Management</h3>
                                <small class="text-muted">Grouped by category &amp; country</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $groups->total() }} Groups
                            </div>
                            <a href="{{ route('photoshoots.create') }}" class="btn btn-primary btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-plus btn-icon-prepend"></i> Add Photoshoot
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
                        <select id="country-filter" class="form-control form-control-sm" style="height:32px;width:150px;flex-shrink:0;padding-right:22px;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 6px center;background-size:12px;appearance:none;-webkit-appearance:none;">
                            <option value="">All Countries</option>
                            <option value="global" {{ (isset($country) && $country === 'global') ? 'selected' : '' }}>Global</option>
                            @foreach($countries as $code => $label)
                                <option value="{{ $code }}" {{ (isset($country) && $country === $code) ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="search-wrapper position-relative" style="flex:1; min-width:180px; max-width:280px; margin-left:auto;">
                            <i class="mdi mdi-magnify text-muted position-absolute" style="left:10px; top:50%; transform:translateY(-50%); font-size:16px; pointer-events:none; line-height:1;"></i>
                            <input type="search" id="search-input" class="form-control form-control-sm"
                                placeholder="Search category..."
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
                                        <th>Country</th>
                                        <th>Items</th>
                                        <th>Preview</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groups as $group)
                                        @php $items = is_array($group->items) ? $group->items : []; @endphp
                                        <tr>
                                            <td>{{ $group->id }}</td>
                                            <td>{{ $group->category->name ?? 'N/A' }}</td>
                                            <td>
                                                @if($group->country === '')
                                                    <span class="badge badge-secondary">Global</span>
                                                @else
                                                    <span class="badge badge-info">{{ strtoupper($group->country) }}</span>
                                                @endif
                                            </td>
                                            <td><span class="badge badge-primary">{{ count($items) }}</span></td>
                                            <td>
                                                @if(count($items))
                                                    <div class="d-flex align-items-center flex-wrap">
                                                        @foreach(array_slice($items, 0, 3) as $it)
                                                            <div class="mr-2 mb-1">
                                                                <img src="{{ asset('upload/photoshoot/' . ($group->category->name ?? 'default') . '/photoshoots/' . ($it['image'] ?? '')) }}"
                                                                    alt="image" style="width: 44px; height: 44px; object-fit: contain; border:1px solid #eee; border-radius: 6px;">
                                                            </div>
                                                        @endforeach
                                                        @if(count($items) > 3)
                                                            <span class="text-muted" style="font-size:12px;">+{{ count($items) - 3 }} more</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('photoshoots.edit', $group->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $group->id }}"
                                                    data-url="{{ route('photoshoots.destroy', $group->id) }}">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if($groups->isEmpty())
                                        <tr><td colspan="6" class="text-center text-muted py-4">No photoshoots found.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3 align-items-center">
                            <div class="col-sm-6">
                                <div class="dataTables_info" role="status" aria-live="polite">
                                    Showing {{ $groups->firstItem() ?? 0 }} to {{ $groups->lastItem() ?? 0 }} of
                                    {{ $groups->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                                <div class="dataTables_paginate paging_simple_numbers">
                                    {!! $groups->appends(request()->query())->links('pagination::bootstrap-4') !!}
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
            setTimeout(function () { $('#success-alert').fadeOut('fast'); }, 5000);

            $(document).on('click', '.delete-btn', function () {
                var url = $(this).data('url');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This deletes the whole country group and its items!",
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
                            data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', 'Photoshoot group has been deleted.', 'success');
                                    row.remove();
                                }
                            }
                        });
                    }
                });
            });

            var searchTimer;

            function fetch_data(page) {
                $.ajax({
                    url: "{{ route('photoshoots.index') }}",
                    data: {
                        page: page,
                        search: $('#search-input').val(),
                        per_page: $('#per_page').val(),
                        category_id: $('#category-filter').val(),
                        country: $('#country-filter').val()
                    },
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

            $(document).on('keyup', '#search-input', function () {
                var val = $(this).val();
                $('.search-clear').css('display', val ? 'inline-block' : 'none');
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () { fetch_data(1); }, 300);
            });

            $(document).on('click', '.search-clear', function () {
                $('#search-input').val('');
                $(this).css('display', 'none');
                fetch_data(1);
            });

            $(document).on('change', '#per_page', function () { fetch_data(1); });
            $(document).on('change', '#category-filter', function () { fetch_data(1); });
            $(document).on('change', '#country-filter', function () { fetch_data(1); });
        });
    </script>
@endsection
