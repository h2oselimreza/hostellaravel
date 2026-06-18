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
        Schema::create('home_service_app_detail_gen', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_no', 30);
            $table->string('service_variant', 30);

            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('quantity', 10, 2)->default(0.00);
            $table->decimal('total_amount', 10, 2)->default(0.00);

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
        Schema::dropIfExists('home_service_app_detail_gen');
    }
};
