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
        Schema::create('fuel', function (Blueprint $table) {
            $table->id();
            $table->string('fuel_code', 50)->unique();
            $table->string('fuel_name', 70);
            $table->decimal('fuel_rate', 10, 2)->default(0.00);
            
            $table->string('created_by', 70)->nullable();
            $table->string('updated_by', 70)->nullable();
            
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel');
    }
};
