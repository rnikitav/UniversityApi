<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_case_scores', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('case_id')
                ->comment('Идентификатор кейса');

            $table->unsignedBigInteger('user_id')
                ->comment('Идентификатор пользователя');

            $table->unsignedTinyInteger('score')
                ->comment('Балл за выполнение');

            $table->timestamps();

            $table->foreign('case_id')
                ->references('id')
                ->on('accelerator_cases')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_case_scores');
    }
};
