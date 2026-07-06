<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotoshootsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Each photoshoot is stored as an individual row (not a JSON array) so a
     * single category can hold ~500+ records without bloating one column.
     * Indexes on the category id and country keep the paginated, country
     * filtered API queries fast.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photoshoots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('photoshoot_category_id');
            $table->string('name');
            $table->string('hashtag');
            $table->string('image');
            $table->string('country', 5)->nullable();
            $table->timestamps();

            $table->foreign('photoshoot_category_id')
                ->references('id')
                ->on('photoshoot_categories')
                ->onDelete('cascade');

            // Composite index for "by category + country" lookups, plus a plain
            // country index for the country-first ordering inside a category.
            $table->index(['photoshoot_category_id', 'country'], 'photoshoots_cat_country_idx');
            $table->index('country');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photoshoots');
    }
}
