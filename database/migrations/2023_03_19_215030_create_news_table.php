<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('body')->comment('Body news HTML');
            $table->string('slug', 200);
            $table->string('img_preview');
            $table->string('img');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
