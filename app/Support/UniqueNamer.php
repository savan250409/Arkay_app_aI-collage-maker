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

    public static function uniqueFile(string $directory, string $filename): string
    {
        $target = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $filename;

        if (!File::exists($target)) {
            return $filename;
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $base = pathinfo($filename, PATHINFO_FILENAME);

        return $base . '_' . time() . ($ext !== '' ? '.' . $ext : '');
    }
}
