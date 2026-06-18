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
        Schema::create('quotation_req_summary', function (Blueprint $table) {

            $table->id();

            $table->string('customer', 50);
            $table->string('customer_type', 30);
            $table->string('request_no', 30)->index();

            $table->string('approved_quotation_no', 30)
                ->nullable();
            $table->text('remarks')
                ->nullable();
            $table->text('admin_remarks')
                ->nullable();
            $table->integer('status')
                ->default(3);
            $table->date('quotation_submitted_date')
                ->nullable();
            $table->date('req_sending_date')
                ->nullable();
            $table->text('reject_reason')
                ->nullable();
            $table->string('rm_id', 50)
                ->nullable();
            $table->string('reference_no', 100)
                ->nullable();
            $table->integer('is_active')
                ->default(1);
            $table->string('created_by', 70);
            $table->string('created_by_type', 20);
            $table->string('updated_by', 70);
            $table->string('updated_by_type', 20);
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_req_summary');
    }
};
