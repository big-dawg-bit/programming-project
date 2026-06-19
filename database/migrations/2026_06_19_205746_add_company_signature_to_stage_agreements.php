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
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->longText('company_signature')->nullable()->after('docent_signed_at');
            $table->timestamp('company_signed_at')->nullable()->after('company_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->dropColumn(['company_signature', 'company_signed_at']);
        });
    }
};
