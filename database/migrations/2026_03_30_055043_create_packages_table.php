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
        Schema::create('package', function (Blueprint $table) {
             $table->id();
            $table->string('package_code', 50)->index();
            $table->string('package_name', 200)->index();
            $table->text('package_details');
            $table->string('created_by', 50);
            $table->string('updated_by', 50);
            $table->dateTime('created_dt_tm');
            $table->dateTime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package');
    }
};
