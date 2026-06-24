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
        Schema::create('seat_allocation', function (Blueprint $table) {
             $table->id();

            $table->string('seat', 50);
            $table->string('boarder', 50);

            $table->dateTime('allocated_dt_tm');

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
        Schema::dropIfExists('seat_allocation');
    }
};
