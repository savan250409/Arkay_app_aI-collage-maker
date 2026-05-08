    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>NGD Admin</title>
        <!-- plugins:css -->
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/vendors/ti-icons/css/themify-icons.css') }}">
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/vendors/css/vendor.bundle.base.css') }}">
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
        <!-- endinject -->
        <!-- Layout styles -->
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/css/style.css') }}">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <!-- Per-page plugin CSS (e.g. dropify) opted in via @push('plugin-css') -->
        @stack('plugin-css')
        <!-- Inter font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- End layout styles -->
        <link rel="shortcut icon" href="{{ asset('adminpanel/dist/assets/images/favicon.png') }}" />
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/css/admin-theme.css') }}">
    </head>

    <body>
        <div class="container-scroller">

            <!-- partial:partials/_navbar.html -->
            <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
                <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
                    <a class="navbar-brand brand-logo" href="{{ route('dashboard') }}">
                        <h2 class="text-primary font-weight-bold">NGD</h2>
                    </a>
                    <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">
                        <h2 class="text-primary font-weight-bold">N</h2>
                    </a>
                </div>
                <div class="navbar-menu-wrapper d-flex align-items-stretch">
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                        <span class="mdi mdi-menu"></span>
                    </button>

                    <ul class="navbar-nav navbar-nav-right">
                        <li class="nav-item">
                            <a class="nav-link" id="clear-cache-logs-btn" href="#" title="Clear Cache & Logs">
                                <i class="mdi mdi-broom text-primary" style="font-size: 1.5rem;"></i>
                            </a>
                        </li>
                        <li class="nav-item nav-profile dropdown">
                            <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <div class="nav-profile-img">
                                    <!-- Use placeholder or user avatar -->
                                    <img src="{{ asset('adminpanel/dist/assets/images/faces/face1.jpg') }}" alt="image">
                                    <span class="availability-status online"></span>
                                </div>
                                <div class="nav-profile-text">
                                    <p class="mb-1 text-black">{{ Auth::guard('admin')->user()->email ?? 'Admin' }}</p>
                                </div>
                            </a>
                            <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                                <a class="dropdown-item" href="#"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="mdi mdi-logout me-2 text-primary"></i> Signout
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                        data-toggle="offcanvas">
                        <span class="mdi mdi-menu"></span>
                    </button>
                </div>
            </nav>
            <div class="container-fluid page-body-wrapper">
                <nav class="sidebar sidebar-offcanvas" id="sidebar">
                    <ul class="nav">
                        <li class="nav-item nav-profile">
                            <a href="#" class="nav-link">
                                <div class="nav-profile-image">
                                    <img src="{{ asset('adminpanel/dist/assets/images/faces/face1.jpg') }}" alt="profile" />
                                    <span class="login-status online"></span>
                                </div>
                                <div class="nav-profile-text d-flex flex-column">
                                    <span class="font-weight-bold mb-2">Admin</span>
                                    <span class="text-secondary text-small">NGD Admin</span>
                                </div>
                                <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <span class="menu-title">Dashboard</span>
                                <i class="mdi mdi-home menu-icon"></i>
                            </a>
                        </li>

                        @php
                            $routeName = (string) (Route::currentRouteName() ?? '');

                            $matchRoute = function (array $names) use ($routeName) {
                                foreach ($names as $name) {
                                    if ($routeName === $name || str_starts_with($routeName, $name . '.')) {
                                        return true;
                                    }
                                }
                                return false;
                            };

                            $frameActive = $matchRoute(['frame-categories', 'frames']);
                            $stickerActive = $matchRoute(['sticker-categories', 'stickers']);
                            $backgroundActive = $matchRoute(['background-categories', 'backgrounds']);
                            $filterActive = $matchRoute(['filter-categories', 'filters']);
                            $fontActive = $matchRoute(['fonts']);
                            $doodleActive = $matchRoute(['doodles']);
                            $apiListActive = $routeName === 'api-list';
                        @endphp

                        <li class="nav-item {{ $frameActive ? 'active' : '' }}">
                            <a class="nav-link" data-bs-toggle="collapse" href="#frame-menu"
                                aria-expanded="{{ $frameActive ? 'true' : 'false' }}"
                                aria-controls="frame-menu">
                                <span class="menu-title">Frame</span>
                                <i class="menu-arrow"></i>
                                <i class="mdi mdi-image-multiple menu-icon"></i>
                            </a>
                            <div class="collapse {{ $frameActive ? 'show' : '' }}"
                                id="frame-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a
                                            class="nav-link {{ $matchRoute(['frame-categories']) ? 'active' : '' }}"
                                            href="{{ route('frame-categories.index') }}">
                                            Category </a></li>
                                    <li class="nav-item"> <a class="nav-link {{ $matchRoute(['frames']) ? 'active' : '' }}"
                                            href="{{ route('frames.index') }}"> Frames
                                        </a></li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ $stickerActive ? 'active' : '' }}">
                            <a class="nav-link" data-bs-toggle="collapse" href="#sticker-menu"
                                aria-expanded="{{ $stickerActive ? 'true' : 'false' }}"
                                aria-controls="sticker-menu">
                                <span class="menu-title">Sticker</span>
                                <i class="menu-arrow"></i>
                                <i class="mdi mdi-sticker menu-icon"></i>
                            </a>
                            <div class="collapse {{ $stickerActive ? 'show' : '' }}"
                                id="sticker-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a
                                            class="nav-link {{ $matchRoute(['sticker-categories']) ? 'active' : '' }}"
                                            href="{{ route('sticker-categories.index') }}">
                                            Category </a></li>
                                    <li class="nav-item"> <a
                                            class="nav-link {{ $matchRoute(['stickers']) ? 'active' : '' }}"
                                            href="{{ route('stickers.index') }}"> Sticker
                                        </a></li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ $backgroundActive ? 'active' : '' }}">
                            <a class="nav-link" data-bs-toggle="collapse" href="#background-menu"
                                aria-expanded="{{ $backgroundActive ? 'true' : 'false' }}"
                                aria-controls="background-menu">
                                <span class="menu-title">Background</span>
                                <i class="menu-arrow"></i>
                                <i class="mdi mdi-image menu-icon"></i>
                            </a>
                            <div class="collapse {{ $backgroundActive ? 'show' : '' }}"
                                id="background-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a
                                            class="nav-link {{ $matchRoute(['background-categories']) ? 'active' : '' }}"
                                            href="{{ route('background-categories.index') }}">
                                            Category </a></li>
                                    <li class="nav-item"> <a
                                            class="nav-link {{ $matchRoute(['backgrounds']) ? 'active' : '' }}"
                                            href="{{ route('backgrounds.index') }}"> Background
                                        </a></li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item {{ $fontActive ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('fonts.index') }}">
                                <span class="menu-title">Font</span>
                                <i class="mdi mdi-format-font menu-icon"></i>
                            </a>
                        </li>
                        <li class="nav-item {{ $doodleActive ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('doodles.index') }}">
                                <span class="menu-title">Doodle</span>
                                <i class="mdi mdi-creation menu-icon"></i>
                            </a>
                        </li>

                        <li class="nav-item {{ $filterActive ? 'active' : '' }}">
                            <a class="nav-link" data-bs-toggle="collapse" href="#filter-menu"
                                aria-expanded="{{ $filterActive ? 'true' : 'false' }}"
                                aria-controls="filter-menu">
                                <span class="menu-title">Filter</span>
                                <i class="menu-arrow"></i>
                                <i class="mdi mdi-filter menu-icon"></i>
                            </a>
                            <div class="collapse {{ $filterActive ? 'show' : '' }}"
                                id="filter-menu">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a
                                            class="nav-link {{ $matchRoute(['filter-categories']) ? 'active' : '' }}"
                                            href="{{ route('filter-categories.index') }}">
                                            Category </a></li>
                                    <li class="nav-item"> <a
                                            class="nav-link {{ $matchRoute(['filters']) ? 'active' : '' }}"
                                            href="{{ route('filters.index') }}"> Filters
                                        </a></li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item {{ $apiListActive ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('api-list') }}">
                                <span class="menu-title">API List</span>
                                <i class="mdi mdi-api menu-icon"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- partial -->
                <div class="main-panel">
                    <div class="content-wrapper">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ asset('adminpanel/dist/assets/vendors/js/vendor.bundle.base.js') }}"></script>
        <script src="{{ asset('adminpanel/dist/assets/js/off-canvas.js') }}"></script>
        <script src="{{ asset('adminpanel/dist/assets/js/misc.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- Per-page plugin JS (e.g. dropify) opted in via @push('plugin-js') -->
        @stack('plugin-js')
        <script>
            $(document).ready(function () {
                // Auto-hide alerts after 5 seconds
                setTimeout(function () {
                    $(".alert").alert('close');
                }, 5000);

                $(document).on('click', '#clear-cache-logs-btn', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Final Warning!',
                        text: "This will permanently clear all system cache and log files. Are you really sure?",
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, clear it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Clearing...',
                                text: 'Please wait...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            });

                            $.ajax({
                                url: "{{ route('system.clear-cache-logs') }}",
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (response) {
                                    if (response.success) {
                                        Swal.fire(
                                            'Success!',
                                            response.message,
                                            'success'
                                        );
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            response.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function (xhr) {
                                    var msg = 'Something went wrong. Please try again.';
                                    if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                                        msg = xhr.responseJSON.message;
                                    } else if (xhr && xhr.status) {
                                        msg = 'HTTP ' + xhr.status + ' - ' + (xhr.statusText || 'Request failed');
                                    }
                                    Swal.fire(
                                        'Error!',
                                        msg,
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });
        </script>
        @yield('scripts')
    </body>

    </html>

