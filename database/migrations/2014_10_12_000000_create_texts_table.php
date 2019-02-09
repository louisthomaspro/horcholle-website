<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('texts', function (Blueprint $table) {
          $table->string('page');
          $table->string('context');
          $table->string('id');
          $table->text('value');
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
        Schema::dropIfExists('texts');
    }
}



// CREATE TABLE `texts` (
//   `page` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
//   `context` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
//   `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
//   `value` text COLLATE utf8mb4_unicode_ci NOT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;