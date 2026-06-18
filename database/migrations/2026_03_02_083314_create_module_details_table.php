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
        Schema::create('web_module_description', function (Blueprint $table) {
            $table->id();
            $table->string('module_code', 4);
            $table->string('heading', 500)->nullable();
            $table->text('short_description')->nullable();
            $table->string('image', 256)->nullable();
            $table->longText('description')->nullable();
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
        Schema::dropIfExists('web_module_description');
    }
};
