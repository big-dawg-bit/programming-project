<?php

namespace App\Livewire\Mentor;

use App\Models\Evaluation;
use App\Models\Mentor;
use App\Models\Stage;
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

    /**
     * De stage die bij de ingelogde mentor hoort.
     */
    private function currentStage(): ?Stage
    {
        $mentor = Mentor::where('user_id', Auth::id())->first();

        return $mentor
            ? $mentor->stages()->with('student.user', 'company')->first()
            : null;
    }

    public function render()
    {
        $stage = $this->currentStage();

        // Reeds ingediende mentor-evaluaties van deze stage.
        $ingediend = $stage
            ? $stage->evaluations()
                ->where('evaluator_role', 'mentor')
                ->where('status', 'submitted')
                ->get()
            : collect();

        // Welke types zijn al ingediend?
        $ingediendeTypes = $ingediend->pluck('type')->all();

        // "Te doen" = de types die nog niet ingediend zijn.
        $teDoen = collect(self::TYPES)
            ->reject(fn ($label, $type) => in_array($type, $ingediendeTypes, true))
            ->map(fn ($label, $type) => ['type' => $type, 'label' => $label]);

        return view('livewire.mentor.evaluation-list', [
            'stage' => $stage,
            'teDoen' => $teDoen,
            'ingediend' => $ingediend,
            'teDoenCount' => $teDoen->count(),
        ]);
    }
}
