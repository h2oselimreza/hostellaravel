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
        Schema::create('stock', function (Blueprint $table) {

            $table->id();

            $table->string('company', 50);

            $table->string('variant', 50)
                ->index();

            $table->decimal('quantity', 10, 2)
                ->default(0.00);

            $table->integer('status')
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
        Schema::dropIfExists('stock');
    }
};
