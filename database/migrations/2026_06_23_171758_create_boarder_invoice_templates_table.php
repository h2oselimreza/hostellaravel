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
        Schema::create('boarder_invoice_template', function (Blueprint $table) {
            $table->id();

            $table->string('boarder', 50);

            $table->string('item_head', 50);

            $table->decimal('quantity', 10, 2);

            $table->decimal('unit_price', 10, 2);

            $table->string('template_type', 100)->default('1');

            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 70);
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boarder_invoice_template');
    }
};
