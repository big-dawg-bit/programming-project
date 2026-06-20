<?php

namespace App\Livewire\Applications;

use App\Models\CompetencyFramework;
use App\Models\StageAgreement;
use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
class ReviewQueue extends Component
{
    use WithPagination;

    public array $feedback = [];

    public function render()
    {
        return view('livewire.applications.review-queue', [
            'applications' => StageApplication::where('status', 'submitted')
                ->where('company_status', 'accepted')   // enkel na akkoord van het bedrijf
                ->with(['student.user', 'company'])
                ->latest('submitted_at')
                ->paginate(15),
        ]);
    }

    public function approve(int $id): void
    {
        $application = StageApplication::with('student')->findOrFail($id);

        // 1. leg de goedkeuring vast
        $application->reviews()->create([
            'reviewer_id' => auth()->id(),
            'decision' => 'approved',
            'reviewed_at' => now(),
        ]);

        // 2. status op goedgekeurd
        $application->update(['status' => 'approved']);

        // 3. automatisch het actieve framework (passend bij de opleiding, anders algemeen)
        $studyProgram = $application->student?->study_program;
        $framework = CompetencyFramework::where('is_active', true)
            ->where(function ($q) use ($studyProgram) {
                $q->where('study_program', $studyProgram)->orWhereNull('study_program');
            })
            ->orderByRaw('study_program is null')   // specifieke match eerst
            ->first();

        // 4. de stage aanmaken — geen mentor (bedrijf koppelt later via Mentor toewijzen)
        $application->stage()->firstOrCreate([], [
            'student_id' => $application->student_id,
            'company_id' => $application->company_id,
            'framework_id' => $framework?->id,
            'start_date' => $application->start_date,
            'end_date' => $application->end_date,
        ]);

        // 5. overeenkomst klaarzetten voor student + bedrijf
        StageAgreement::firstOrCreate(
            ['application_id' => $application->id],
            ['status' => 'te_ondertekenen'],
        );
    }

    public function requestChanges(int $id): void
    {
        $feedback = trim($this->feedback[$id] ?? '');
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
