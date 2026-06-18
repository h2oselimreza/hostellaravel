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
        Schema::create('stock_summary', function (Blueprint $table) {

            $table->id();

            $table->string('company', 50);

            $table->string('stock_summary_id', 50)
                ->index();

            $table->string('title', 100)
                ->nullable();

            $table->date('stock_date');

            $table->string('reference_no', 100)
                ->nullable();

            $table->string('stock_type', 15);

            $table->integer('is_active')
                ->default(1);

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
        Schema::dropIfExists('stock_summary');
    }
};
