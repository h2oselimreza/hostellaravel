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
        Schema::create('product_variants', function (Blueprint $table) {
             $table->id();

            $table->string('company', 50);

            $table->string('product', 50);

            $table->string('variant_name', 200)
                  ->default('Default');

            $table->string('variant_code', 50);

            $table->string('unit_name', 100)
                  ->nullable();

            $table->string('model', 100)
                  ->nullable();

            $table->string('display_code', 100)
                  ->nullable();

            $table->text('details')
                  ->nullable();

            $table->string('variant_type', 20);

            $table->integer('default_variant');

            $table->integer('is_active')
                  ->default(1);

            $table->string('created_by', 50);

            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 50);

            $table->dateTime('updated_dt_tm');

            // Indexes
            $table->index('variant_code');
            $table->index('product');
            $table->index('company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
