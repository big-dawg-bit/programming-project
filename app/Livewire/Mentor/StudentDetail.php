<?php

namespace App\Livewire\Mentor;

use App\Models\Mentor;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Student')]
class StudentDetail extends Component
{
    public Stage $stage;

    // Actieve tab: 'weeklogs' | 'evaluaties' | 'documenten'.
    public string $tab = 'weeklogs';

    public function mount(Stage $stage): void
    {
        // Veiligheid: alleen de mentor van deze stage mag de pagina zien.
        $mentor = Mentor::where('user_id', Auth::id())->first();

        abort_unless($mentor && $stage->mentor_id === $mentor->id, 403);

        $this->stage = $stage->load('student.user', 'company');
    }

    public function render()
    {
        // Stats voor de 4 kaarten.
        $totaal = $this->stage->weeklogs()->count();
        $goedgekeurd = $this->stage->weeklogs()->whereIn('status', ['goedgekeurd', 'gevalideerd'])->count();
        $teBeoordelen = $this->stage->weeklogs()->where('status', 'ingediend')->count();

        // Week X van Y.
        $huidigeWeek = null;
        $totaalWeken = null;
        if ($this->stage->start_date && $this->stage->end_date) {
            $start = \Illuminate\Support\Carbon::parse($this->stage->start_date);
            $eind = \Illuminate\Support\Carbon::parse($this->stage->end_date);
            $totaalWeken = max(1, (int) ceil($start->diffInDays($eind) / 7));
            $huidigeWeek = max(1, min((int) floor($start->diffInDays(now(), false) / 7) + 1, $totaalWeken));
        }

        // Data per tab.
        $weeklogs = $this->tab === 'weeklogs'
            ? $this->stage->weeklogs()->orderByDesc('week_number')->get()
            : collect();

        $evaluaties = $this->tab === 'evaluaties'
            ? $this->stage->evaluations()->where('evaluator_role', 'mentor')->where('status', 'submitted')->get()
            : collect();

        return view('livewire.mentor.student-detail', [
            'totaal' => $totaal,
            'goedgekeurd' => $goedgekeurd,
            'teBeoordelen' => $teBeoordelen,
            'huidigeWeek' => $huidigeWeek,
            'totaalWeken' => $totaalWeken,
            'weeklogs' => $weeklogs,
            'evaluaties' => $evaluaties,
        ]);
    }
}
