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
        Schema::table('echannels_incoming_messages_log', function (Blueprint $table) {
            $table->dropColumn('incoming_messages_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echannels_incoming_messages_log', function (Blueprint $table) {
            $table->string('incoming_messages_id')->after('log_id');
        });
    }
};
