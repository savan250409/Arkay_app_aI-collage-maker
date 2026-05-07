<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenamePriorityToRowOrderInStickerCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sticker_categories', function (Blueprint $table) {
            $table->renameColumn('priority', 'row_order');
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
            $table->renameColumn('row_order', 'priority');
        });
    }
}
