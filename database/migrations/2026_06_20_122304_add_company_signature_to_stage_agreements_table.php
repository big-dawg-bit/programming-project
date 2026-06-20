<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->string('company_signature')->nullable()->after('mentor_approved_at');
            $table->timestamp('company_signed_at')->nullable()->after('company_signature');
        });
    }

    public function down(): void
    {
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->dropColumn(['company_signature', 'company_signed_at']);
        });
    }
};
