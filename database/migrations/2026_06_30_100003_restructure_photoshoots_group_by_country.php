<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RestructurePhotoshootsGroupByCountry extends Migration
{
    /**
     * Group photoshoots by country: one row per (category, country) holding a
     * JSON `items` array. country = '' means the global row (no country).
     * A category therefore has at most 5 country rows + 1 global row = 6 rows.
     *
     * @return void
     */
    public function up()
    {
        // Clear the previous per-item rows; they are rebuilt under the new shape.
        DB::table('photoshoots')->delete();

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->dropColumn(['name', 'hashtag', 'image']);
        });

        Schema::table('photoshoots', function (Blueprint $table) {
            // Single country code per row; '' = global (no country).
            $table->string('country', 5)->default('')->nullable(false)->change();
            $table->longText('items')->nullable()->after('country');
        });

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->unique(['photoshoot_category_id', 'country'], 'photoshoots_cat_country_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('photoshoots')->delete();

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->dropUnique('photoshoots_cat_country_unique');
            $table->dropColumn('items');
            $table->string('name')->after('photoshoot_category_id')->default('');
            $table->string('hashtag')->after('name')->default('');
            $table->string('image')->after('hashtag')->default('');
        });

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->string('country', 5)->nullable()->default(null)->change();
        });
    }
}
