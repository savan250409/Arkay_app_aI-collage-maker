<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFramesColumnsToJson extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('frames', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_input_count']);
            $table->longText('images')->nullable()->after('frame_category_id'); // Stores JSON array of images
            $table->longText('image_input_counts')->nullable()->after('images'); // Stores JSON array of counts
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('frames', function (Blueprint $table) {
            //
        });
    }
}
