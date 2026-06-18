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
        Schema::create('enrolled_corp_indv', function (Blueprint $table) {

            $table->id();

            $table->string('corp_company', 50);

            $table->string('indv_company', 50);

            $table->string('indv_employee', 50);

            $table->string('department', 200)
                ->nullable();

            $table->string('designation', 200)
                ->nullable();

            $table->string('display_emp_code', 50)
                ->nullable();

            $table->string('created_by', 50);

            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 50);

            $table->dateTime('updated_dt_tm');

            /*
            |--------------------------------------------------------------------------
            | INDEXES (Future Performance Safe)
            |--------------------------------------------------------------------------
            */
            $table->index('corp_company');

            $table->index('indv_company');

            $table->index('indv_employee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrolled_corp_indv');
    }
};
