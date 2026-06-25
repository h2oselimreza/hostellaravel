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
        Schema::create('invoice_detail', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_no', 30);
            $table->string('item_head', 50);
            $table->string('item_category', 50);

            $table->string('category_name', 100);
            $table->string('head_name', 200);

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
            $table->index('invoice_no');
            $table->index('item_head');
            $table->index('item_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_detail');
    }
};
