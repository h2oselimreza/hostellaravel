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
        Schema::create('quotation_req_detail', function (Blueprint $table) {

            $table->id();

            $table->string('request_no', 30);

            $table->string('request_details_no', 50)->index();

            $table->string('vehicle', 50);

            $table->string('service_veriant', 50)->nullable();

            $table->string('product_variant', 50)->nullable();

            $table->string('product_display_name', 300)->nullable();

            $table->string('request_type', 30);

            $table->integer('quantity')->default(1);

            $table->string('unit_name', 50)->nullable();

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
        Schema::dropIfExists('quotation_req_detail');
    }
};
