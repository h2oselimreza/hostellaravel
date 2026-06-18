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
        Schema::create('customer_feedback', function (Blueprint $table) {
            $table->id();
            $table->string('feedback_code', 50)->unique();
            $table->string('call_type', 20);
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->integer('feedback_order')->default(1);
            $table->integer('is_active')->default(1);
            
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            
            $table->datetime('created_dt_tm')->nullable();
            $table->datetime('updated_dt_tm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_feedback');
    }
};
