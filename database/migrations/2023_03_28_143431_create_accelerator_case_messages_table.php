<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_case_messages', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('owner_id')
                ->comment('Идентификатор данных');

            $table->string('owner_type', 255)
                ->comment('Имя класса для связи');

            $table->unsignedBigInteger('user_id')
                ->comment('Идентификатор пользователя');

            $table->text('message')
                ->comment('Сообщение');

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_case_messages');
    }
};
