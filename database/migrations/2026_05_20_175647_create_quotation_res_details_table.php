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
         Schema::create('quotation_res_detail', function (Blueprint $table) {
            $table->id();
            $table->string('quotation_no', 30)->index();
            $table->string('request_no', 30)->index();
            $table->string('req_detail_no', 30)->index();
            $table->decimal('unit_price', 10, 2);
            $table->integer('is_active')->default(1);
            $table->string('created_by', 70);
            $table->string('updated_by', 70);
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_res_detail');
    }
};
