<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_main_data', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('ID пользователя');
            $table->string('first_name', 255)->nullable()->comment('Имя');
            $table->string('last_name', 255)->nullable()->comment('Фамилия');
            $table->string('patronymic', 255)->nullable()->comment('Отчество');
            $table->string('email', 255)->nullable()->comment('Email');
            $table->text('education')->nullable()->comment('Укажите образования');
            $table->text('position_work')->nullable()->comment('Должность работы');
            $table->text('place_work')->nullable()->comment('Место работы');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down(): void {
        Schema::dropIfExists('user_main_data');
    }
};
