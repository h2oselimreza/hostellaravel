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
        Schema::create('user_group', function (Blueprint $table) {
            $table->id();
            $table->string('group_name', 250)->nullable();
            $table->text('modules');
            $table->text('sub_modules');
            $table->string('panel_type',100);
            $table->integer('is_active')->default(1);
            $table->string('created_by', 70);
            $table->string('updated_by', 70);
            $table->timestamp('created_dt_tm');
            $table->timestamp('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_group');
    }
};
