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
        Schema::create('service_variants', function (Blueprint $table) {
            $table->id();
            $table->string('service', 50)->comment('Points to service category code');
            $table->string('service_variant_name', 200);
            $table->string('variant_code', 50)->unique();
            $table->string('variant_type', 100);
            $table->integer('default_variant')->default(0)->comment('1 for default, 0 for not');
            $table->string('unit_name', 50)->nullable();
            $table->decimal('unit_price', 10, 2)->default(0.00);
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
        Schema::dropIfExists('service_variants');
    }
};
