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
        Schema::create('member_family', function (Blueprint $table) {
            $table->id(); // id column (Primary, AUTO_INCREMENT)
            $table->string('member_id', 50);
            $table->string('name', 250);
            $table->string('relation', 100)->nullable();
            $table->date('dob')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('occupation', 200)->nullable();
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
        Schema::dropIfExists('member_family');
    }
};
