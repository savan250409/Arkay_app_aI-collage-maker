<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UniqueNamer
{
    public static function uniqueName(string $table, string $column, string $name, $ignoreId = null): string
    {
        $exists = DB::table($table)
            ->where($column, $name)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if (!$exists) {
            return $name;
        }

        return $name . '_' . time();
    }

    public static function uniqueFile(string $directory, string $filename, array &$used = []): string
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $base = pathinfo($filename, PATHINFO_FILENAME);
        $dir  = rtrim($directory, '/\\');

        $candidate = $filename;
        $counter   = 0;

        while (File::exists($dir . DIRECTORY_SEPARATOR . $candidate) || in_array($candidate, $used)) {
            $counter++;
            $suffix    = time() . ($counter > 1 ? '_' . $counter : '');
            $candidate = $base . '_' . $suffix . ($ext !== '' ? '.' . $ext : '');
        }

        $used[] = $candidate;
        return $candidate;
    }
}
