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
        Schema::create('home_service_app_summary_gen', function (Blueprint $table) {
            $table->id();

            $table->string('appointment_no', 30)->index();
            $table->string('company', 30);

            $table->string('name', 200);
            $table->string('mobile', 30);
            $table->string('address', 300);

            $table->date('service_date');
            $table->time('service_time');

            $table->date('final_date')->nullable();
            $table->time('appointment_time')->nullable();

            // 💰 Billing fields
            $table->decimal('grand_total', 10, 2)->default(0.00);
            $table->text('additional_bill')->nullable();
            $table->decimal('total_additional_bill', 10, 2)->default(0.00);
            $table->decimal('discount', 10, 2)->default(0.00);

            // 📝 Notes
            $table->text('remarks')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->text('reject_reason')->nullable();

            $table->string('leads_by', 50)->nullable();

            $table->integer('status')->default(2);

            // 👨‍🔧 Assignment
            $table->string('assign_emp', 50)->nullable();
            $table->dateTime('assign_emp_dt_tm')->nullable();

            $table->string('transaction_channel', 30)->nullable();

            // 🧾 Audit fields
            $table->string('created_by', 70);
            $table->string('created_type', 20);

            $table->string('updated_by', 70);
            $table->string('updated_type', 20);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_service_app_summary_gen');
    }
};
