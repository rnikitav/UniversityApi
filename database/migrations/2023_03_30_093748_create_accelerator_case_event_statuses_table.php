<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('accelerator_case_event_statuses', function (Blueprint $table) {
            $table->string('id', 20)
                ->unique()
                ->comment('Идентификатор');

            $table->string('name', 255)
                ->comment('Наименование');

            $table->boolean('active')
                ->default(true)
                ->comment('Активность');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('accelerator_case_event_statuses');
    }
};
