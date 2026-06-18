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
        Schema::create('company_notification_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('company', 50)->index();
            $table->string('notification_code', 100)->index();
            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_notification_permissions');
    }
};
