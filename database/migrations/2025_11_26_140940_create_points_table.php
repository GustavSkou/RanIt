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
        Schema::create('points', function (Blueprint $table) {
            $table->id();
            $table->float("latitude");
            $table->float("longitude");
            $table->float("elevation");
            $table->integer('heart_rate')->nullable();
            $table->integer('cadence')->nullable();
            $table->integer('power')->nullable();
            $table->timestamp("timestamp");
            $table->foreignId("activity_id")->constrained("activities");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points');
    }
};
