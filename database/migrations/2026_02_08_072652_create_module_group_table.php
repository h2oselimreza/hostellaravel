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
        Schema::create('module_group', function (Blueprint $table) {
            $table->id();
            $table->string('module_group_name', 250);
            $table->string('module_group_code', 20)->unique(); // codes are usually unique
            $table->integer('module_group_order')->default(0)->nullable();
            $table->string('panel_type', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_group');
    }
};
