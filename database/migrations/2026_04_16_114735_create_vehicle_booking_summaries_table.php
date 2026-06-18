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
        Schema::create('vehicle_booking_summary', function (Blueprint $table) {
            $table->id();
            $table->string('company', 50);
            $table->string('booking_no', 50);
            $table->string('person_name', 200);
            $table->string('person_emp_id', 50)->nullable();
            $table->string('department', 200)->nullable();
            $table->string('designation', 200)->nullable();
            $table->string('display_emp_code', 50)->nullable();

            $table->dateTime('from_dt_tm');
            $table->dateTime('to_dt_tm');
            $table->dateTime('from_dt_tm_confirmed')->nullable();
            $table->dateTime('to_dt_tm_confirmed')->nullable();

            $table->longText('route');

            $table->string('vehicle', 50)->nullable();
            $table->integer('participant')->default(1);

            $table->text('remarks')->nullable();
            $table->text('comment')->nullable();

            $table->integer('status')->default(3);
            $table->string('first_processing_by', 50)->default('fms');
            $table->string('forward_to', 50)->nullable();

            $table->tinyInteger('trip_status')->nullable();

            $table->string('created_by', 50);
            $table->string('created_type', 50);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 50);
            $table->string('updated_type', 50);
            $table->dateTime('updated_dt_tm');

            // Optional indexes (recommended)
            $table->index('company');
            $table->index('booking_no');
            $table->index('vehicle');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_booking_summary');
    }
};
