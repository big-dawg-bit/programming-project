<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_applications', function (Blueprint $table) {
            $table->string('company_status')->default('pending')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('stage_applications', function (Blueprint $table) {
            $table->dropColumn('company_status');
        });
    }
};
