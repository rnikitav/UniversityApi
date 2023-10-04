<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('image_collection', function (Blueprint $table) {
            $table->id();

            $table->string('name', 255)
                ->comment('Наименование');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('image_collection');
    }
};
