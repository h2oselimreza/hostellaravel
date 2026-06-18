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
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->integer('division'); // FK (as per your structure)
            $table->string('district_en_name', 100);
            $table->string('district_bn_name', 100);
            $table->string('latitude', 50);
            $table->string('longitude', 50);
            $table->string('website', 300);
            $table->integer('is_active')->default(1);
            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');
            $table->string('updated_by', 70);
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
