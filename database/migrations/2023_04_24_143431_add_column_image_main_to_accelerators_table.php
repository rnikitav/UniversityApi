<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('accelerators', function (Blueprint $table) {
            $table->unsignedBigInteger('image_main_id')
                ->nullable()
                ->comment('ID файла для основного изображения');

            $table->foreign('image_main_id')
                ->references('id')
                ->on('files');
        });
    }

    public function down()
    {
        Schema::table('accelerators', function (Blueprint $table) {
            $table->dropForeign('image_main_id');
            $table->dropColumn('image_main_id');
        });
    }
};
