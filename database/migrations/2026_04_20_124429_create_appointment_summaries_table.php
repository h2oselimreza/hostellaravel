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
        Schema::create('appointment_summary', function (Blueprint $table) {
            $table->id();

            $table->string('appointment_no', 30);
            $table->string('company', 30);
            $table->string('workshop', 50);

            $table->date('date_1')->nullable();
            $table->date('date_2')->nullable();

            $table->string('time_slot_1', 20)->nullable();
            $table->string('time_slot_2', 20)->nullable();

            $table->date('final_date')->nullable();
            $table->time('appointment_time')->nullable();

            $table->text('remarks')->nullable();
            $table->text('reject_reason')->nullable();

            $table->integer('status')->default(2);

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
        Schema::dropIfExists('appointment_summary');
    }
};
