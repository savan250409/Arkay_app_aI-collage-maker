<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBackgroundsTableToImagesArray extends Migration
{
    public function up()
    {
        // Truncate old rows before adding unique constraint (one row per category)
        \DB::table('backgrounds')->truncate();

        Schema::table('backgrounds', function (Blueprint $table) {
            $table->unique('background_category_id');
        });
    }

    public function down()
    {
        Schema::table('backgrounds', function (Blueprint $table) {
            $table->dropUnique(['background_category_id']);
        });
    }
}
