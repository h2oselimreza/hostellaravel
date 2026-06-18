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
        Schema::create('place_time_schedule', function (Blueprint $table) {
            $table->id();

            $table->string('place', 50);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->time('total_time')->nullable();

            $table->string('week_day', 30);
            $table->integer('weekend_status');

            $table->integer('is_active')->default(1);

            $table->string('created_by', 70);
            $table->string('updated_by', 70);
            
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');

            $table->index('place');
            $table->index('week_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('place_time_schedule');
    }
};
