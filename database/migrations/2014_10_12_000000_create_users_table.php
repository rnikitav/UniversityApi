<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('login', 255)
                ->unique()
                ->comment('Логин');

            $table->string('password', 255)
                ->nullable()
                ->comment('Пароль');

            $table->boolean('external')
                ->default(false)
                ->comment('Внешний пользователь');

            $table->rememberToken();

            $table->string('confirm_token', 255)
                ->nullable()
                ->comment('Токен для подтверждения пользователя');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
