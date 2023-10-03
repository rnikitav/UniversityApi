<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_case_events', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('case_id')
                ->comment('Идентификатор кейса');

            $table->unsignedBigInteger('initializer_id')
                ->comment('Идентификатор пользователя инициализатора');

            $table->string('type_id')
                ->comment('Тип события');

            $table->text('description')
                ->comment('Описание события');

            $table->unsignedBigInteger('participant_id')
                ->comment('Идентификатор пользователя участника кейса');

            $table->string('status_id')
                ->comment('Статус события');

            $table->unsignedBigInteger('moderator_id')
                ->nullable()
                ->comment('Идентификатор пользователя модератора события');

            $table->timestamps();

            $table->foreign('case_id')
                ->references('id')
                ->on('accelerator_cases')
                ->onDelete('cascade');

            $table->foreign('initializer_id')
                ->references('id')
                ->on('users');

            $table->foreign('type_id')
                ->references('id')
                ->on('accelerator_case_event_types');

            $table->foreign('status_id')
                ->references('id')
                ->on('accelerator_case_event_statuses');

            $table->foreign('participant_id')
                ->references('id')
                ->on('users');

            $table->foreign('moderator_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_case_events');
    }
};
