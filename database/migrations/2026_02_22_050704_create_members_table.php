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
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->string('member_id', 50);
            $table->string('member_name', 250)->nullable();
            $table->string('member_bangla_name', 200)->nullable();
            $table->string('member_image', 100)->nullable();
            $table->string('designation', 200)->nullable();
            $table->date('first_joining_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->date('dob')->nullable();
            $table->string('blood_group', 10)->nullable();
            $table->string('marital_status', 20)->nullable();

            $table->string('spouse_name', 250)->nullable();
            $table->string('spouse_occupation', 250)->nullable();
            $table->string('spouse_contact', 30)->nullable();
            $table->string('spouse_office_address', 500)->nullable();
            $table->string('member_tnt_phone', 50)->nullable();

            $table->string('primary_mobile', 30)->nullable();
            $table->string('secendary_mobile', 30)->nullable(); // fixed typo

            $table->string('emer_contact_name', 250)->nullable();
            $table->string('emer_contact_relation', 250)->nullable();
            $table->string('emer_contact_mobile', 30)->nullable(); // fixed typo
            $table->string('emer_contact_address', 500)->nullable();

            $table->string('email', 250)->nullable();
            $table->string('present_address', 500)->nullable();
            $table->string('member_permanent_address', 500)->nullable();
            $table->string('national_id', 50)->nullable();

            $table->string('father_name', 250)->nullable();
            $table->string('father_occupation', 250)->nullable();
            $table->string('father_office_address', 500)->nullable();
            $table->string('father_contact', 30)->nullable();

            $table->string('mother_name', 250)->nullable();
            $table->string('mother_occupation', 250)->nullable();
            $table->string('mother_office_address', 500)->nullable();
            $table->string('mother_contact', 30)->nullable();

            $table->string('guardian_name', 200)->nullable();
            $table->string('guardian_contact', 30)->nullable();
            $table->string('guardian_relation', 250)->nullable();
            $table->string('guardian_house_address', 500)->nullable();

            $table->string('last_organization', 250)->nullable();
            $table->string('last_org_address', 500)->nullable();
            $table->string('last_org_designation', 250)->nullable();
            $table->date('last_org_from_date')->nullable();
            $table->date('last_org_to_date')->nullable();

            $table->string('passport_no', 100)->nullable();
            $table->date('passport_expiry_date')->nullable(); // fixed typo
            $table->string('driving_license_no', 100)->nullable();
            $table->date('driving_license_expiry_date')->nullable();

            $table->date('anniversary')->nullable();

            $table->string('society_block', 50);
            $table->string('society_road', 50);
            $table->string('society_plot', 50);
            $table->string('society_flat', 50);

            $table->string('member_type', 50)->nullable()->default('life');
            $table->string('first_introduced_by', 50)->nullable();;
            $table->string('second_introduced_by', 50)->nullable();
            $table->string('member_occupation', 100)->nullable();
            $table->string('institution_name', 300)->nullable();

            $table->boolean('is_active')->default(1);
            $table->boolean('system_user')->default(0);
            $table->boolean('birthday_sms_status')->default(0);
            $table->boolean('anniversary_sms_status')->default(0);

            $table->string('donar_member_id', 100)->nullable();

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
        Schema::dropIfExists('members');
    }
};
