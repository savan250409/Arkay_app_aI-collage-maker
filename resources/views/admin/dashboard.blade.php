@extends('layouts.admin')

@section('content')
    <div class="page-header">
        <div>
            <h3 class="page-title">
                <span class="page-title-icon">
                    <i class="mdi mdi-view-dashboard-outline"></i>
                </span>
                Dashboard
            </h3>
            <p class="text-muted mb-0" style="margin-top: 6px; margin-left: 52px; font-size: 13px;">
                Welcome back, here's an overview of your content library.
            </p>
        </div>
        <nav aria-label="breadcrumb">
            <ul class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">
                    Overview
                </li>
            </ul>
        </nav>
    </div>

    <div class="row">
        <div class="col-md-6 col-xl-3 grid-margin">
            <a href="{{ route('frames.index') }}" class="stat-card">
                <div class="stat-icon icon-indigo">
                    <i class="mdi mdi-image-multiple-outline"></i>
                </div>
                <p class="stat-label">Total Frames</p>
                <h2 class="stat-value">{{ $totalFrames }}</h2>
                <span class="stat-link">View all <i class="mdi mdi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-md-6 col-xl-3 grid-margin">
            <a href="{{ route('fonts.index') }}" class="stat-card">
                <div class="stat-icon icon-emerald">
                    <i class="mdi mdi-format-font"></i>
                </div>
                <p class="stat-label">Total Fonts</p>
                <h2 class="stat-value">{{ $totalFonts }}</h2>
                <span class="stat-link">View all <i class="mdi mdi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-md-6 col-xl-3 grid-margin">
            <a href="{{ route('stickers.index') }}" class="stat-card">
                <div class="stat-icon icon-sky">
                    <i class="mdi mdi-sticker-emoji"></i>
                </div>
                <p class="stat-label">Total Stickers</p>
                <h2 class="stat-value">{{ $totalStickers }}</h2>
                <span class="stat-link">View all <i class="mdi mdi-arrow-right"></i></span>
            </a>
        </div>

        <div class="col-md-6 col-xl-3 grid-margin">
            <a href="{{ route('doodles.index') }}" class="stat-card">
                <div class="stat-icon icon-violet">
                    <i class="mdi mdi-creation"></i>
                </div>
                <p class="stat-label">Total Doodles</p>
                <h2 class="stat-value">{{ $totalDoodles }}</h2>
                <span class="stat-link">View all <i class="mdi mdi-arrow-right"></i></span>
            </a>
        </div>
    </div>

    <div class="row fill-row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h4 style="font-weight: 600; margin-bottom: 4px; color: var(--text-primary); font-size: 1.05rem;">
                                Quick actions
                            </h4>
                            <p class="text-muted mb-0" style="font-size: 13px;">Jump straight to a module</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="{{ route('backgrounds.index') }}" class="stat-card" style="padding: 1.1rem; display: flex; align-items: center; gap: 12px;">
                                <div class="stat-icon icon-rose" style="width: 40px; height: 40px; font-size: 18px; margin-bottom: 0;">
                                    <i class="mdi mdi-image-outline"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 14px; color: var(--text-primary);">Backgrounds</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">Manage backgrounds</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="{{ route('filters.index') }}" class="stat-card" style="padding: 1.1rem; display: flex; align-items: center; gap: 12px;">
                                <div class="stat-icon icon-indigo" style="width: 40px; height: 40px; font-size: 18px; margin-bottom: 0;">
                                    <i class="mdi mdi-filter-variant"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 14px; color: var(--text-primary);">Filters</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">Photo filters</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-4 col-lg-3 mb-3">
                            <a href="{{ route('fonts.index') }}" class="stat-card" style="padding: 1.1rem; display: flex; align-items: center; gap: 12px;">
                                <div class="stat-icon icon-emerald" style="width: 40px; height: 40px; font-size: 18px; margin-bottom: 0;">
                                    <i class="mdi mdi-format-font"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 14px; color: var(--text-primary);">Fonts</div>
                                    <div style="font-size: 12px; color: var(--text-muted);">Manage fonts</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
