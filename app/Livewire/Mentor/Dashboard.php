<?php

namespace App\Livewire\Mentor;

use App\Models\StageAgreement;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * Mentor geeft akkoord op de stage: hiermee ontstaat de stageovereenkomst,
     * die de student en docent daarna kunnen ondertekenen.
     */
    public function approve(int $stageId): void
    {
        $mentor = auth()->user()?->mentor;
        abort_unless($mentor !== null, 403);

        // Scoping: enkel een eigen stage met een gekoppelde aanvraag.
        $stage = $mentor->stages()->whereNotNull('application_id')->findOrFail($stageId);

        StageAgreement::firstOrCreate(
            ['application_id' => $stage->application_id],
            [
                'status' => 'te_ondertekenen',
                'mentor_approved_at' => now(),
                'uploaded_by' => auth()->id(),
            ]
        );

        session()->flash('mentor-approved', 'Akkoord gegeven. De student en docent kunnen nu de overeenkomst ondertekenen.');
    }

    public function render()
    {
        // Stages van de ingelogde mentor (stagiairs binnen het bedrijf).
        $mentor = auth()->user()?->mentor;

        $stages = $mentor
            ? $mentor->stages()
                ->with(['student.user', 'company', 'application.agreement'])
                ->withCount('weeklogs')
                ->get()
            : collect();

        return view('livewire.mentor.dashboard', [
            'mentor' => $mentor,
            'stages' => $stages,
        ]);
    }
}
