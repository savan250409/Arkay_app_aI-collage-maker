<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeCountryToJsonInPhotoshootsTable extends Migration
{
    /**
     * Store the selected countries as a JSON array inside a single record
     * (e.g. ["ph","in"]) instead of one row per country.
     *
     * @return void
     */
    public function up()
    {
        // Keep an index that starts with photoshoot_category_id so the foreign
        // key still has supporting index once the composite index is dropped.
        Schema::table('photoshoots', function (Blueprint $table) {
            $table->index('photoshoot_category_id', 'photoshoots_category_idx');
        });

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->dropIndex('photoshoots_cat_country_idx');
            $table->dropIndex('photoshoots_country_index');
        });

        // Widen the column first so the JSON string fits before the type change.
        Schema::table('photoshoots', function (Blueprint $table) {
            $table->string('country', 255)->nullable()->change();
        });

        // Convert existing scalar codes to a JSON array; blanks become NULL.
        DB::statement('UPDATE photoshoots SET country = CONCAT(\'["\', country, \'"]\') WHERE country IS NOT NULL AND country <> \'\'');
        DB::statement('UPDATE photoshoots SET country = NULL WHERE country = \'\'');

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->json('country')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Collapse the array back to the first country code.
        DB::statement('UPDATE photoshoots SET country = JSON_UNQUOTE(JSON_EXTRACT(country, \'$[0]\')) WHERE country IS NOT NULL');

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->string('country', 5)->nullable()->change();
        });

        Schema::table('photoshoots', function (Blueprint $table) {
            $table->index(['photoshoot_category_id', 'country'], 'photoshoots_cat_country_idx');
            $table->index('country', 'photoshoots_country_index');
            $table->dropIndex('photoshoots_category_idx');
        });
    }
}
