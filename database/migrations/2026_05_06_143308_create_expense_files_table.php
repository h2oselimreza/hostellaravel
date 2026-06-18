<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_files', function (Blueprint $table) {
            $table->id();

            $table->string('expense_no', 30);
            $table->string('original_name', 100);
            $table->string('file_name', 50);

            $table->string('created_by', 70);
            $table->string('updated_by', 70);

            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_files');
    }
};
