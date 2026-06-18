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
        Schema::create('emp_edu_qualification', function (Blueprint $table) {
            $table->id(); // id Primary

            $table->string('employee_id', 64);

            $table->string('level_of_education', 300);
            $table->string('exam_degree', 300);

            $table->string('institute_name', 300)->nullable();
            $table->string('education_board', 300)->nullable();
            $table->string('qualification_result', 300)->nullable();

            $table->string('cgpa_marks', 32)->nullable();
            $table->string('scale', 32)->nullable();
            $table->string('passing_year', 10)->nullable();

            $table->string('duration', 10);

            $table->string('major_group', 30)->nullable();

            $table->integer('is_active')->default(1);

            $table->string('created_by', 70);
            $table->string('updated_by', 70);
            $table->timestamp('created_dt_tm')->useCurrent();
            $table->timestamp('updated_dt_tm')
                ->useCurrent()
                ->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emp_edu_qualification');
    }
};
