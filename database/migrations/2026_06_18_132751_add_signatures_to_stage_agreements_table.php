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
        // Handtekeningen (getekend op de trackpad, opgeslagen als base64 PNG-dataURL).
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->longText('student_signature')->nullable()->after('status');
            $table->timestamp('student_signed_at')->nullable()->after('student_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stage_agreements', function (Blueprint $table) {
            $table->dropColumn([
                'student_signature',
                'student_signed_at',
            ]);
        });
    }
};
