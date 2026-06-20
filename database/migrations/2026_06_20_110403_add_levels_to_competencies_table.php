<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->text('level_full')->nullable()->after('description');
            $table->text('level_good')->nullable()->after('level_full');
            $table->text('level_low')->nullable()->after('level_good');
        });
    }

    public function down(): void
    {
        Schema::table('competencies', function (Blueprint $table) {
            $table->dropColumn(['level_full', 'level_good', 'level_low']);
        });
    }
};
