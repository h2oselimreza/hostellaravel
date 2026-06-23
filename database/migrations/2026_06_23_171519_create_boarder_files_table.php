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
        Schema::create('boarder_file', function (Blueprint $table) {
            $table->id();

            $table->string('boarder', 50)->index();

            $table->string('original_name', 100);

            $table->string('file_name', 50)->index();

            $table->string('file_type', 30);

            $table->integer('is_active')->default(1);

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
        Schema::dropIfExists('boarder_file');
    }
};
