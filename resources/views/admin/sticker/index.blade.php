@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-sm-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-sticker text-primary" style="font-size: 2rem; margin-right: 15px;"></i>
                            <div>
                                <h3 class="text-primary mb-0" style="font-weight: bold;">Sticker Management</h3>
                                <small class="text-muted">Manage stickers</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 1rem;">
                            <div class="bg-light px-3 py-2 rounded mr-3 text-dark font-weight-bold"
                                style="border: 1px solid #e3e3e3;">
                                <i class="mdi mdi-layers-outline mr-1"></i> Total: {{ $stickers->total() }} Stickers
                            </div>
                            <a href="{{ route('stickers.create') }}" class="btn btn-primary btn-sm btn-icon-text"
                                style="padding: 0.5rem 0.8rem;">
                                <i class="mdi mdi-plus btn-icon-prepend"></i> Add Sticker
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
                                placeholder="Search categories..."
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
                                        <th>Category Name</th>
                                        <th>Sticker Images</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($stickers as $sticker)
                                        <tr>
                                            <td>{{ $sticker->id }}</td>
                                            <td>{{ $sticker->category->name ?? 'N/A' }}</td>
                                            <td>
                                                @if(!empty($sticker->images) && is_array($sticker->images))
                                                    <div class="d-flex align-items-center flex-wrap">
                                                        @foreach(array_slice($sticker->images, 0, 2) as $img)
                                                            <div class="mr-2 mb-2">
                                                                <img src="{{ asset('upload/sticker/' . ($sticker->category->name ?? 'default') . '/stickers/' . $img) }}"
                                                                    alt="image" class="img-thumbnail"
                                                                    style="width: 60px; height: 90px; object-fit: contain; border-radius: 8px; padding: 2px;">
                                                            </div>
                                                        @endforeach
                                                        @if(count($sticker->images) > 2)
                                                            <div class="view-more-btn mb-2"
                                                                data-images="{{ json_encode($sticker->images) }}"
                                                                data-category="{{ $sticker->category->name ?? 'Category' }}"
                                                                data-path="{{ asset('upload/sticker/' . ($sticker->category->name ?? 'default') . '/stickers/') }}"
                                                                style="cursor: pointer; width: 90px; height: 90px; background: linear-gradient(to right, #87CEEB, #00BFFF); border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                                                                <span
                                                                    style="font-weight: bold; font-size: 11px; text-transform: uppercase;">VIEW
                                                                    MORE</span>
                                                                <span
                                                                    style="font-weight: bold; font-size: 18px;">(+{{ count($sticker->images) - 2 }})</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('stickers.edit', $sticker->id) }}"
                                                    class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $sticker->id }}"
                                                    data-url="{{ route('stickers.destroy', $sticker->id) }}">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-3 align-items-center">
                            <div class="col-sm-6">
                                <div class="dataTables_info" id="order-listing_info" role="status" aria-live="polite">
                                    Showing {{ $stickers->firstItem() ?? 0 }} to {{ $stickers->lastItem() ?? 0 }} of
                                    {{ $stickers->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6 d-flex justify-content-end">
                                <div class="dataTables_paginate paging_simple_numbers" id="order-listing_paginate">
                                    {!! $stickers->appends(request()->query())->links('pagination::bootstrap-4') !!}
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" role="dialog" aria-labelledby="galleryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document" style="max-width: 90%;">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title font-weight-bold" id="galleryModalLabel">Ai Collage Maker / Sticker (10 images)
                    </h5>
                    <button type="button" class="close text-dark" data-dismiss="modal" data-bs-dismiss="modal"
                        aria-label="Close" style="border: none; background: transparent;">
                        <span aria-hidden="true" style="font-size: 2rem; font-weight: bold;">&times;</span>
                    </button>
                </div>
                <div class="modal-body pt-0">
                    <div class="row" id="gallery-content">
                        <!-- Dynamic Content -->
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

            $(document).on('click', '.view-more-btn', function () {
                var images = $(this).data('images');
                var category = $(this).data('category');
                var basePath = $(this).data('path');
                var count = images.length;

                $('#galleryModalLabel').text(`Ai Collage Maker / ${category} (${count} images)`);
                var html = '';

                images.forEach((img, index) => {
                    html += `
                                                                <div class="col-md-2 mb-4">
                                                                    <div class="card h-100 border-0" style="position: relative;">
                                                                       <span class="badge badge-secondary" style="position: absolute; top: 10px; left: 10px; z-index: 1; border-radius: 4px; padding: 5px 8px; font-size: 12px; background-color: #6c757d;">${index + 1}</span>
                                                                       <img src="${basePath}/${img}" class="img-fluid rounded" style="box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; height: auto; object-fit: contain;">
                                                                    </div>
                                                                </div>
                                                            `;
                });

                $('#gallery-content').html(html);
                $('#galleryModal').modal('show');
            });

            $(document).on('click', '.modal .close', function () {
                $(this).closest('.modal').modal('hide');
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
                            text: "This will permanently delete this sticker and its image files. Are you really sure?",
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
                                                'Sticker and its files have been deleted.',
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

            var searchTimer;

            function fetch_data(page) {
                var search = $('#search-input').val();
                var per_page = $('#per_page').val();
                var category_id = $('#category-filter').val();

                $.ajax({
                    url: "{{ route('stickers.index') }}",
                    data: { page: page, search: search, per_page: per_page, category_id: category_id },
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
        });
    </script>
@endsection