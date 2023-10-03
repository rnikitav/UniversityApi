<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_cases', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('accelerator_id')
                ->comment('Идентификатор акселератора');

            $table->string('name', 255)
                ->comment('Наименование');

            $table->text('description')
                ->comment('Описание');

            $table->string('status_id')
                ->comment('Статус кейса');

            $table->string('participation_id')
                ->comment('Участие');

            $table->timestamps();

            $table->foreign('accelerator_id')
                ->references('id')
                ->on('accelerators')
                ->onDelete('cascade');

            $table->foreign('status_id')
                ->references('id')
                ->on('accelerator_case_statuses');

            $table->foreign('participation_id')
                ->references('id')
                ->on('accelerator_case_participations');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_cases');
    }
};
