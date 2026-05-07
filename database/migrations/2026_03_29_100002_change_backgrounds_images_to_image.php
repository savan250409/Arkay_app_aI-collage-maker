<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBackgroundsImagesToImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('backgrounds', function (Blueprint $table) {
            $table->dropColumn('images');
        });
        Schema::table('backgrounds', function (Blueprint $table) {
            $table->string('image')->after('background_category_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('backgrounds', function (Blueprint $table) {
            $table->dropColumn('image');
        });
        Schema::table('backgrounds', function (Blueprint $table) {
            $table->longText('images')->after('background_category_id');
        });
    }
}
