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
        Schema::create('echannels_incoming_messages_log', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('log_id')->unique();
            $table->string('request_url', 2000)->nullable();
            $table->string('response_url', 2000)->nullable();
            $table->string('response_code', 9)->nullable();
            $table->string('response_message', 200)->nullable();
            $table->string('acct_no', 50)->nullable();
            $table->string('login_id', 100)->nullable();
            $table->string('tdate')->nullable();
            $table->string('sec_code', 50)->nullable();
            $table->decimal('amt', 15, 2)->nullable();
            $table->string('next_of_kin', 100)->nullable();
            $table->string('nok_contact', 20)->nullable();
            $table->string('trace_id', 100)->nullable();
            $table->string('bank_code', 20)->nullable();
            $table->string('txn_type', 10)->nullable();
            $table->string('country_code', 30)->nullable();
            $table->string('echannel', 10)->nullable();
            $table->string('funding_source', 20)->nullable();
            $table->string('app_module', 20)->nullable();
            $table->string('mobile_network', 20)->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->decimal('face_value', 15, 2)->nullable();
            $table->decimal('int_rate', 5, 2)->nullable();
            $table->decimal('disc_rate', 5, 2)->nullable();
            $table->string('value_date')->nullable();
            $table->string('maturity_date')->nullable();
            $table->string('inv_amt_type', 20)->nullable();
            $table->string('account_number', 18)->nullable();
            $table->string('action', 30)->nullable();
            $table->string('mobile_number', 18)->nullable();
            $table->string('email', 60)->nullable();
            $table->string('profile_name', 30)->nullable();
            $table->string('incoming_checksum')->nullable();
            $table->string('checksum')->nullable();
            $table->string('checksum_status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('echannels_incoming_messages_log');
    }
};
