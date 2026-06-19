<?php

namespace App\Livewire\Applications;

use App\Models\CompetencyFramework;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
class ReviewQueue extends Component
{
    use WithPagination;

    public array $feedback = [];

    // Per-rij toewijzing bij goedkeuring (key = application id).

    public array $mentorId = [];
    public array $frameworkId = [];

    public function render()
    {
        return view('livewire.applications.review-queue', [
            'applications' => StageApplication::where('status', 'submitted')
                ->with(['student.user', 'company'])
                ->latest('submitted_at')
                ->paginate(15),
            'docenten' => Docent::with('user')->get(),
            'frameworks' => CompetencyFramework::where('is_active', true)->orderBy('name')->get(),
            // Alle mentors zijn toewijsbaar (met hun bedrijf in het label), zodat de
            // commissie ook een mentor kan kiezen voor een bedrijf zonder eigen mentor.
            'mentors' => Mentor::with(['user', 'company'])->get()
                ->sortBy(fn ($m) => $m->user?->name)
                ->values(),
        ]);
    }

    public function approve(int $id): void
    {
        $application = StageApplication::findOrFail($id);

        $mentorId = $this->mentorId[$id] ?? null;
        $frameworkId = $this->frameworkId[$id] ?? null;

        // Alle drie zijn verplicht: de stage moet meteen evalueerbaar zijn.
        $missing = false;
        if (! $mentorId) {
            $this->addError("mentorId.{$id}", 'Kies een mentor.');
            $missing = true;
        }
        if (! $frameworkId) {
            $this->addError("frameworkId.{$id}", 'Kies een evaluatiekader.');
            $missing = true;
        }
        if ($missing) {
            return;
        }

        // 1. leg vast wie goedkeurde en wanneer
        $application->reviews()->create([
            'reviewer_id' => auth()->id(),
            'decision' => 'approved',
            'reviewed_at' => now(),
        ]);

        // 2. zet de status op goedgekeurd
        $application->update(['status' => 'approved']);

        // 3. maak de échte stage aan (firstOrCreate = nooit dubbel) met de toewijzing
        $application->stage()->firstOrCreate([], [
            'student_id' => $application->student_id,
            'company_id' => $application->company_id,
            'mentor_id' => (int) $mentorId,
            'framework_id' => (int) $frameworkId,
            'start_date' => $application->start_date,
            'end_date' => $application->end_date,
        ]);

        unset($this->mentorId[$id], $this->frameworkId[$id]);
    }

    public function requestChanges(int $id): void
    {
        $feedback = trim($this->feedback[$id] ?? '');

        // feedback is verplicht bij "aanpassingen vereist"
        if ($feedback === '') {
            $this->addError("feedback.{$id}", 'Geef feedback mee voor de student.');
            return;
        }

        $application = StageApplication::findOrFail($id);

        $application->reviews()->create([
            'reviewer_id' => auth()->id(),
            'decision' => 'changes_requested',
            'feedback' => $feedback,
            'reviewed_at' => now(),
        ]);

        $application->update(['status' => 'changes_requested']);

        unset($this->feedback[$id]);
    }

    public function reject(int $id): void
    {
        $application = StageApplication::findOrFail($id);

        $application->reviews()->create([
            'reviewer_id' => auth()->id(),
            'decision' => 'rejected',
            'feedback' => $this->feedback[$id] ?? null,
            'reviewed_at' => now(),
        ]);

        $application->update(['status' => 'rejected']);

        unset($this->feedback[$id]);
    }
}
