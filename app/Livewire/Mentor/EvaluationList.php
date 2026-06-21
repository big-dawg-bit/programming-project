<?php

namespace App\Livewire\Mentor;

use App\Models\Mentor;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Evaluaties')]
class EvaluationList extends Component
{
    // Actieve tab: 'te doen' of 'ingediend'.
    public string $tab = 'te doen';

    // De twee vaste evaluatiemomenten.
    public const TYPES = [
        'mid-term' => 'Tussentijdse evaluatie',
        'final' => 'Eindevaluatie',
    ];

    public function render()
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();

        // ALLE stagiairs van de mentor (nieuw → oud), met hun ingediende mentor-evaluaties.
        // (Vroeger werd enkel de eerste stage getoond, waardoor extra stagiairs onzichtbaar bleven.)
        $stages = $mentor
            ? $mentor->stages()
                ->with([
                    'student.user',
                    'company',
                    'evaluations' => fn ($q) => $q->where('evaluator_role', 'mentor')
                        ->where('status', 'submitted')
                        ->latest('submitted_at'),
                ])
                ->latest()
                ->get()
            : collect();

        // Per stagiair: de ingediende evaluaties + de nog te doen evaluatiemomenten.
        $studenten = $stages->map(function ($stage) {
            $ingediend = $stage->evaluations;
            $ingediendeTypes = $ingediend->pluck('type')->all();

            $teDoen = collect(self::TYPES)
                ->reject(fn ($label, $type) => in_array($type, $ingediendeTypes, true))
                ->map(fn ($label, $type) => ['type' => $type, 'label' => $label])
                ->values();

            return [
                'stage' => $stage,
                'student' => $stage->student?->user?->name ?? 'Onbekende student',
                'bedrijf' => $stage->company?->name ?? '—',
                'heeftKader' => $stage->framework_id !== null,
                'ingediend' => $ingediend,
                'teDoen' => $teDoen,
            ];
        });

        return view('livewire.mentor.evaluation-list', [
            'studenten' => $studenten,
            'heeftStudenten' => $stages->isNotEmpty(),
            'teDoenCount' => $studenten->sum(fn ($r) => $r['teDoen']->count()),
            'ingediendCount' => $studenten->sum(fn ($r) => $r['ingediend']->count()),
        ]);
    }
}
