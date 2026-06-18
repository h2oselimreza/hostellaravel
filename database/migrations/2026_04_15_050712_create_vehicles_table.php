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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('company', 50)->index();
            $table->string('registration_no', 100)->nullable()->index();
            $table->string('vehicle_id', 50)->index();

            $table->string('vehicle_type', 50)->nullable();
            $table->string('vehicle_group', 50)->nullable();
            $table->string('brand', 50)->nullable();
            $table->string('brand_model', 50)->nullable();
            $table->string('vehicle_class', 50)->nullable();
            $table->string('vehicle_cc', 50)->nullable();
            $table->string('color', 50)->nullable();

            $table->integer('manufacturing_year')->nullable();
            $table->string('manufacturing_country', 100)->nullable();

            $table->string('engine_no', 30)->nullable();
            $table->string('chasis_no', 30)->nullable();
            $table->string('communication_code', 100)->nullable();

            $table->date('registration_date')->nullable();

            $table->integer('seat_capacity')->nullable();

            $table->date('fitness_issue_date')->nullable();
            $table->date('fitness_validity_from_date')->nullable();
            $table->date('fitness_validity_todate')->nullable();
            $table->decimal('fitness_renew_fee', 10, 2)->nullable();

            $table->date('tax_fee_issue_date')->nullable();
            $table->date('tax_period_from_date')->nullable();
            $table->date('tax_period_to_date')->nullable();
            $table->decimal('tax_fee', 10, 2)->nullable();

            $table->date('insurance_issue_date')->nullable();
            $table->string('insurance_nature', 100)->nullable();
            $table->decimal('insurance_pre_amount', 10, 2)->nullable();
            $table->string('insurance_company', 100)->nullable();
            $table->string('insurance_contact_person', 100)->nullable();
            $table->string('insurance_mobile', 20)->nullable();
            $table->date('insurance_form_date')->nullable();
            $table->date('insurance_to_date')->nullable();

            $table->date('route_issue_date')->nullable();
            $table->string('permit_no', 30)->nullable();
            $table->decimal('permit_fee', 10, 2)->nullable();
            $table->string('route_area', 300)->nullable();

            $table->integer('tyre_number')->default(0);

            $table->date('route_form_date')->nullable();
            $table->date('route_to_date')->nullable();

            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 10, 2)->default(0.00);
            $table->decimal('dpy', 10, 2)->default(0.00);

            $table->string('driver_id', 50)->nullable();

            $table->string('pull_emp_name', 250)->nullable();
            $table->string('pull_designation', 200)->nullable();
            $table->string('pull_department', 100)->nullable();
            $table->string('pull_id_no', 100)->nullable();
            $table->date('pull_receive_date')->nullable();

            $table->text('pull_route')->nullable();
            $table->text('route_json')->nullable();

            $table->string('pull_current_location', 200)->nullable();
            $table->string('pull_detail_ref_no', 30)->nullable();

            $table->text('pull_remarks')->nullable();

            $table->string('assign_type', 20)->default('vacant');

            $table->integer('is_active')->default(1);

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
        Schema::dropIfExists('vehicles');
    }
};
