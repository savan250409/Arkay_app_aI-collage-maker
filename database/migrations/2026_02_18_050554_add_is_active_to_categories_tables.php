<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsActiveToCategoriesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('frame_categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->after('image');
        });

        Schema::table('sticker_categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->after('is_premium');
        });

        Schema::table('filter_categories', function (Blueprint $table) {
            $table->boolean('is_active')->default(1)->after('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frame_categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('sticker_categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('filter_categories', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
