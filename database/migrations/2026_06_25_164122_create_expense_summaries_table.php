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
        Schema::create('expense_summary', function (Blueprint $table) {
            $table->id();

            $table->string('expense_title', 200);
            $table->date('expense_date');

            $table->string('expense_no', 30)->index();

            $table->decimal('total_amount', 10, 2);

            $table->string('vendor', 50)->nullable();

            $table->string('guest_name', 200)->nullable();
            $table->string('guest_mobile', 50)->nullable();

            $table->integer('is_guest')->default(0);

            $table->string('expense_type', 20)->default('vehicle');

            $table->string('created_by', 70);
            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 70);
            $table->dateTime('updated_dt_tm');

            // Recommended indexes
            $table->index('expense_type');
            $table->index('expense_date');
            $table->index('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_summary');
    }
};
