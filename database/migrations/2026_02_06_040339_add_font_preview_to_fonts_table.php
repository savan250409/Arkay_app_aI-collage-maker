<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFontPreviewToFontsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fonts', function (Blueprint $table) {
            if (!Schema::hasColumn('fonts', 'font_preview')) {
                $table->string('font_preview')->nullable()->after('file');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fonts', function (Blueprint $table) {
            $table->dropColumn('font_preview');
        });
    }
}
