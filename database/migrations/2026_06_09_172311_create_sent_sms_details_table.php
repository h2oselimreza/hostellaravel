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
        Schema::create('sent_sms_details', function (Blueprint $table) {
            $table->id();

            $table->string('mobile_no', 20);
            $table->text('message_body')->nullable();

            $table->integer('created_by');
            $table->string('created_type', 30);

            $table->dateTime('created_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sent_sms_details');
    }
};
