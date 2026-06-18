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
        Schema::create('quotation_res_summary', function (Blueprint $table) {
            $table->id();

            $table->string('request_no', 30);
            $table->string('quotation_no', 30)->index();
            $table->string('reference_quo_no', 50)->nullable();

            $table->string('workshop', 50);

            $table->decimal('service_vat', 10, 2)->nullable();
            $table->decimal('product_vat', 10, 2)->nullable();

            $table->integer('status')->default(2);

            $table->text('note')->nullable();

            $table->date('quo_sending_date')->nullable();

            $table->integer('is_active')->default(1);

            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 70);
            $table->dateTime('updated_dt_tm');

            $table->index('request_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_res_summary');
    }
};
