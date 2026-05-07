<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPremiumToStickerCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sticker_categories', function (Blueprint $table) {
            $table->boolean('is_premium')->default(0)->after('image'); // 0 = Free, 1 = Pro
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sticker_categories', function (Blueprint $table) {
            $table->dropColumn('is_premium');
        });
    }
}
