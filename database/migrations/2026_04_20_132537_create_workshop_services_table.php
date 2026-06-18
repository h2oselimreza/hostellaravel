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
        Schema::create('workshop_service', function (Blueprint $table) {
            $table->id();
            $table->string('workshop', 50);
            $table->string('service_variant', 50);

            $table->integer('is_active')->default(1);
            $table->index('workshop');
            $table->index('service_variant');
            $table->index('is_active');

            $table->string('created_by', 70);
            $table->string('updated_by', 70);
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_service');
    }
};
