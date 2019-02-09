<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePicturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pictures', function (Blueprint $table) {
          $table->string('page');
          $table->string('context');
          $table->string('id');
          $table->string('img_path');
          $table->string('alt');
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
        Schema::dropIfExists('pictures');
    }
}


// CREATE TABLE `pictures` (
//   `page` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
//   `context` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
//   `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
//   `img_path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
//   `alt` varchar(255) COLLATE utf8_unicode_ci NOT NULL
// ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;