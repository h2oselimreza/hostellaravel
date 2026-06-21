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
        Schema::create('hst_seat', function (Blueprint $table) {
            $table->id();

            $table->string('seat_code', 50);
            $table->string('seat_type', 50);
            $table->string('room', 50);

            $table->string('title', 250);

            $table->text('description')->nullable();
            $table->string('seat_image', 100)->nullable();

            $table->integer('is_active')->default(1);

            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 70);
            $table->dateTime('updated_dt_tm');

            $table->index('seat_code');
            $table->index('seat_type');
            $table->index('room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hst_seat');
    }
};
