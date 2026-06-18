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
        Schema::create('vehicle_booking_details', function (Blueprint $table) {

            $table->id();

            $table->string('booking_no', 50);

            $table->string('detail_no', 50);

            $table->string('ref_no', 50)
                ->nullable();

            $table->string('application_from', 50)
                ->nullable();

            $table->string('application_from_type', 50);

            $table->string('application_to', 50)
                ->default('fms');

            $table->string('application_to_type', 50)
                ->nullable();

            $table->string('forward_to', 50)
                ->nullable();

            $table->string('forward_to_type', 50)
                ->nullable();

            $table->text('comment_from')
                ->nullable();

            $table->text('comment_to')
                ->nullable();

            $table->integer('status')
                ->default(3);

            $table->string('created_by', 50);

            $table->string('created_type', 50);

            $table->dateTime('created_dt_tm');

            $table->string('updated_by', 50);

            $table->string('updated_type', 50);

            $table->dateTime('updated_dt_tm');

            $table->index('booking_no');
            $table->index('detail_no');
            $table->index('application_from');
            $table->index('application_to');
            $table->index('forward_to');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_booking_details');
    }
};
