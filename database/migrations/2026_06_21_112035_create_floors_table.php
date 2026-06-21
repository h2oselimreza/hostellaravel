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
        Schema::create('hst_floor', function (Blueprint $table) {
            $table->id();

            $table->string('floor_code', 50);
            $table->string('building', 50)->index();
            $table->string('title', 250)->index();
            $table->string('floor_image', 100)->nullable();

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
        Schema::dropIfExists('hst_floor');
    }
};
