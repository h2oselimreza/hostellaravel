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
        Schema::create('products', function (Blueprint $table) {
             $table->id();

            $table->string('company', 50);

            $table->string('product_name', 200);

            $table->string('product_code', 50);

            $table->string('category', 50);

            $table->text('product_details')->nullable();

            $table->string('product_type', 20);

            $table->integer('is_active')->default(1);

            $table->string('created_by', 70);
            $table->string('updated_by', 70);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');

            // indexes (based on your schema hints)
            $table->index('product_code');
            $table->index('category');
            $table->index('company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
