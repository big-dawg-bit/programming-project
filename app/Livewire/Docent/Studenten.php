<?php

namespace App\Livewire\Docent;

use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Toegewezen studenten')]
class Studenten extends Component
{
    // Zoekterm op studentnaam.
    public string $zoek = '';

    public function render()
    {
        $docent = auth()->user()?->docent;

        $stages = $docent
            ? $docent->stages()
                ->with(['student.user', 'company', 'mentor.user', 'weeklogs', 'evaluations'])
                ->get()
            : collect();

        // Per begeleide stage een rij met afgeleide gegevens.
        $studenten = $stages->map(function ($stage) {
            $week = $totaal = null;
            if ($stage->start_date && $stage->end_date) {
                $start = Carbon::parse($stage->start_date);
                $eind = Carbon::parse($stage->end_date);
                $totaal = max(1, (int) ceil($start->diffInDays($eind) / 7));
                $week = max(1, min((int) floor($start->diffInDays(now(), false) / 7) + 1, $totaal));
            }

            return [
                'naam' => $stage->student?->user?->name ?? 'Onbekende student',
                'opleiding' => $stage->student?->study_program ?? '—',
                'bedrijf' => $stage->company?->name ?? '—',
                'week' => $week,
                'totaal' => $totaal,
                'mentor' => $stage->mentor?->user?->name ?? '—',
                'weeklogs' => $stage->weeklogs->whereNotNull('submitted_at')->count(),
                'evaluatie' => $stage->evaluations->where('status', 'submitted')->isNotEmpty()
                    ? 'compleet'
                    : 'in afwachting',
            ];
        });

        if (trim($this->zoek) !== '') {
            $term = mb_strtolower(trim($this->zoek));
            $studenten = $studenten->filter(fn ($s) => str_contains(mb_strtolower($s['naam']), $term))->values();
        }

        return view('livewire.docent.studenten', [
            'studenten' => $studenten,
        ]);
    }
}
