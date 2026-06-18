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
        Schema::create('membership_card', function (Blueprint $table) {
            $table->id(); // int(11) AUTO_INCREMENT
            $table->string('card_id', 30);
            $table->string('card_number', 20);
            $table->string('package_code', 10);
            $table->integer('validity_month')->default(0);
            $table->string('company', 50)->nullable();
            $table->dateTime('activation_dt_tm')->nullable();
            $table->dateTime('valid_dt_tm')->nullable();
            $table->integer('status')->default(2);
            
            // 3 = active, 4 = inactive, 5 = not activate
            $table->integer('is_active')->default(2);

            // Audit Trails (Created)
            $table->string('created_by', 50);
            $table->dateTime('created_dt_tm');
            $table->string('created_type', 50);

            // Audit Trails (Updated)
            $table->string('updated_by', 50);
            $table->dateTime('updated_dt_tm');
            $table->string('updated_type', 50);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_card');
    }
};
