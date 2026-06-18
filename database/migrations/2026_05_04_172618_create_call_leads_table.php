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
        Schema::create('call_leads', function (Blueprint $table) {
            $table->id();
            $table->string('lead_code', 50);
            $table->string('name', 250)->nullable();
            $table->string('mobile', 30);
            $table->text('address')->nullable();
            
            $table->integer('call_status')->default(0)->comment('0 for call not make, 1 for make call done');
            
            $table->datetime('last_call_dt_tm')->nullable();
            
            $table->string('created_by', 70);
            $table->string('updated_by', 70);

            $table->datetime('created_dt_tm');
            $table->datetime('updated_dt_tm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_leads');
    }
};
