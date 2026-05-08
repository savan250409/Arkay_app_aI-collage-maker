@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-api"></i>
            </span> API List
        </h3>
    </div>

    <div class="row g-4">
        <!-- Get Stickers API -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">1. Get Stickers</h4>
                    <p class="card-description">
                        <strong>Method:</strong> <span class="badge badge-success">POST</span>
                    </p>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>URL:</strong><br>
                        <code id="url-sticker"></code>
                    </div>
                    <p><strong>Headers:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-danger">Authorization</code> : Bearer YOUR_API_TOKEN</li>
                    </ul>
                    <p><strong>Description:</strong></p>
                    <p>Fetches all sticker categories and their stickers. Returns flattened URLs prefixed with
                        <code>sticker/</code>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Get Fonts API -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">2. Get Fonts</h4>
                    <p class="card-description">
                        <strong>Method:</strong> <span class="badge badge-success">POST</span>
                    </p>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>URL:</strong><br>
                        <code id="url-fonts"></code>
                    </div>
                    <p><strong>Headers:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-danger">Authorization</code> : Bearer YOUR_API_TOKEN</li>
                    </ul>
                    <p><strong>Description:</strong></p>
                    <p>Fetches all available fonts. File URLs are prefixed with <code>font/</code>.</p>
                </div>
            </div>
        </div>

        <!-- Get Doodles API -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">3. Get Doodles</h4>
                    <p class="card-description">
                        <strong>Method:</strong> <span class="badge badge-success">POST</span>
                    </p>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>URL:</strong><br>
                        <code id="url-doodles"></code>
                    </div>
                    <p><strong>Headers:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-danger">Authorization</code> : Bearer YOUR_API_TOKEN</li>
                    </ul>
                    <p><strong>Description:</strong></p>
                    <p>Fetches all doodles. Image URLs are prefixed with <code>doodle/</code>.</p>
                </div>
            </div>
        </div>

        <!-- Frame Categories API -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">4. Get Frame Categories</h4>
                    <p class="card-description">
                        <strong>Method:</strong> <span class="badge badge-info">GET</span>
                    </p>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>URL:</strong><br>
                        <code id="url-frame-cat"></code>
                    </div>
                    <p><strong>Headers:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-danger">Authorization</code> : Bearer YOUR_API_TOKEN</li>
                    </ul>
                    <p><strong>Description:</strong></p>
                    <p>Fetches all frame categories with latest 6 frames.</p>
                </div>
            </div>
        </div>

        <!-- Frames By Category API -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">5. Get Frames By Category</h4>
                    <p class="card-description">
                        <strong>Method:</strong> <span class="badge badge-success">POST</span>
                    </p>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>URL:</strong><br>
                        <code id="url-frame-by-id"></code>
                    </div>
                    <p><strong>Parameters:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-primary">category_id</code> (required)</li>
                    </ul>
                    <p><strong>Headers:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-danger">Authorization</code> : Bearer YOUR_API_TOKEN</li>
                    </ul>
                    <p><strong>Description:</strong></p>
                    <p>Fetches all frames for a specific category.</p>
                </div>
            </div>
        </div>

        <!-- Get Backgrounds API -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">6. Get Backgrounds</h4>
                    <p class="card-description">
                        <strong>Method:</strong> <span class="badge badge-success">POST</span>
                    </p>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>URL:</strong><br>
                        <code id="url-background"></code>
                    </div>
                    <p><strong>Headers:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-danger">Authorization</code> : Bearer YOUR_API_TOKEN</li>
                    </ul>
                    <p><strong>Description:</strong></p>
                    <p>Fetches all background categories and their backgrounds with individual free/pro type. URLs prefixed with
                        <code>background/</code>.
                    </p>
                </div>
            </div>
        </div>

        <!-- Get All Filter API -->
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">9. Get All Filters</h4>
                    <p class="card-description">
                        <strong>Method:</strong> <span class="badge badge-success">POST</span>
                    </p>
                    <div class="bg-light p-3 rounded mb-3">
                        <strong>URL:</strong><br>
                        <code id="url-get-all-filter"></code>
                    </div>
                    <p><strong>Headers:</strong></p>
                    <ul class="list-unstyled">
                        <li><code class="text-danger">Authorization</code> : Bearer YOUR_API_TOKEN</li>
                    </ul>
                    <p><strong>Description:</strong></p>
                    <p>Fetches all filter categories and their associated filters with adjustment values.</p>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            var baseUrl = window.location.origin;

            $('#url-sticker').text(baseUrl + '/api/get_sticker');
            $('#url-fonts').text(baseUrl + '/api/get_fonts');
            $('#url-doodles').text(baseUrl + '/api/get_doodles'); // Wait, route is get_doodles or get_doodle? Route says get_doodle
            $('#url-doodles').text(baseUrl + '/api/get_doodle');

            $('#url-frame-cat').text(baseUrl + '/api/get_frame_category');
            $('#url-frame-by-id').text(baseUrl + '/api/get_frame_by_category_id');
            $('#url-background').text(baseUrl + '/api/get_background');
            $('#url-get-all-filter').text(baseUrl + '/api/get_all_filter');
        });
    </script>
@endsection