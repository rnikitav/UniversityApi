<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_tags', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('accelerator_id')
                ->comment('Идентификатор акселератора');

            $table->unsignedBigInteger('tag_id')
                ->comment('Идентификатор тэга');

            $table->timestamps();

            $table->foreign('accelerator_id')
                ->references('id')
                ->on('accelerators')
                ->onDelete('cascade');

            $table->foreign('tag_id')
                ->references('id')
                ->on('tags');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_tags');
    }
};
