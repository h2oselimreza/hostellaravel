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
         Schema::create('hst_building', function (Blueprint $table) {
            $table->id();

            $table->string('building_code', 50);
            $table->string('title', 250)->index();
            $table->text('address')->nullable();
            $table->string('building_image', 100)->nullable();

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
        Schema::dropIfExists('hst_building');
    }
};
