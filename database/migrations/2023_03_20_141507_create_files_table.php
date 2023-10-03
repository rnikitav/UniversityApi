<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('file_id')
                ->comment('Идентификатор данных');

            $table->string('file_type', 255)
                ->comment('Имя класса для связи');

            $table->string('disk', 255)
                ->comment('Диск хранения');

            $table->string('category', 255)
                ->comment('Категория файла');

            $table->string('path',255)
                ->comment('Путь к файлу');

            $table->string('original_name',255)
                ->comment('Оригинальное имя');

            $table->string('sha256', 64)
                ->comment('Хэш sha256');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
};
