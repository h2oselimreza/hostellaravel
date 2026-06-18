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
        Schema::create('common_table', function (Blueprint $table) {
            $table->id();
            $table->string('element', 300);
            $table->string('element_code', 300)->index();
            $table->string('depend_on_element', 300);
            $table->string('type', 200);
            $table->boolean('is_active')->default(1);
            $table->integer('element_order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('common_table');
    }
};
