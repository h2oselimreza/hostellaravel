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
        Schema::create('otp_log', function (Blueprint $table) {
            $table->id();

            $table->string('user_id', 50)->nullable();
            $table->string('user_type_code', 30)->nullable();

            $table->unsignedBigInteger('otp_id');

            $table->string('mobile_no', 20);
            $table->string('encrypted_otp', 100);
            $table->string('otp_type', 100);

            $table->integer('otp_status');

            $table->string('created_by', 100);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 100);
            $table->dateTime('updated_dt_tm');

            $table->index('otp_id');
            $table->index('mobile_no');

            // Uncomment if otp.id is BIGINT (Laravel default)
            // $table->foreign('otp_id')
            //     ->references('id')
            //     ->on('otp')
            //     ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_log');
    }
};
