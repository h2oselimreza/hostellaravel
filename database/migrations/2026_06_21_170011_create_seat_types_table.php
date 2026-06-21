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
        Schema::create('hst_seat_type', function (Blueprint $table) {
            $table->id();

            $table->string('title', 100)->index();
            $table->string('seat_type_code', 50)->index();

            $table->text('description')->nullable();

            $table->integer('is_active')->default(1);

            $table->string('created_by', 50);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 50);
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hst_seat_type');
    }
};
