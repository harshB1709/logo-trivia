<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SQLite cannot alter an existing foreign key. The original constraint
        // remains valid for local SQLite installations.
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreign('player_id')
                ->references('id')
                ->on('players')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['player_id']);
            $table->foreign('player_id')
                ->references('id')
                ->on('players');
        });
    }
};
