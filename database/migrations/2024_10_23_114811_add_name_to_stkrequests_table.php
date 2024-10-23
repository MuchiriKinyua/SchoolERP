<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToStkrequestsTable extends Migration
{
    public function up(): void
    {
        Schema::table('stkrequests', function (Blueprint $table) {
            $table->string('name')->nullable(); // Add name column
        });
    }

    public function down(): void
    {
        Schema::table('stkrequests', function (Blueprint $table) {
            $table->dropColumn('name'); // Remove name column
        });
    }
}

