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
        Schema::create('hst_room', function (Blueprint $table) {
            $table->id();

            $table->string('room_code', 50);
            $table->string('floor', 50);
            $table->string('title', 250);

            $table->text('description')->nullable();
            $table->string('room_image', 100)->nullable();

            $table->integer('is_active')->default(1);

            $table->string('created_by', 70)->nullable();
            $table->dateTime('created_dt_tm')->nullable();

            $table->string('updated_by', 70)->nullable();
            $table->dateTime('updated_dt_tm')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hst_room');
    }
};
