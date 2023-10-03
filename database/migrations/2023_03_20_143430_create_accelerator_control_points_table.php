<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_control_points', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('accelerator_id')
                ->comment('Идентификатор акселератора');

            $table->string('name', 255)
                ->comment('Наименование');

            $table->timestamp('date_completion')
                ->comment('Дата выполнения');

            $table->unsignedTinyInteger('max_score')
                ->comment('Максимальный балл за выполнение');

            $table->timestamps();

            $table->foreign('accelerator_id')
                ->references('id')
                ->on('accelerators')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_control_points');
    }
};
