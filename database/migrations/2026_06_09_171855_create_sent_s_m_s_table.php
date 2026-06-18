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
        Schema::create('sent_sms', function (Blueprint $table) {
            $table->id();

            $table->string('reference_number', 50)->index();
            $table->string('mobile_no', 20)->nullable();
            $table->string('mail_address', 100)->nullable();

            $table->string('sms_template', 256);
            $table->integer('sms_count');

            $table->text('heading')->nullable();
            $table->text('custom_sms')->nullable();

            $table->string('reminder_no', 30)->nullable();
            $table->string('reminder_for', 20)->nullable();
            $table->string('reminder_for_value', 50)->nullable();
            $table->string('reminder_type', 30)->nullable();

            $table->string('company', 50)->nullable();
            $table->string('module_type', 100)->nullable()->comment('reference table name');

            $table->string('channel_type', 20)->default('mobileNo');

            $table->string('created_by', 70);
            $table->string('updated_by', 70);

            $table->string('created_type', 30);
            $table->string('updated_type', 30);
            
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_sms');
    }
};
