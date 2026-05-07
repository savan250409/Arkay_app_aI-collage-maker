<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FrameCategory;
use App\Models\Frame;
use App\Models\StickerCategory;
use App\Models\Sticker;
use App\Models\Font;
use App\Models\Doodle;

class DashboardController extends Controller
{
    public function index()
    {
        $totalFrameCategories = FrameCategory::count();
        $totalFrames = Frame::count();
        $totalStickerCategories = StickerCategory::count();
        $totalStickers = Sticker::count();
        $totalFonts = Font::count();
        $totalDoodles = Doodle::count();

        return view('admin.dashboard', compact(
            'totalFrameCategories',
            'totalFrames',
            'totalStickerCategories',
            'totalStickers',
            'totalFonts',
            'totalDoodles'
        ));
    }
}
