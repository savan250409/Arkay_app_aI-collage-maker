<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSortOrderToPhotoshootCategoriesTable extends Migration
{
    /**
     * Manual display order for photoshoot categories. Lower `sort_order` shows
     * first (in the admin list and the API). Backfilled so the current
     * newest-first order (id DESC) is preserved: the highest id gets 0.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photoshoot_categories', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_active');
        });

        $position = 0;
        foreach (DB::table('photoshoot_categories')->orderBy('id', 'desc')->pluck('id') as $id) {
            DB::table('photoshoot_categories')->where('id', $id)->update(['sort_order' => $position]);
            $position++;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photoshoot_categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
}
