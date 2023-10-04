<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tag_image_collections', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tag_id')
                ->comment('Идентификатор тэга');

            $table->unsignedBigInteger('image_collection_id')
                ->comment('Идентификатор коллекции изображений');

            $table->timestamps();

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags')
                ->onDelete('cascade');

            $table->foreign('image_collection_id')
                ->references('id')
                ->on('tags');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tag_image_collections');
    }
};
