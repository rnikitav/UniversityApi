<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerators', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255)
                ->comment('Наименование');

            $table->text('description')
                ->nullable()
                ->comment('Описание');

            $table->timestamp('published_at')
                ->nullable()
                ->comment('Дата публикации');

            $table->timestamp('date_end_accepting')
                ->comment('Дата окончания приема заявок на размещение кейсов');

            $table->timestamp('date_end')
                ->comment('Дата окончания работы акселератора');

            $table->string('status_id')
                ->comment('Статус акселератора');

            $table->unsignedBigInteger('user_id')
                ->comment('Владелец акселератора');

            $table->timestamps();

            $table->foreign('status_id')
                ->references('id')
                ->on('accelerator_statuses');

            $table->foreign('user_id')
                ->references('id')
                ->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerators');
    }
};
