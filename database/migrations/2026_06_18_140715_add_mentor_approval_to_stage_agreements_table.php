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
        // Tijdstip waarop de mentor akkoord gaf (waarmee de overeenkomst ontstaat).
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->timestamp('mentor_approved_at')->nullable()->after('uploaded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->dropColumn('mentor_approved_at');
        });
    }
};
