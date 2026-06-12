<?php

namespace App\Livewire\Student;

use App\Models\Stage;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        // Voorlopig de eerste stage (testdata). Later: stage van de ingelogde student.
        $stage = Stage::with(['student.user', 'company', 'finalReport'])
            ->withCount('weeklogs')
            ->first();

        $weeklogs = $stage
            ? $stage->weeklogs()->orderByDesc('week_number')->take(5)->get()
            : collect();

        // Stage-week voortgang (verdedigend berekend uit de start/eind-datum).
        $currentWeek = null;
        $totalWeeks = null;
        if ($stage && $stage->start_date && $stage->end_date) {
            $start = Carbon::parse($stage->start_date);
            $end = Carbon::parse($stage->end_date);
            $totalWeeks = max(1, (int) ceil($start->diffInDays($end) / 7));
            $elapsed = (int) floor($start->diffInDays(now(), false) / 7) + 1;
            $currentWeek = max(1, min($elapsed, $totalWeeks));
        }

        return view('livewire.student.dashboard', [
            'stage' => $stage,
            'weeklogs' => $weeklogs,
            'currentWeek' => $currentWeek,
            'totalWeeks' => $totalWeeks,
        ]);
    }
}
