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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 70)->index();
            $table->string('username', 100)->index();

            $table->integer('user_group')->default(0);

            $table->string('email', 300)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('user_type_code', 30);
            $table->string('panel_type');

            $table->string('full_name', 250)->nullable();
            $table->string('contact_no', 30)->nullable();

            $table->boolean('is_reset')->default(true);

            $table->string('created_by', 70);
            $table->string('created_type');
            $table->string('updated_by', 70);

            $table->timestamp('created_dt_tm');
            $table->timestamp('updated_dt_tm');

            $table->boolean('is_active')->default(true);
            
            $table->string('password');
            $table->string('password_reset_code')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
