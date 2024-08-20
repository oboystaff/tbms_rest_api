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
        Schema::create('tBillRegistration', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('pname');
            $table->date('tdate');
            $table->string('mobile_number');
            $table->string('email');
            $table->string('echannel');
            $table->string('trace_id');
            $table->string('txn_type');
            $table->string('mno')->nullable();
            $table->string('country_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tBillRegistration');
    }
};
