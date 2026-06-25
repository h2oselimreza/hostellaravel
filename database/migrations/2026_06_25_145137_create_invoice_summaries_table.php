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
         Schema::create('invoice_summary', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_no', 30)->index();
            $table->string('reference_no', 30);

            $table->string('val_id', 100)->nullable();

            $table->string('invoice_title', 200);

            $table->date('invoice_date');
            $table->date('invoice_due_date');

            $table->string('boarder', 50)->nullable();
            $table->string('boarder_name', 250)->nullable();
            $table->string('boarder_email', 250)->nullable();
            $table->string('boarder_address', 500)->nullable();
            $table->string('boarder_city', 200)->nullable();
            $table->string('boarder_postcode', 10)->nullable();
            $table->string('boarder_primary_mobile', 30)->nullable();

            $table->string('guest_name', 200)->nullable();
            $table->string('guest_mobile', 50)->nullable();
            $table->string('guest_email', 250)->nullable();
            $table->string('guest_address', 500)->nullable();
            $table->string('guest_city', 200)->nullable();
            $table->string('guest_postcode', 10)->nullable();

            $table->integer('is_guest')->default(0);

            $table->string('invoice_type', 20)->default('general');

            $table->decimal('invoice_amount', 10, 2);
            $table->decimal('total_amount', 10, 2)->default(0.00);

            $table->decimal('discount', 10, 2);
            $table->integer('discount_type')->default(1);

            $table->decimal('paid_amount', 10, 2);

            $table->dateTime('payment_dt_tm')->nullable();
            $table->string('payment_method', 50)->nullable();

            $table->integer('is_paid')->default(0);

            $table->text('tran_status_history')->nullable();

            $table->integer('mail_send_status')->default(0);

            $table->integer('is_admission_invoice')->default(0);

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
        Schema::dropIfExists('invoice_summary');
    }
};
