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
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('parent_category', 50);
            $table->text('parent_category_str');
            $table->string('category_name', 100);
            $table->string('category_code', 50)->unique();
            $table->string('category_type', 100);
            $table->integer('is_active')->default(1);
            
            // Custom Audit Columns
            $table->string('created_by', 70);
            $table->string('updated_by', 70)->nullable();
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
