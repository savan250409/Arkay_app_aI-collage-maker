<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class DropOverlayAndDynamicPhotoFrameTables extends Migration
{
    public function up()
    {
        Schema::dropIfExists('overlay_frames');
        Schema::dropIfExists('overlay_frame_categories');
        Schema::dropIfExists('dynamic_photo_frames');
        Schema::dropIfExists('dynamic_photo_frame_categories');
    }

    public function down()
    {
        // Intentionally empty: the original create migrations have been removed
        // along with their controllers/models/views, so there is nothing meaningful
        // to recreate here.
    }
}
