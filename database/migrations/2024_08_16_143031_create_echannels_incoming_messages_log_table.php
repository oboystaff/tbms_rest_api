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
            $table->string('incoming_messages_id');
            $table->string('request_url', 2000)->nullable();
            $table->string('response_url', 2000)->nullable();
            $table->string('response_code', 9)->nullable();
            $table->string('response_message', 200)->nullable();
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
