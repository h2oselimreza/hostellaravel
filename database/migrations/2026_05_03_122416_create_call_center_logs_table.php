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
        Schema::create('call_center_log', function (Blueprint $table) {
            $table->id();

            $table->string('log_id', 50)->index();
            $table->string('ref_log_id', 50)->nullable();

            $table->string('call_type', 30);
            $table->string('company', 50)->nullable();

            $table->string('customer_name', 250)->nullable();
            $table->string('customer_mobile_no', 20);

            $table->text('customer_address')->nullable();

            $table->string('call_reason', 50);
            $table->text('call_reason_text')->nullable();

            $table->string('customer_feedback', 50);
            $table->text('customer_feedback_text')->nullable();

            $table->dateTime('call_start_dt_tm')->nullable();
            $table->dateTime('call_end_dt_tm')->nullable();
            $table->dateTime('next_call_dt_tm')->nullable();

            $table->integer('next_call_status')->default(0)
                  ->comment('0=no call, 1=call pending, 2=done, 3=taken by user');

            $table->dateTime('next_call_flag_dt_tm')->nullable();

            $table->text('remarks')->nullable();

            $table->string('created_by', 50);
            $table->string('updated_by', 50);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_center_log');
    }
};
