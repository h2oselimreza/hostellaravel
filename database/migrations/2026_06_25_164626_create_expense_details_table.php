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
        Schema::create('expense_detail', function (Blueprint $table) {
            $table->id();

            $table->string('expense_no', 30)->index();

            $table->string('expense_head', 50);

            $table->decimal('quantity', 10, 2);

            $table->string('unit_name', 50);

            $table->decimal('unit_price', 10, 2);

            $table->decimal('adjust', 10, 2)->default(0.00);

            $table->decimal('amount', 10, 2);

            $table->string('remarks', 200)->nullable();

            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 70);
            $table->dateTime('updated_dt_tm');

            // Recommended indexes
            $table->index('expense_head');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_detail');
    }
};
