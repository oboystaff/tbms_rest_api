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
        Schema::create('tBillAccount', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tdate');
            $table->string('login_id');
            $table->string('bank_code');
            $table->string('account_number');
            $table->string('action');
            $table->string('echannel');
            $table->string('trace_id');
            $table->string('txn_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tBillAccount');
    }
};
