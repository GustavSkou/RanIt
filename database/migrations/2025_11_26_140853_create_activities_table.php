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
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string("name");
            $table->string('type')->nullable();
            $table->string("device")->nullable();
            $table->float("distance")->nullable();
            $table->timestamp('start_time')->nullable();
            $table->integer('duration')->nullable();        // duration in seconds
            $table->float("average_speed")->nullable();
            $table->float("average_heart_rate")->nullable();
            $table->foreignId("user_id")->constrained("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
