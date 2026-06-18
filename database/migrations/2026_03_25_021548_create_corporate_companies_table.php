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
        Schema::create('corporate_companies', function (Blueprint $table) {
            $table->id();

            $table->string('company_code', 50)->index();
            $table->string('company_type', 50);
            $table->string('enrolled_apprv_code', 50)->nullable();
            $table->string('title', 250)->index();

            $table->string('package', 50)->nullable();
            $table->string('membership_card', 20)->nullable();
            $table->text('address')->nullable();

            $table->string('company_email', 100)->nullable();
            $table->string('website', 100)->nullable();

            $table->string('company_mobile', 20)->index();
            $table->string('company_land_phone', 20)->nullable();

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

            $table->string('vts_company', 50)->nullable();
            $table->string('vts_app_key', 100)->nullable();
            $table->string('map_api_key', 100)->nullable();
            $table->string('rm_id', 50)->nullable();

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
        Schema::dropIfExists('corporate_companies');
    }
};
