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
        Schema::create('item_heads', function (Blueprint $table) {
            $table->id();

            $table->string('item_category', 50);
            $table->string('item_head', 200);

            $table->string('unit_name', 50)->nullable();
            $table->decimal('unit_price', 10, 2)->default(0.00);

            $table->string('item_head_code', 50);
            $table->string('item_head_dis_code', 50)->nullable();

            $table->integer('is_active')->default(1);

            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 70);
            $table->dateTime('updated_dt_tm');

            $table->index('item_head');
            $table->index('item_head_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_heads');
    }
};
