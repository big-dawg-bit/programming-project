<?php

namespace App\Livewire\Docent;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class StudentDetail extends Component
{
    // Naam van de bekeken student (via de route doorgegeven).
    public string $naam = '';

    // Actieve tab (weeklogs | evaluaties | rapport).
    public string $tab = 'weeklogs';

    public function mount(string $naam): void
    {
        $this->naam = urldecode($naam);
    }

    /** De begeleide stage van deze docent die bij de gevraagde student hoort. */
    private function stage()
    {
        $docent = auth()->user()?->docent;

        if (! $docent) {
            return null;
        }

        return $docent->stages()
            ->with([
                'student.user',
                'company',
                'mentor.user',
                'weeklogs',
                'evaluations.scores.competency',
                'finalReport',
            ])
            ->get()
            ->first(fn ($stage) => $stage->student?->user?->name === $this->naam);
    }

    public function render()
    {
        $stage = $this->stage();

        $week = $totaal = null;
        if ($stage && $stage->start_date && $stage->end_date) {
            $start = Carbon::parse($stage->start_date);
            $eind = Carbon::parse($stage->end_date);
            $totaal = max(1, (int) ceil($start->diffInDays($eind) / 7));
            $week = max(1, min((int) floor($start->diffInDays(now(), false) / 7) + 1, $totaal));
        }

        return view('livewire.docent.student-detail', [
            'stage' => $stage,
            'huidigeWeek' => $week,
            'totaalWeken' => $totaal,
        ])->layoutData(['title' => $this->naam]);
    }
}
