<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SystemController extends Controller
{
    public function clearCacheAndLogs()
    {
        try {
            // Clear Laravel Cache
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');

            // Clear Log Files
            $logPath = storage_path('logs');
            if (File::exists($logPath)) {
                $files = File::files($logPath);
                foreach ($files as $file) {
                    File::put($file->getRealPath(), '');
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Cache and logs cleared successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
