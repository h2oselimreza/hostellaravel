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
         Schema::create('reminder', function (Blueprint $table) {

            $table->id();

            $table->string('reminder_no', 30)->index();

            $table->string('reminder_for', 20);

            $table->string('reminder_for_value', 50)->nullable();

            $table->string('reminder_type', 30)->nullable();

            $table->string('company', 50);

            $table->string('heading', 100);

            $table->text('body');

            $table->dateTime('reminder_on_dt_tm');

            $table->dateTime('next_show_dt_tm')->nullable();

            $table->integer('repeat_every');

            $table->string('repeat_type', 20);

            $table->integer('before_reminder_count');

            $table->string('before_reminder_type', 20);

            $table->integer('default_mobile_flag')->default(0);

            $table->integer('default_email_flag')->default(0);

            $table->text('default_mobile_no')->nullable();

            $table->text('default_email')->nullable();

            $table->text('mobile_no')->nullable();

            $table->text('email')->nullable();

            $table->string('created_by', 70);
            $table->string('updated_by', 70);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder');
    }
};
