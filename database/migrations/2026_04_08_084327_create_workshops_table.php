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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('workshop_code', 50);
            $table->string('title', 250);
            $table->text('address');
            $table->string('workshop_email', 100);
            $table->string('website', 100);
            $table->string('workshop_mobile', 20);
            $table->string('workshop_land_phone', 20);
            $table->string('profile_image', 100)->nullable();
            $table->integer('division');
            $table->integer('district');
            $table->integer('upozilla');
            $table->integer('postal_code');
            $table->string('latitude', 50);
            $table->string('longitude', 50);
            $table->string('primary_contact_person', 200);
            $table->string('primary_contact_designation', 100);
            $table->string('primary_contact_mobile', 20);
            $table->string('primary_contact_email', 100);
            $table->string('second_contact_person', 200);
            $table->string('second_contact_designation', 100);
            $table->string('second_contact_mobile', 20);
            $table->string('second_contact_email', 100);
            $table->integer('is_active')->default(1);
            $table->integer('status');
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
        Schema::dropIfExists('workshops');
    }
};
