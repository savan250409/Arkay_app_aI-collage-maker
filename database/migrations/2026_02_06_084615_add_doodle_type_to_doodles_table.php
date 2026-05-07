<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDoodleTypeToDoodlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doodles', function (Blueprint $table) {
            $table->string('doodle_type')->default('image')->after('name'); // 'line' or 'image'
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doodles', function (Blueprint $table) {
            $table->dropColumn('doodle_type');
        });
    }
}
