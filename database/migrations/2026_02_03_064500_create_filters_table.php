<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiltersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('filters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('filter_category_id');
            $table->string('name');
            $table->double('saturation')->default(1.0);
            $table->double('brightness')->default(0);
            $table->double('contrast')->default(1.0);
            $table->double('red')->default(1.0);
            $table->double('green')->default(1.0);
            $table->double('blue')->default(1.0);
            $table->timestamps();

            $table->foreign('filter_category_id')->references('id')->on('filter_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('filters');
    }
}
