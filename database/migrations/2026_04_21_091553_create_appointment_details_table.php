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
        Schema::create('appointment_detail', function (Blueprint $table) {
            $table->id();

            $table->string('appointment_no', 30)->index();
            $table->string('service_variant', 30)->index();
            $table->string('vehicle', 50)->index();

            $table->string('created_by', 70);
            $table->string('created_type', 20);

            $table->string('updated_by', 70);
            $table->string('updated_type', 20);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_detail');
    }
};
