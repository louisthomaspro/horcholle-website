<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRealisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realisations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->string('path');
            $table->string('filename');
            $table->string('extension');
            $table->string('timestamp');
            $table->string('mimetype');
            $table->integer('size');
            $table->string('dirname');
            $table->string('basename');
            $table->integer('hierarchy');
            $table->integer('parent_id');
            $table->mediumText('comment');
            $table->string('img_path');
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
        Schema::dropIfExists('realisations');
    }
}


// CREATE TABLE `realisations` (
//   `id` int(11) NOT NULL,
//   `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `extension` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `timestamp` int(11) DEFAULT NULL,
//   `mimetype` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `size` int(11) DEFAULT NULL,
//   `dirname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `basename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
//   `hierarchy` int(11) DEFAULT NULL COMMENT '1=category, 2=album, 3=picture',
//   `parent_id` int(11) DEFAULT NULL,
//   `comment` mediumtext COLLATE utf8mb4_unicode_ci,
//   `img_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;