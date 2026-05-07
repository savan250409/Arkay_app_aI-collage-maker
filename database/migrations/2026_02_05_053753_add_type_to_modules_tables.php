<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToModulesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fonts', function (Blueprint $table) {
            $table->string('type')->default('free')->after('id'); // free or pro
        });

        Schema::table('doodles', function (Blueprint $table) {
            $table->string('type')->default('free')->after('id'); // free or pro
        });

        Schema::table('filter_categories', function (Blueprint $table) {
            $table->string('type')->default('free')->after('id'); // free or pro
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fonts', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('doodles', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('filter_categories', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
