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
        Schema::create('default_reminders', function (Blueprint $table) {
            $table->id();

            $table->string('company', 50)->index();

            $table->string('reminder_for', 30)->index();

            $table->string('reminder_channel_type', 20)->index();

            $table->string('reminder_no', 100)->index();

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
        Schema::dropIfExists('default_reminders');
    }
};
