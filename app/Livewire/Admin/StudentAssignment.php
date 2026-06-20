<?php

namespace App\Livewire\Admin;

use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Stage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Toewijzingen')]
class StudentAssignment extends Component
{
    /** Wijs (of verwijder) de begeleidende docent van een stage toe. */
    public function assignDocent(int $stageId, ?int $docentId): void
    {
        Stage::findOrFail($stageId)->update([
            'docent_id' => $docentId ?: null,
        ]);

        session()->flash('success', 'Docent toegewezen.');
    }

    /** Wijs (of verwijder) de begeleidende mentor van een stage toe. */
    public function assignMentor(int $stageId, ?int $mentorId): void
    {
        Stage::findOrFail($stageId)->update([
            'mentor_id' => $mentorId ?: null,
        ]);

        session()->flash('success', 'Mentor toegewezen.');
    }

    public function render()
    {
        return view('livewire.admin.assignments', [
            'stages' => Stage::with(['student.user', 'company', 'docent.user', 'mentor.user'])
                ->latest('id')
                ->get(),
            'docenten' => Docent::with('user')->get(),
            'mentoren' => Mentor::with('user')->get(),
        ]);
    }
}
