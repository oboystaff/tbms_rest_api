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
        Schema::create('tBillApplication', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('acct_no');
            $table->string('login_id');
            $table->date('tdate');
            $table->string('sec_code');
            $table->decimal('amt');
            $table->string('next_of_kin');
            $table->string('next_of_kin_contact');
            $table->string('trace_id');
            $table->string('bank_code');
            $table->string('txn_type');
            $table->string('country_code');
            $table->string('echannel');
            $table->string('fsource');
            $table->string('app_module');
            $table->string('mno')->nullable();
            $table->decimal('cost');
            $table->decimal('face_value');
            $table->decimal('int_rate');
            $table->decimal('disc_rate')->nullable();
            $table->date('value_date');
            $table->date('mat_date');
            $table->string('inv_amt_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tBillApplication');
    }
};
