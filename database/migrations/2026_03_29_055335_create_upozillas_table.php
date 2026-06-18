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
        Schema::create('upozillas', function (Blueprint $table) {
            $table->id();
            $table->integer('district'); // FK as per your structure
            $table->string('upozilla_en_name', 100);
            $table->string('upozilla_bn_name', 100);
            $table->integer('is_active')->default(1);
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
        Schema::dropIfExists('upozillas');
    }
};
