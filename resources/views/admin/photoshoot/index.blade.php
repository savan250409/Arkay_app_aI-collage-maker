@extends('layouts.admin')

@section('content')
    @php
        $curPerPage = request('per_page', 10);
        $curCategory = request('category_id', '');
        $curCountry = request('country', '');
        $curSearch = request('search', '');
    @endphp
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

                    <!-- Controls: a plain GET form. Changing any control (or pressing
                         Enter in search) reloads the page with the query string. -->
                    <form method="GET" action="{{ route('photoshoots.index') }}" id="filter-form"
                        class="d-flex flex-wrap align-items-center mb-3" style="gap: 1rem;">
                        <label class="d-flex align-items-center mb-0" style="gap: 0.5rem;">
                            <span style="font-weight: normal; white-space: nowrap;">Show</span>
                            <select name="per_page" class="form-control form-control-sm auto-submit" style="width:70px;height:32px;padding-right:22px;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 6px center;background-size:12px;appearance:none;-webkit-appearance:none;">
                                @foreach([10,25,50,100] as $pp)
                                    <option value="{{ $pp }}" {{ (int) $curPerPage === $pp ? 'selected' : '' }}>{{ $pp }}</option>
                                @endforeach
                            </select>
                            <span style="font-weight: normal; white-space: nowrap;">entries</span>
                        </label>

                        <select name="category_id" class="form-control form-control-sm auto-submit" style="height:32px;width:160px;flex-shrink:0;padding-right:22px;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 6px center;background-size:12px;appearance:none;-webkit-appearance:none;">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (string) $curCategory === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>

                        <select name="country" class="form-control form-control-sm auto-submit" style="height:32px;width:150px;flex-shrink:0;padding-right:22px;background-image:url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 fill=%22gray%22%3E%3Cpath d=%22M2 4l4 4 4-4%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 6px center;background-size:12px;appearance:none;-webkit-appearance:none;">
                            <option value="">All Countries</option>
                            <option value="global" {{ $curCountry === 'global' ? 'selected' : '' }}>Global</option>
                            @foreach($countries as $code => $label)
                                <option value="{{ $code }}" {{ $curCountry === $code ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>

                        <div class="search-wrapper" style="flex:1 1 220px; min-width:180px; max-width:300px; margin-left:auto;">
                            <div class="input-group input-group-sm">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="background:#fff; border-right:0; padding-right:6px;">
                                        <i class="mdi mdi-magnify text-muted" style="font-size:16px; line-height:1;"></i>
                                    </span>
                                </div>
                                <input type="search" name="search" id="search-input" class="form-control"
                                    placeholder="Search category... (press Enter)" value="{{ $curSearch }}"
                                    style="border-left:0; padding-left:4px; box-shadow:none;">
                                @if($curSearch !== '')
                                    <div class="input-group-append" id="search-clear" style="cursor:pointer;">
                                        <span class="input-group-text" style="background:#fff; border-left:0;">
                                            <i class="mdi mdi-close text-muted" style="font-size:14px; line-height:1;"></i>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category</th>
                                    <th>Country</th>
                                    <th>Preview</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $group)
                                    @php
                                        $items = is_array($group->items) ? $group->items : [];
                                        $catName = $group->category->name ?? 'default';
                                        $countryLabel = $group->country === '' ? 'Global' : strtoupper($group->country);
                                    @endphp
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
                                        <td>
                                            @if(count($items))
                                                <div class="d-flex align-items-center flex-wrap" style="gap:6px;">
                                                    @foreach(array_slice($items, 0, 3) as $it)
                                                        <img src="{{ asset('upload/photoshoot/' . $catName . '/photoshoots/' . ($it['image'] ?? '')) }}"
                                                            alt="image" style="width: 44px; height: 44px; object-fit: contain; border:1px solid #eee; border-radius: 6px;">
                                                    @endforeach
                                                    <button type="button" class="btn btn-sm view-all-btn"
                                                        style="background:linear-gradient(to right,#b66dff,#8a3df0); color:#fff; padding:5px 12px; border:none; border-radius:20px; font-size:12px; font-weight:600;"
                                                        data-images='@json(array_column($items, "image"))'
                                                        data-names='@json(array_column($items, "name"))'
                                                        data-path="{{ asset('upload/photoshoot/' . $catName . '/photoshoots') }}"
                                                        data-title="{{ ($group->category->name ?? 'N/A') . ' · ' . $countryLabel }}">
                                                        <i class="mdi mdi-image-multiple"></i> View all ({{ count($items) }})
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('photoshoots.edit', $group->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $group->id }}"
                                                data-url="{{ route('photoshoots.destroy', $group->id) }}">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                                @if($groups->isEmpty())
                                    <tr><td colspan="5" class="text-center text-muted py-4">No photoshoots found.</td></tr>
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

    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title font-weight-bold" id="galleryModalLabel">Photoshoot images</h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"
                        style="border: none; background: transparent;">
                        <span aria-hidden="true" style="font-size: 2rem; font-weight: bold;">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row" id="gallery-content"></div>
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

            // Any select change reloads the page with the new query (resets to page 1).
            $(document).on('change', '#filter-form .auto-submit', function () {
                document.getElementById('filter-form').submit();
            });

            // Clear search -> submit empty.
            $(document).on('click', '#search-clear', function () {
                $('#search-input').val('');
                document.getElementById('filter-form').submit();
            });

            // Delete group
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
                            url: url, type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                            success: function (response) {
                                if (response.success) {
                                    Swal.fire('Deleted!', 'Photoshoot group has been deleted.', 'success')
                                        .then(function () { location.reload(); });
                                }
                            }
                        });
                    }
                });
            });

            // View-all gallery modal
            $(document).on('click', '.view-all-btn', function () {
                var images = $(this).data('images') || [];
                var names = $(this).data('names') || [];
                var path = $(this).data('path');
                var title = $(this).data('title');

                $('#galleryModalLabel').text(title + ' (' + images.length + ' images)');
                var html = '';
                images.forEach(function (img, i) {
                    html +=
                        '<div class="col-6 col-sm-4 col-md-2 mb-4">' +
                            '<div class="card h-100 border-0" style="position: relative;">' +
                                '<span class="badge badge-secondary" style="position:absolute; top:8px; left:8px; z-index:1; border-radius:4px; padding:4px 7px; font-size:11px;">' + (i + 1) + '</span>' +
                                '<img src="' + path + '/' + img + '" class="img-fluid rounded" style="box-shadow:0 2px 10px rgba(0,0,0,0.1); width:100%; height:150px; object-fit:contain; background:#f8f9fa;">' +
                                '<div class="text-center mt-1" style="font-size:12px;">' +
                                    '<div class="font-weight-bold text-truncate">' + (names[i] || '') + '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                });
                $('#gallery-content').html(html);
                $('#galleryModal').modal('show');
            });
            $(document).on('click', '#galleryModal .close', function () { $('#galleryModal').modal('hide'); });
        });
    </script>
@endsection
