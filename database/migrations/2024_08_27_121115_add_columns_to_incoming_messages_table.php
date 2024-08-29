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
            $table->string('incoming_checksum')->after('profile_name')->nullable();
            $table->string('checksum')->after('incoming_checksum')->nullable();
            $table->string('checksum_status')->after('checksum')->nullable();
            $table->string('log_id')->after('inc_messages_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('echannels_incoming_messages', function (Blueprint $table) {
            $table->dropColumn('incoming_checksum');
            $table->dropColumn('checksum');
            $table->dropColumn('checksum_status');
            $table->dropColumn('log_id');
        });
    }
};
