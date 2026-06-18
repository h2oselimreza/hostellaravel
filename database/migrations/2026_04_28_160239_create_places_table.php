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
        Schema::create('places', function (Blueprint $table) {
            $table->id();

            $table->string('place_code', 50)->nullable();
            $table->string('place_type', 30);

            $table->string('title', 250)->nullable();
            $table->string('title_bn', 250)->nullable();

            $table->text('address')->nullable();
            $table->text('address_bn')->nullable();

            $table->string('place_email', 100)->nullable();
            $table->string('website', 100)->nullable();

            $table->string('place_mobile', 20)->nullable();
            $table->string('place_land_phone', 20)->nullable();

            $table->string('profile_image', 100)->nullable();

            $table->integer('division')->default(0);
            $table->integer('district')->default(0);
            $table->integer('upozilla')->default(0);
            $table->integer('postal_code')->default(0);

            $table->string('latitude', 50)->nullable();
            $table->string('longitude', 50)->nullable();

            $table->string('primary_contact_person', 200)->nullable();
            $table->string('primary_contact_designation', 100)->nullable();
            $table->string('primary_contact_mobile', 20)->nullable();
            $table->string('primary_contact_email', 100)->nullable();

            $table->string('second_contact_person', 200)->nullable();
            $table->string('second_contact_designation', 100)->nullable();
            $table->string('second_contact_mobile', 20)->nullable();
            $table->string('second_contact_email', 100)->nullable();

            $table->integer('is_active')->default(1);
            $table->integer('status')->nullable();
            $table->string('place_display_code', 100)->nullable();

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
        Schema::dropIfExists('places');
    }
};
