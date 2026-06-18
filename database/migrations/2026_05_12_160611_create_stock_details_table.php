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
        Schema::create('stock_details', function (Blueprint $table) {

            $table->id();

            $table->string('stock_detail_id', 30)->index();

            $table->string('stock_summary_id', 50);

            $table->string('company', 50);

            $table->string('vehicle', 50)->nullable();

            $table->string('variant', 50);

            $table->string('remarks', 300)->nullable();

            $table->decimal('credit_quantity', 10, 2)->default(0.00);

            $table->decimal('debit_quantity', 10, 2)->default(0.00);

            $table->string('trasaction_type', 10);

            $table->integer('status')->default(1);

            $table->string('created_by', 50);
            $table->string('updated_by', 59);
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_details');
    }
};
