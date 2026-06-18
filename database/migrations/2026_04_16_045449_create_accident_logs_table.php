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
        Schema::create('accidental_log', function (Blueprint $table) {
            $table->id();

            $table->string('vehicle', 50);
            $table->string('driver', 50);
            $table->dateTime('accident_date_time')->index();
            $table->string('place', 200);
            $table->string('vehicle_affected_area', 200);
            $table->string('company', 50)->index();

            $table->text('remarks')->nullable();
            $table->text('file_name')->nullable();
            $table->text('file_original_name')->nullable();

            $table->string('created_by', 50);
            $table->string('updated_by', 50);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accidental_log');
    }
};
