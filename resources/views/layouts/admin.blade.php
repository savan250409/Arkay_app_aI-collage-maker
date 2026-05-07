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
        <!-- Plugin css for this page -->
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/vendors/font-awesome/css/font-awesome.min.css') }}" />
        <link rel="stylesheet"
            href="{{ asset('adminpanel/dist/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
        <!-- End plugin css for this page -->
        <!-- inject:css -->
        <!-- endinject -->
        <!-- Layout styles -->
        <link rel="stylesheet" href="{{ asset('adminpanel/dist/assets/css/style.css') }}">
        <!-- Dropify CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/css/dropify.min.css">
        <!-- SweetAlert2 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <!-- Inter font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- End layout styles -->
        <link rel="shortcut icon" href="{{ asset('adminpanel/dist/assets/images/favicon.png') }}" />
        <style>
            /* ==========================================================
               MODERN MINIMAL LIGHT THEME
               ========================================================== */
            :root {
                --bg-app: #f6f8fb;
                --bg-surface: #ffffff;
                --bg-muted: #f3f5f9;
                --bg-hover: #eef1f6;
                --border: #e6e8ee;
                --border-strong: #d6d9e0;
                --text-primary: #0f172a;
                --text-secondary: #5b6473;
                --text-muted: #94a3b8;
                --accent: #6366f1;
                --accent-hover: #4f46e5;
                --accent-soft: #eef2ff;
                --success: #10b981;
                --success-soft: #ecfdf5;
                --warning: #f59e0b;
                --warning-soft: #fffbeb;
                --danger: #ef4444;
                --danger-soft: #fef2f2;
                --info: #0ea5e9;
                --info-soft: #f0f9ff;
                --radius: 12px;
                --radius-sm: 8px;
                --radius-pill: 999px;
                --shadow-xs: 0 1px 2px rgba(15, 23, 42, .04);
                --shadow-sm: 0 1px 3px rgba(15, 23, 42, .06), 0 1px 2px rgba(15, 23, 42, .04);
                --shadow-md: 0 4px 12px rgba(15, 23, 42, .06);
                --shadow-lg: 0 10px 30px rgba(15, 23, 42, .08);
            }

            /* LOCK PAGE SCROLL */
            html,
            body {
                height: 100vh;
                overflow: hidden;
            }

            body,
            body * {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            }

            body {
                background: var(--bg-app) !important;
                color: var(--text-primary) !important;
                font-size: 14px !important;
                -webkit-font-smoothing: antialiased;
            }

            /* WRAPPER SETUP */
            .container-scroller {
                height: 100%;
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .page-body-wrapper {
                flex: 1;
                min-height: 0;
                display: flex;
                overflow: hidden !important;
                padding-bottom: 0;
                background: var(--bg-app);
            }

            /* Override bundled template rule that adds padding-top: 70px to wrapper after fixed-top navbar */
            .navbar.fixed-top + .page-body-wrapper {
                padding-top: 0 !important;
            }

            .sidebar {
                height: 100%;
                overflow-y: auto;
                position: relative;
                z-index: 100;
            }

            .main-panel {
                height: 100%;
                overflow-y: auto;
                flex: 1;
                position: relative;
                display: flex;
                flex-direction: column;
                padding-bottom: 0 !important;
                margin-bottom: 0 !important;
                background: var(--bg-app);
            }

            .content-wrapper {
                flex: 1;
                width: 100%;
                padding: 1.75rem 2rem !important;
                margin-bottom: 0 !important;
                background: var(--bg-app) !important;
                display: flex;
                flex-direction: column;
            }

            /* Last row of any page automatically stretches to fill remaining height */
            .content-wrapper > .row:last-child,
            .content-wrapper > .row.fill-row {
                flex: 1;
                min-height: 0;
            }

            .content-wrapper > .row:last-child > [class*="col-"],
            .content-wrapper > .row.fill-row > [class*="col-"] {
                display: flex;
                flex-direction: column;
                margin-bottom: 0 !important;
            }

            .content-wrapper > .row:last-child > [class*="col-"] > .card,
            .content-wrapper > .row.fill-row > [class*="col-"] > .card {
                flex: 1;
            }

            .main-panel>*:last-child {
                margin-bottom: 0 !important;
                padding-bottom: 0 !important;
            }

            /* --- CUSTOM SCROLLBARS --- */
            .sidebar::-webkit-scrollbar { width: 4px; }
            .sidebar::-webkit-scrollbar-track { background: transparent; }
            .sidebar::-webkit-scrollbar-thumb { background: #d6d9e0; border-radius: 4px; }
            .sidebar::-webkit-scrollbar-thumb:hover { background: var(--accent); }

            .main-panel::-webkit-scrollbar { width: 8px; }
            .main-panel::-webkit-scrollbar-track { background: transparent; }
            .main-panel::-webkit-scrollbar-thumb { background: #d6d9e0; border-radius: 4px; }
            .main-panel::-webkit-scrollbar-thumb:hover { background: #a8b0bd; }

            /* ===================== NAVBAR ===================== */
            .navbar.default-layout-navbar {
                position: relative !important;
                flex-shrink: 0;
                background: var(--bg-surface) !important;
                border-bottom: 1px solid var(--border) !important;
                box-shadow: none !important;
                height: 64px !important;
                padding: 0 !important;
            }

            .navbar .navbar-brand-wrapper {
                background: transparent !important;
                border: none !important;
                width: 250px !important;
                height: 64px !important;
                padding: 0 1.5rem !important;
            }

            .navbar .navbar-brand-wrapper .brand-logo h2,
            .navbar .navbar-brand-wrapper .brand-logo-mini h2 {
                color: var(--accent) !important;
                font-weight: 700 !important;
                letter-spacing: -0.5px;
                margin: 0;
                font-size: 1.5rem;
            }

            .navbar .navbar-menu-wrapper {
                padding: 0 1.5rem !important;
                background: transparent !important;
                border: none !important;
            }

            .navbar .navbar-toggler {
                color: var(--text-secondary) !important;
                font-size: 1.4rem;
            }

            .navbar .navbar-nav .nav-item .nav-link {
                color: var(--text-secondary) !important;
                font-size: 14px !important;
                padding: 8px 12px !important;
                border-radius: var(--radius-sm);
                transition: background .15s ease;
            }

            .navbar .navbar-nav .nav-item .nav-link:hover {
                background: var(--bg-muted) !important;
            }

            .navbar .nav-profile .nav-profile-img img {
                width: 36px !important;
                height: 36px !important;
                border-radius: 50% !important;
                border: 2px solid var(--border);
            }

            .navbar .nav-profile .nav-profile-img .availability-status {
                width: 10px !important;
                height: 10px !important;
                border: 2px solid #fff;
                bottom: 0 !important;
                right: 0 !important;
            }

            .navbar .nav-profile .nav-profile-text p {
                color: var(--text-primary) !important;
                font-weight: 500 !important;
                margin-bottom: 0 !important;
                margin-left: 10px;
            }

            .navbar .dropdown-menu {
                border: 1px solid var(--border) !important;
                border-radius: var(--radius-sm) !important;
                box-shadow: var(--shadow-md) !important;
                padding: 6px !important;
                margin-top: 8px !important;
            }

            .navbar .dropdown-menu .dropdown-item {
                border-radius: 6px;
                padding: 8px 12px;
                font-size: 14px;
                color: var(--text-primary);
            }

            .navbar .dropdown-menu .dropdown-item:hover {
                background: var(--bg-muted);
            }

            /* ===================== SIDEBAR ===================== */
            .sidebar {
                background: var(--bg-surface) !important;
                border-right: 1px solid var(--border) !important;
                width: 250px !important;
                padding-top: 12px !important;
                box-shadow: none !important;
            }

            .sidebar .nav {
                padding: 0 !important;
            }

            .sidebar .nav .nav-item {
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
            }

            .sidebar .nav .nav-item .nav-link {
                color: var(--text-secondary) !important;
                background: transparent !important;
                border-radius: var(--radius-sm) !important;
                margin: 2px 12px !important;
                padding: 10px 14px !important;
                height: auto !important;
                font-size: 14px !important;
                font-weight: 500 !important;
                display: flex !important;
                align-items: center !important;
                transition: all .15s ease;
                white-space: nowrap;
            }

            .sidebar .nav .nav-item .nav-link:hover {
                background: var(--bg-muted) !important;
                color: var(--text-primary) !important;
            }

            .sidebar .nav .nav-item.active > .nav-link {
                background: var(--accent-soft) !important;
                color: var(--accent) !important;
                font-weight: 600 !important;
            }

            .sidebar .nav .nav-item .nav-link .menu-title {
                flex-grow: 1 !important;
                color: inherit !important;
                font-weight: inherit !important;
            }

            .sidebar .nav .nav-item .nav-link .menu-icon {
                background: transparent !important;
                color: var(--text-muted) !important;
                font-size: 18px !important;
                margin: 0 !important;
                margin-right: 12px !important;
                width: auto !important;
                height: auto !important;
                order: -1;
            }

            .sidebar .nav .nav-item.active > .nav-link .menu-icon,
            .sidebar .nav .nav-item .nav-link:hover .menu-icon {
                color: var(--accent) !important;
            }

            .sidebar .nav .nav-item .nav-link .menu-arrow {
                color: var(--text-muted) !important;
                margin-left: auto !important;
            }

            .sidebar .nav .nav-item .nav-link[aria-expanded="true"] .menu-arrow {
                color: var(--accent) !important;
            }

            .sidebar .nav .nav-item .nav-link .menu-arrow:before {
                content: "\F0142";
                font-family: "Material Design Icons";
                font-size: 16px;
            }

            .sidebar .nav .nav-item .nav-link[aria-expanded="true"] .menu-arrow:before {
                content: "\F0140";
            }

            .sidebar .nav-profile {
                padding: 14px 18px !important;
                margin: 0 0 8px !important;
                border-bottom: 1px solid var(--border) !important;
                background: transparent !important;
            }

            .sidebar .nav-profile .nav-link {
                background: transparent !important;
                margin: 0 !important;
                padding: 0 !important;
                display: flex !important;
                align-items: center !important;
            }

            .sidebar .nav-profile .nav-link:hover {
                background: transparent !important;
            }

            .sidebar .nav-profile .nav-profile-image {
                position: relative;
                margin-right: 12px;
            }

            .sidebar .nav-profile .nav-profile-image img {
                width: 40px !important;
                height: 40px !important;
                border-radius: 50% !important;
                border: 2px solid var(--border);
            }

            .sidebar .nav-profile .nav-profile-image .login-status {
                width: 10px !important;
                height: 10px !important;
                bottom: 0 !important;
                right: 0 !important;
                border: 2px solid #fff;
                background: var(--success);
            }

            .sidebar .nav-profile .nav-profile-text {
                flex-grow: 1;
            }

            .sidebar .nav-profile .nav-profile-text .font-weight-bold {
                color: var(--text-primary) !important;
                font-size: 14px !important;
                font-weight: 600 !important;
                margin-bottom: 2px !important;
            }

            .sidebar .nav-profile .nav-profile-text .text-secondary {
                color: var(--text-muted) !important;
                font-size: 12px !important;
            }

            .sidebar .nav-profile-badge {
                color: var(--success) !important;
            }

            .sidebar .sub-menu {
                background: transparent !important;
                padding: 2px 12px 6px 40px !important;
                margin: 0 !important;
                list-style: none !important;
                border: none !important;
            }

            .sidebar .sub-menu .nav-item {
                padding: 0 !important;
                margin: 0 !important;
            }

            .sidebar .sub-menu .nav-item .nav-link {
                padding: 7px 12px !important;
                font-size: 13px !important;
                margin: 1px 0 !important;
                color: var(--text-secondary) !important;
                background: transparent !important;
                border-radius: var(--radius-sm) !important;
                position: relative;
                display: block !important;
                font-weight: 500 !important;
                line-height: 1.4 !important;
                white-space: nowrap;
            }

            /* Kill template's ::before indicator (the stray "→" / dot) */
            .sidebar .sub-menu .nav-item .nav-link::before,
            .sidebar .sub-menu .nav-item .nav-link::after {
                content: none !important;
                display: none !important;
                background: transparent !important;
                border: none !important;
            }

            .sidebar .sub-menu .nav-item .nav-link.active {
                background: var(--accent-soft) !important;
                color: var(--accent) !important;
                font-weight: 600 !important;
            }

            .sidebar .sub-menu .nav-item .nav-link:hover {
                color: var(--accent) !important;
                background: var(--bg-muted) !important;
            }

            /* ============ SIDEBAR COLLAPSED (icon-only) STATE ============ */
            body.sidebar-icon-only .navbar .navbar-brand-wrapper {
                width: 70px !important;
                padding: 0 !important;
                justify-content: center !important;
            }

            body.sidebar-icon-only .navbar .navbar-brand-wrapper .brand-logo {
                display: none !important;
            }

            body.sidebar-icon-only .navbar .navbar-brand-wrapper .brand-logo-mini {
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                padding: 0 !important;
                margin: 0 !important;
            }

            .navbar .navbar-brand-wrapper .brand-logo-mini {
                display: none;
            }

            body.sidebar-icon-only .sidebar {
                width: 70px !important;
            }

            body.sidebar-icon-only .sidebar .nav .nav-item .nav-link {
                justify-content: center !important;
                padding: 12px !important;
                margin: 2px 10px !important;
                position: relative;
            }

            body.sidebar-icon-only .sidebar .nav .nav-item .nav-link .menu-arrow {
                display: none !important;
            }

            body.sidebar-icon-only .sidebar .nav .nav-item .nav-link .menu-title {
                position: absolute;
                left: calc(100% + 6px);
                top: 50%;
                transform: translateY(-50%);
                background: var(--text-primary);
                color: #fff !important;
                font-size: 12px !important;
                font-weight: 500 !important;
                padding: 6px 10px;
                border-radius: 6px;
                white-space: nowrap;
                z-index: 1000;
                box-shadow: var(--shadow-md);
                pointer-events: none;
                opacity: 0;
                visibility: hidden;
                transition: opacity .12s ease;
            }

            body.sidebar-icon-only .sidebar .nav .nav-item .nav-link:hover .menu-title {
                opacity: 1;
                visibility: visible;
            }

            body.sidebar-icon-only .sidebar .nav .nav-item .nav-link .menu-icon {
                margin-right: 0 !important;
                font-size: 20px !important;
            }

            body.sidebar-icon-only .sidebar .nav-profile {
                padding: 14px 0 !important;
                text-align: center;
            }

            body.sidebar-icon-only .sidebar .nav-profile .nav-link {
                justify-content: center !important;
            }

            body.sidebar-icon-only .sidebar .nav-profile .nav-profile-image {
                margin-right: 0 !important;
            }

            body.sidebar-icon-only .sidebar .nav-profile .nav-profile-text,
            body.sidebar-icon-only .sidebar .nav-profile-badge {
                display: none !important;
            }

            body.sidebar-icon-only .sidebar .sub-menu,
            body.sidebar-icon-only .sidebar .nav .collapse {
                display: none !important;
            }

            /* ===================== CARDS ===================== */
            .card {
                background: var(--bg-surface) !important;
                border: 1px solid var(--border) !important;
                border-radius: var(--radius) !important;
                box-shadow: var(--shadow-xs) !important;
            }

            .card-body {
                padding: 1.5rem !important;
            }

            /* ===================== PAGE HEADER ===================== */
            .page-header {
                margin-bottom: 1.5rem !important;
                background: transparent !important;
                padding: 0 !important;
                border: none !important;
                display: flex;
                align-items: center;
                justify-content: space-between;
                flex-wrap: wrap;
            }

            .page-header .page-title {
                color: var(--text-primary) !important;
                font-weight: 600 !important;
                font-size: 1.25rem !important;
                margin-bottom: 0 !important;
                display: flex;
                align-items: center;
            }

            .page-header .page-title-icon {
                width: 40px !important;
                height: 40px !important;
                border-radius: var(--radius-sm) !important;
                background: var(--accent-soft) !important;
                color: var(--accent) !important;
                display: inline-flex !important;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                margin-right: 12px !important;
            }

            .page-header .page-title-icon i {
                line-height: 1 !important;
                color: var(--accent) !important;
            }

            .page-header .breadcrumb {
                background: transparent !important;
                padding: 0 !important;
                margin-bottom: 0 !important;
                font-size: 13px;
            }

            .page-header .breadcrumb-item.active {
                color: var(--text-secondary) !important;
            }

            /* ===================== BUTTONS ===================== */
            .btn {
                border-radius: var(--radius-sm) !important;
                font-weight: 500 !important;
                font-size: 14px !important;
                padding: 0.5rem 1rem !important;
                transition: all .15s ease;
                box-shadow: none !important;
                border-width: 1px !important;
            }

            .btn:focus {
                box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important;
            }

            .btn-sm {
                padding: 0.4rem 0.75rem !important;
                font-size: 13px !important;
            }

            .btn-primary {
                background: var(--accent) !important;
                border-color: var(--accent) !important;
                color: #fff !important;
            }

            .btn-primary:hover,
            .btn-primary:active {
                background: var(--accent-hover) !important;
                border-color: var(--accent-hover) !important;
            }

            .btn-success {
                background: var(--success) !important;
                border-color: var(--success) !important;
                color: #fff !important;
            }

            .btn-warning {
                background: var(--warning) !important;
                border-color: var(--warning) !important;
                color: #fff !important;
            }

            .btn-warning:hover {
                background: #d97706 !important;
                border-color: #d97706 !important;
                color: #fff !important;
            }

            .btn-danger {
                background: var(--danger) !important;
                border-color: var(--danger) !important;
                color: #fff !important;
            }

            .btn-danger:hover {
                background: #dc2626 !important;
                border-color: #dc2626 !important;
            }

            .btn-info {
                background: var(--info) !important;
                border-color: var(--info) !important;
                color: #fff !important;
            }

            .btn-secondary,
            .btn-outline-secondary {
                background: var(--bg-surface) !important;
                border-color: var(--border-strong) !important;
                color: var(--text-secondary) !important;
            }

            .btn-secondary:hover,
            .btn-outline-secondary:hover {
                background: var(--bg-muted) !important;
                color: var(--text-primary) !important;
            }

            /* ===================== FORMS ===================== */
            .form-control,
            .form-select {
                border-radius: var(--radius-sm) !important;
                border: 1px solid var(--border) !important;
                font-size: 14px !important;
                padding: 0.55rem 0.85rem !important;
                color: var(--text-primary) !important;
                background-color: var(--bg-surface) !important;
                transition: all .15s ease;
                box-shadow: none !important;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: var(--accent) !important;
                box-shadow: 0 0 0 3px rgba(99, 102, 241, .12) !important;
                outline: none !important;
            }

            .form-control::placeholder {
                color: var(--text-muted) !important;
            }

            .form-control-sm {
                font-size: 13px !important;
                padding: 0.4rem 0.7rem !important;
            }

            label,
            .form-label {
                color: var(--text-secondary);
                font-weight: 500;
                font-size: 13px;
                margin-bottom: 6px;
            }

            /* ===================== TABLES ===================== */
            .table {
                color: var(--text-primary) !important;
                margin-bottom: 0 !important;
                border-collapse: separate;
                border-spacing: 0;
            }

            .table thead th {
                background: var(--bg-muted) !important;
                color: var(--text-secondary) !important;
                border-top: none !important;
                border-bottom: 1px solid var(--border) !important;
                font-weight: 600 !important;
                text-transform: uppercase;
                font-size: 11px !important;
                letter-spacing: 0.5px;
                padding: 0.85rem 1rem !important;
            }

            .table tbody td {
                padding: 0.95rem 1rem !important;
                border-top: 1px solid var(--border) !important;
                vertical-align: middle;
                font-size: 14px;
                color: var(--text-primary);
            }

            .table-striped > tbody > tr:nth-of-type(odd) > * {
                background: transparent !important;
                color: inherit !important;
            }

            .table tbody tr:hover > * {
                background: var(--bg-muted) !important;
            }

            .table .img-thumbnail {
                background: var(--bg-surface) !important;
                border: 1px solid var(--border) !important;
                padding: 2px !important;
                border-radius: var(--radius-sm) !important;
            }

            /* ===================== ALERTS ===================== */
            .alert {
                border-radius: var(--radius-sm) !important;
                border: 1px solid transparent !important;
                padding: 0.85rem 1rem !important;
                font-size: 14px !important;
            }

            .alert-success {
                background: var(--success-soft) !important;
                border-color: rgba(16, 185, 129, .25) !important;
                color: #047857 !important;
            }

            .alert-danger {
                background: var(--danger-soft) !important;
                border-color: rgba(239, 68, 68, .25) !important;
                color: #b91c1c !important;
            }

            .alert-warning {
                background: var(--warning-soft) !important;
                border-color: rgba(245, 158, 11, .25) !important;
                color: #b45309 !important;
            }

            .alert-info {
                background: var(--info-soft) !important;
                border-color: rgba(14, 165, 233, .25) !important;
                color: #0369a1 !important;
            }

            /* ===================== BADGES ===================== */
            .badge {
                font-weight: 500 !important;
                padding: 0.35em 0.65em !important;
                border-radius: 6px !important;
                font-size: 11px !important;
            }

            /* ===================== MODALS ===================== */
            .modal-content {
                border: 1px solid var(--border) !important;
                border-radius: var(--radius) !important;
                box-shadow: var(--shadow-lg) !important;
            }

            .modal-header {
                border-bottom: 1px solid var(--border) !important;
                padding: 1.25rem 1.5rem !important;
            }

            .modal-title {
                color: var(--text-primary) !important;
                font-weight: 600 !important;
            }

            .modal-body {
                padding: 1.5rem !important;
            }

            /* ===================== PAGINATION ===================== */
            .pagination .page-link {
                color: var(--text-secondary) !important;
                background: var(--bg-surface) !important;
                border: 1px solid var(--border) !important;
                margin: 0 2px;
                border-radius: var(--radius-sm) !important;
                font-size: 13px;
                padding: 0.4rem 0.75rem;
            }

            .pagination .page-link:hover {
                background: var(--bg-muted) !important;
                color: var(--accent) !important;
            }

            .pagination .page-item.active .page-link {
                background: var(--accent) !important;
                border-color: var(--accent) !important;
                color: #fff !important;
            }

            /* ===================== TEXT UTILITIES ===================== */
            .text-primary {
                color: var(--accent) !important;
            }

            .text-muted {
                color: var(--text-muted) !important;
            }

            .text-secondary {
                color: var(--text-secondary) !important;
            }

            .border-bottom {
                border-bottom: 1px solid var(--border) !important;
            }

            .bg-light {
                background: var(--bg-muted) !important;
            }

            /* ===================== DASHBOARD STAT CARDS ===================== */
            .stat-card {
                background: var(--bg-surface);
                border: 1px solid var(--border);
                border-radius: var(--radius);
                padding: 1.5rem;
                transition: all .2s ease;
                text-decoration: none !important;
                color: var(--text-primary) !important;
                display: block;
                height: 100%;
                box-shadow: var(--shadow-xs);
            }

            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
                border-color: var(--border-strong);
                color: var(--text-primary) !important;
            }

            .stat-card .stat-icon {
                width: 48px;
                height: 48px;
                border-radius: var(--radius-sm);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
                margin-bottom: 1rem;
            }

            .stat-card .stat-icon.icon-indigo { background: #eef2ff; color: #6366f1; }
            .stat-card .stat-icon.icon-emerald { background: #ecfdf5; color: #10b981; }
            .stat-card .stat-icon.icon-sky { background: #f0f9ff; color: #0ea5e9; }
            .stat-card .stat-icon.icon-amber { background: #fffbeb; color: #f59e0b; }
            .stat-card .stat-icon.icon-rose { background: #fff1f2; color: #f43f5e; }
            .stat-card .stat-icon.icon-violet { background: #f5f3ff; color: #8b5cf6; }

            .stat-card .stat-label {
                font-size: 13px;
                color: var(--text-secondary);
                font-weight: 500;
                margin-bottom: 4px;
            }

            .stat-card .stat-value {
                font-size: 1.85rem;
                font-weight: 700;
                color: var(--text-primary);
                margin-bottom: 0;
                line-height: 1.1;
                letter-spacing: -0.5px;
            }

            .stat-card .stat-link {
                font-size: 13px;
                color: var(--accent);
                font-weight: 500;
                margin-top: 1rem;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            /* Override legacy gradient backgrounds when used inside cards as content */
            .card.bg-gradient-danger,
            .card.bg-gradient-success,
            .card.bg-gradient-info,
            .card.bg-gradient-primary,
            .card.bg-gradient-warning {
                background: var(--bg-surface) !important;
                color: var(--text-primary) !important;
            }

            .card-img-absolute { display: none !important; }

            /* Bootstrap close button */
            .close, .btn-close {
                opacity: 0.5;
            }
            .close:hover, .btn-close:hover {
                opacity: 1;
            }

            /* Search wrapper inside controls */
            .search-wrapper input.form-control {
                background-color: var(--bg-surface) !important;
            }

            /* ============================================================
               LEGACY MARKUP OVERRIDES — clean up patterns used in module views
               so every screen inherits the new look without per-view edits.
               ============================================================ */

            /* Card header pattern: `border-bottom pb-3 mb-4` heading row */
            .card-body > .d-flex.border-bottom {
                border-bottom: 1px solid var(--border) !important;
                padding-bottom: 1.25rem !important;
                margin-bottom: 1.5rem !important;
            }

            /* Big icon next to page title → modern soft-tinted icon badge */
            .card-body > .d-flex.border-bottom .mdi.text-primary[style*="font-size: 2rem"] {
                font-size: 18px !important;
                color: var(--accent) !important;
                background: var(--accent-soft);
                width: 40px;
                height: 40px;
                border-radius: var(--radius-sm);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-right: 14px !important;
                flex-shrink: 0;
            }

            /* Page heading h3 inside card */
            .card-body > .d-flex.border-bottom h3 {
                color: var(--text-primary) !important;
                font-weight: 600 !important;
                font-size: 1.125rem !important;
                margin-bottom: 2px !important;
                letter-spacing: -0.2px;
            }

            .card-body > .d-flex.border-bottom small.text-muted {
                color: var(--text-secondary) !important;
                font-size: 13px !important;
            }

            /* Total: X count badge */
            .card-body .bg-light.px-3.py-2.rounded {
                background: var(--accent-soft) !important;
                color: var(--accent) !important;
                border: 1px solid rgba(99, 102, 241, 0.15) !important;
                border-radius: var(--radius-pill) !important;
                font-weight: 500 !important;
                font-size: 13px !important;
                padding: 6px 14px !important;
            }

            .card-body .bg-light.px-3.py-2.rounded .mdi {
                color: var(--accent) !important;
            }

            /* Gradient buttons → flatten to solid theme colors */
            .btn-gradient-primary {
                background: var(--accent) !important;
                background-image: none !important;
                border-color: var(--accent) !important;
                color: #fff !important;
                box-shadow: none !important;
            }

            .btn-gradient-primary:hover {
                background: var(--accent-hover) !important;
                border-color: var(--accent-hover) !important;
            }

            .btn-gradient-success { background: var(--success) !important; background-image: none !important; border-color: var(--success) !important; color: #fff !important; }
            .btn-gradient-info { background: var(--info) !important; background-image: none !important; border-color: var(--info) !important; color: #fff !important; }
            .btn-gradient-warning { background: var(--warning) !important; background-image: none !important; border-color: var(--warning) !important; color: #fff !important; }
            .btn-gradient-danger { background: var(--danger) !important; background-image: none !important; border-color: var(--danger) !important; color: #fff !important; }

            /* btn-light "Back" buttons */
            .btn-light, .btn.btn-light {
                background: var(--bg-surface) !important;
                border: 1px solid var(--border) !important;
                color: var(--text-secondary) !important;
            }

            .btn-light:hover {
                background: var(--bg-muted) !important;
                color: var(--text-primary) !important;
            }

            /* Action buttons in tables (Edit / Delete) */
            .table .btn-warning.btn-sm,
            .table .btn-danger.btn-sm,
            .table .btn-info.btn-sm {
                padding: 5px 12px !important;
                font-size: 12px !important;
                font-weight: 500 !important;
                border-radius: 6px !important;
                margin-right: 4px;
            }

            .table .btn-warning.btn-sm {
                background: var(--warning-soft) !important;
                border-color: rgba(245, 158, 11, 0.25) !important;
                color: #b45309 !important;
            }

            .table .btn-warning.btn-sm:hover {
                background: var(--warning) !important;
                border-color: var(--warning) !important;
                color: #fff !important;
            }

            .table .btn-danger.btn-sm {
                background: var(--danger-soft) !important;
                border-color: rgba(239, 68, 68, 0.25) !important;
                color: #b91c1c !important;
            }

            .table .btn-danger.btn-sm:hover {
                background: var(--danger) !important;
                border-color: var(--danger) !important;
                color: #fff !important;
            }

            /* Add button (top-right of index pages) — "Add Frame", "Add Category" */
            .card-body > .d-flex.border-bottom .btn-primary.btn-sm.btn-icon-text,
            .card-body > .d-flex.border-bottom .btn-primary {
                padding: 8px 14px !important;
                font-size: 13px !important;
                font-weight: 500 !important;
                border-radius: var(--radius-sm) !important;
                display: inline-flex !important;
                align-items: center;
                gap: 6px;
            }

            .card-body > .d-flex.border-bottom .btn-info.btn-sm {
                padding: 8px 14px !important;
                font-size: 13px !important;
                font-weight: 500 !important;
                border-radius: var(--radius-sm) !important;
                background: var(--bg-surface) !important;
                border: 1px solid var(--border) !important;
                color: var(--text-secondary) !important;
            }

            .card-body > .d-flex.border-bottom .btn-info.btn-sm:hover {
                background: var(--bg-muted) !important;
                color: var(--text-primary) !important;
            }

            /* DataTables wrapper labels */
            .dataTables_length label,
            .dataTables_filter label {
                color: var(--text-secondary) !important;
                font-size: 13px !important;
                font-weight: 500 !important;
            }

            .dataTables_length select,
            .dataTables_filter input {
                border-radius: var(--radius-sm) !important;
                border: 1px solid var(--border) !important;
                font-size: 13px !important;
                color: var(--text-primary);
                background-color: var(--bg-surface) !important;
            }

            .dataTables_info {
                color: var(--text-secondary) !important;
                font-size: 13px !important;
            }

            /* Form check / custom switch (legacy template patterns) */
            .form-check.form-check-flat {
                padding: 0;
                margin: 0;
                position: relative;
            }

            .form-check.form-check-flat .form-check-label {
                font-size: 13px !important;
                color: var(--text-primary) !important;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                min-height: 20px;
                position: relative;
                padding-left: 0 !important;
            }

            .form-check.form-check-flat .form-check-input {
                position: static !important;
                margin: 0 !important;
                width: 18px !important;
                height: 18px !important;
                cursor: pointer;
                accent-color: var(--accent);
                opacity: 1 !important;
                pointer-events: auto !important;
            }

            .form-check.form-check-flat .input-helper {
                display: none !important;
            }

            /* Custom switch (used on font index, etc.) */
            .custom-control.custom-switch {
                padding-left: 0;
                min-height: auto;
            }

            .custom-control.custom-switch .custom-control-input {
                position: absolute;
                width: 0;
                height: 0;
                opacity: 0;
            }

            .custom-control.custom-switch .custom-control-label {
                cursor: pointer;
                padding-left: 50px;
                font-size: 13px;
                color: var(--text-primary);
                line-height: 24px;
                user-select: none;
                position: relative;
            }

            .custom-control.custom-switch .custom-control-label::before {
                content: "";
                position: absolute;
                left: 0;
                top: 0;
                width: 40px;
                height: 22px;
                background: var(--border-strong);
                border: none;
                border-radius: 999px;
                transition: background .15s ease;
            }

            .custom-control.custom-switch .custom-control-label::after {
                content: "";
                position: absolute;
                left: 2px;
                top: 2px;
                width: 18px;
                height: 18px;
                background: #fff;
                border-radius: 50%;
                transition: transform .15s ease;
                box-shadow: 0 1px 3px rgba(0, 0, 0, .15);
            }

            .custom-control.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
                background: var(--accent);
            }

            .custom-control.custom-switch .custom-control-input:checked ~ .custom-control-label::after {
                transform: translateX(18px);
            }

            /* File upload (input-group with .file-upload-info) */
            .input-group .file-upload-info {
                background: var(--bg-muted) !important;
                border-color: var(--border) !important;
                color: var(--text-secondary) !important;
                font-size: 13px;
            }

            .input-group .file-upload-browse {
                border-radius: 0 var(--radius-sm) var(--radius-sm) 0 !important;
                font-size: 13px !important;
            }

            .input-group .form-control {
                border-radius: var(--radius-sm) 0 0 var(--radius-sm) !important;
            }

            /* Dropify (drag-drop image uploads) */
            .dropify-wrapper {
                border-radius: var(--radius-sm) !important;
                border: 2px dashed var(--border) !important;
                background: var(--bg-muted) !important;
                transition: border-color .15s ease, background .15s ease;
            }

            .dropify-wrapper:hover {
                background: #eef2ff !important;
                border-color: var(--accent) !important;
            }

            .dropify-message p {
                color: var(--text-secondary);
                font-size: 13px;
            }

            /* Image input rows in frame/sticker forms */
            .image-input-row {
                background: var(--bg-surface) !important;
                border: 1px solid var(--border) !important;
                border-radius: var(--radius-sm) !important;
                padding: 1rem !important;
                margin-bottom: 12px !important;
                box-shadow: var(--shadow-xs);
            }

            .image-input-row .drag-handle {
                color: var(--text-muted) !important;
            }

            .image-input-row .remove-btn {
                background: var(--danger-soft) !important;
                border-color: rgba(239, 68, 68, 0.25) !important;
                color: var(--danger) !important;
            }

            .image-input-row .remove-btn:hover {
                background: var(--danger) !important;
                color: #fff !important;
            }

            /* Sortable list group (order modals) */
            .modal-body .list-group-item {
                background: var(--bg-surface) !important;
                border: 1px solid var(--border) !important;
                border-radius: var(--radius-sm) !important;
                margin-bottom: 8px !important;
                padding: 12px 14px !important;
                transition: border-color .15s ease, box-shadow .15s ease;
            }

            .modal-body .list-group-item:hover {
                border-color: var(--accent);
                box-shadow: var(--shadow-xs);
            }

            .modal-body .list-group-item .badge.badge-secondary {
                background: var(--bg-muted) !important;
                color: var(--text-secondary) !important;
                border: 1px solid var(--border) !important;
                font-weight: 500 !important;
            }

            /* Image thumbnails in tables */
            .table img {
                border-radius: var(--radius-sm) !important;
            }

            /* Sub-controls on index pages: per_page select + search */
            .card-body .d-flex.flex-nowrap .form-control,
            .card-body .d-flex.justify-content-between .form-control-sm {
                height: 36px !important;
                border-radius: var(--radius-sm) !important;
            }

            /* Form-group label */
            .form-group {
                margin-bottom: 1.25rem;
            }

            .form-group > label,
            .form-group label:first-child {
                color: var(--text-secondary);
                font-size: 13px;
                font-weight: 500;
                margin-bottom: 6px;
                display: inline-block;
            }

            .text-danger {
                color: var(--danger) !important;
            }

            small.text-danger {
                font-size: 12px;
                margin-top: 4px;
                display: block;
            }

            /* Modal cleanup */
            .modal-header .btn-close,
            .modal-header .close {
                background: transparent;
                border: none;
                font-size: 1.4rem;
                color: var(--text-muted);
                opacity: 0.7;
                cursor: pointer;
                padding: 0 6px;
            }

            .modal-header .btn-close:hover,
            .modal-header .close:hover {
                color: var(--text-primary);
                opacity: 1;
            }

            /* .modal-footer */
            .modal-footer {
                border-top: 1px solid var(--border) !important;
                padding: 1rem 1.5rem !important;
            }

            /* Empty-state row in tables */
            .table tbody tr td.text-center[colspan] {
                color: var(--text-muted) !important;
                font-style: italic;
                padding: 2rem !important;
            }
        </style>
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
        <script src="{{ asset('adminpanel/dist/assets/vendors/chart.js/chart.umd.js') }}"></script>
        <script
            src="{{ asset('adminpanel/dist/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
        <script src="{{ asset('adminpanel/dist/assets/js/off-canvas.js') }}"></script>
        <script src="{{ asset('adminpanel/dist/assets/js/misc.js') }}"></script>
        <script src="{{ asset('adminpanel/dist/assets/js/settings.js') }}"></script>
        <script src="{{ asset('adminpanel/dist/assets/js/todolist.js') }}"></script>
        <script src="{{ asset('adminpanel/dist/assets/js/jquery.cookie.js') }}"></script>

        <!-- Dropify JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Dropify/0.2.2/js/dropify.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                                    Swal.fire(
                                        'Error!',
                                        'Something went wrong. Please try again.',
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
