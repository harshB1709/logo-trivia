<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_words', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained();
            $table->foreignId('word_id')->constrained();
            $table->unsignedInteger('score')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_words');
    }
};
