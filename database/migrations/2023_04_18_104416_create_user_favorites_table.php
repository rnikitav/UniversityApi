<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')
                ->comment('ID пользователя');

            $table->unsignedInteger('subject_id')
                ->comment('Идентификатор данных');

            $table->string('subject_type', 255)
                ->comment('Имя класса для связи');

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_favorites');
    }
};
