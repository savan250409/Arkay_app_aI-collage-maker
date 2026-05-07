<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBackgroundCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('background_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image');
            $table->boolean('is_premium')->default(0);
            $table->boolean('is_active')->default(1);
            $table->integer('row_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('background_categories');
    }
}
