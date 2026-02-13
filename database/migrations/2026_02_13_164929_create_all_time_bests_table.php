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
        Schema::create('all_time_bests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('activity_id')->constrained('activities');
            $table->foreignId('sport_id')->constrained('sports');
            $table->integer('distance');        // distance in meters
            $table->integer('duration');        // duration in seconds
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_time_bests');
    }
};
