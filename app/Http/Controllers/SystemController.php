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
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');

            // Release any open Monolog handlers so the log files are no longer
            // locked by this process before we try to truncate them on Windows.
            $logger = \Illuminate\Support\Facades\Log::getLogger();
            if (method_exists($logger, 'getHandlers')) {
                foreach ($logger->getHandlers() as $handler) {
                    if (method_exists($handler, 'close')) {
                        $handler->close();
                    }
                }
            }

            $logPath = storage_path('logs');
            $skipped = [];
            if (File::exists($logPath)) {
                foreach (File::files($logPath) as $file) {
                    $path = $file->getRealPath();
                    if (basename($path) === '.gitignore') {
                        continue;
                    }

                    // 1) Try simple truncate via fopen('w'). On Windows this
                    //    fails if the file is still open elsewhere.
                    $fp = @fopen($path, 'w');
                    if ($fp !== false) {
                        fclose($fp);
                        continue;
                    }

                    // 2) Fall back to delete (Windows allows DELETE share even
                    //    when another handle is open, then recreate empty).
                    if (@unlink($path)) {
                        @file_put_contents($path, '');
                        continue;
                    }

                    $skipped[] = basename($path);
                }
            }

            $message = 'Cache and logs cleared successfully.';
            if (!empty($skipped)) {
                $message .= ' (skipped locked files: ' . implode(', ', $skipped) . ')';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
