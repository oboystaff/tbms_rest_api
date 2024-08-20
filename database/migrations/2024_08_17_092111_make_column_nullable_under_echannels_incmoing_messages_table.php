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
            $table->string('value_date')->nullable()->change();
            $table->string('maturity_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echannels_incoming_messages', function (Blueprint $table) {
            $table->string('value_date')->nullable(false)->change();
            $table->string('maturity_date')->nullable(false)->change();
        });
    }
};
