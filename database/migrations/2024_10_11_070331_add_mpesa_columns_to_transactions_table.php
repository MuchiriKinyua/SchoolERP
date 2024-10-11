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
        Schema::table('transactions', function (Blueprint $table) {
        $table->string('mpesa_reference')->nullable(); 
        $table->dateTime('payment_date')->nullable();  
        $table->string('status')->default('pending'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('mpesa_reference');
            $table->dropColumn('payment_date');
            $table->dropColumn('status');
        });
    }
};
