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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();

            $table->string('company', 50);

            $table->string('parent_category', 50);

            $table->text('parent_category_str');

            $table->string('category_name', 100);

            $table->string('category_code', 50);

            $table->string('category_type', 20);

            $table->integer('is_active')->default(1);

            $table->string('created_by', 50);

            $table->string('updated_by', 50);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');

            // Optional indexes for better performance
            $table->index('company');
            $table->index('category_type');
            $table->index('is_active');
            $table->index('category_code');;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_categories');
    }
};
