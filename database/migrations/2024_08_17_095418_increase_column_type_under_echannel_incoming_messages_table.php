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
            $table->string('action', 60)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echannels_incoming_messages', function (Blueprint $table) {
            $table->string('action', 3)->change();
        });
    }
};
