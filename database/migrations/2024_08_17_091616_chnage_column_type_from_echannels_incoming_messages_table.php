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
        Schema::table('echannels_incoming_messages', function (Blueprint $table) {
            $table->string('tdate')->change();
            $table->string('value_date')->change();
            $table->string('maturity_date')->change();
            $table->string('country_code')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echannels_incoming_messages', function (Blueprint $table) {
            $table->date('tdate');
            $table->date('value_date');
            $table->date('maturity_date');
            $table->date('country_code');
        });
    }
};
