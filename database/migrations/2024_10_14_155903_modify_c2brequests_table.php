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
        Schema::table('c2brequests', function (Blueprint $table) {
            $table->decimal('OrgAccountBalance', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c2brequests', function (Blueprint $table) {
            $table->decimal('OrgAccountBalance', 10, 2)->nullable(false)->change(); // Set back to NOT NULL if necessary
        });
    }
};
