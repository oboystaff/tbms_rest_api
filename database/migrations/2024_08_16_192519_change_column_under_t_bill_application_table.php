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
        Schema::table('tBillApplication', function (Blueprint $table) {
            $table->string('tdate')->change();
            $table->string('value_date')->change();
            $table->string('mat_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tBillApplication', function (Blueprint $table) {
            $table->date('tdate');
            $table->date('value_date');
            $table->date('mat_date');
        });
    }
};
