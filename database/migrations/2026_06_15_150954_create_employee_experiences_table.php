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
        Schema::create('customer_emp_working_exp', function (Blueprint $table) {
            $table->id();

            $table->string('employee_id', 64);

            $table->string('institution_name', 250)->nullable();
            $table->string('institution_type', 128)->nullable();
            $table->string('address', 500)->nullable();

            $table->string('designation', 250)->nullable();
            $table->string('department', 150)->nullable();

            $table->date('from_date');
            $table->date('to_date')->nullable();

            $table->tinyInteger('is_continued')->default(0);

            $table->text('responsibilites')->nullable();

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
        Schema::dropIfExists('employee_experiences');
    }
};
