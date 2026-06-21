<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Weeklog-inhoud opgesplitst in drie delen i.p.v. één groot tekstveld.
        Schema::table('weeklogs', function (Blueprint $table) {
            $table->text('tasks_description')->nullable()->after('period_end');
            $table->text('reflection')->nullable()->after('tasks_description');
            $table->text('learning_points')->nullable()->after('reflection');
        });
    }

    public function down(): void
    {
        Schema::table('weeklogs', function (Blueprint $table) {
            $table->dropColumn(['tasks_description', 'reflection', 'learning_points']);
        });
    }
};
