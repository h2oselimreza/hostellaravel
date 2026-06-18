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
        Schema::create('vehicle_assign_details', function (Blueprint $table) {
            $table->id();

            $table->string('reference_no', 30)->index();
            $table->string('booking_no', 50)->nullable();

            $table->text('route_lat_long')->nullable();
            $table->string('map_image', 100)->nullable();

            $table->string('company', 50);
            $table->string('vehicle', 50);

            $table->string('driver', 50)->nullable();

            $table->dateTime('assign_dt_tm');

            $table->string('assign_type', 20);

            $table->string('emp_name', 250)->nullable();
            $table->string('emp_designation', 200)->nullable();
            $table->string('emp_department', 100)->nullable();
            $table->string('emp_id_no', 100)->nullable();

            $table->text('route')->nullable();

            $table->string('current_location', 200)->nullable();

            $table->text('remarks')->nullable();

            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_assign_details');
    }
};
