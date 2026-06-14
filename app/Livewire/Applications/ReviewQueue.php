<?php

namespace App\Livewire\Applications;

use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
class ReviewQueue extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.applications.review-queue', [
            'applications' => StageApplication::where('status', 'submitted')
                ->with(['student.user', 'company'])
                ->latest('submitted_at')
                ->paginate(15),
        ]);
    }

    public function approve(int $id): void
    {
        $application = StageApplication::findOrFail($id);

        // 1. leg vast wie goedkeurde en wanneer
        $application->reviews()->create([
            'reviewer_id' => auth()->id(),
            'decision' => 'approved',
            'reviewed_at' => now(),
        ]);

        // 2. zet de status op goedgekeurd
        $application->update(['status' => 'approved']);

        // 3. maak de échte stage aan (firstOrCreate = nooit dubbel)
        $application->stage()->firstOrCreate([], [
            'student_id' => $application->student_id,
            'company_id' => $application->company_id,
            'start_date' => $application->start_date,
            'end_date' => $application->end_date,
        ]);
    }
}
