<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_case_solutions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('case_id')
                ->comment('Идентификатор кейса');

            $table->unsignedBigInteger('control_point_id')
                ->comment('Идентификатор контрольной точки');

            $table->unsignedBigInteger('author_id')
                ->comment('Идентификатор пользователя автора решения');

            $table->text('description')
                ->comment('Описание решения');

            $table->string('status_id')
                ->comment('Статус решения');

            $table->unsignedTinyInteger('score')
                ->nullable()
                ->comment('Балл за выполнение');

            $table->timestamps();

            $table->foreign('case_id')
                ->references('id')
                ->on('accelerator_cases')
                ->onDelete('cascade');

            $table->foreign('control_point_id')
                ->references('id')
                ->on('accelerator_control_points');

            $table->foreign('author_id')
                ->references('id')
                ->on('users');

            $table->foreign('status_id')
                ->references('id')
                ->on('accelerator_case_solution_statuses');
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_case_solutions');
    }
};
