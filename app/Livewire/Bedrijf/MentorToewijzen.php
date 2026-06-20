<?php

namespace App\Livewire\Bedrijf;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mentor toewijzen')]
class MentorToewijzen extends Component
{
    public function assignMentor(int $stageId, $mentorId): void
    {
        $company = Company::where('user_id', Auth::id())->first();

        if (! $company) {
            return;
        }

        $stage = $company->stages()->find($stageId);

        if (! $stage) {
            return;
        }

        $mentorBelongs = $mentorId && $company->mentors()->whereKey($mentorId)->exists();

        $stage->update(['mentor_id' => $mentorBelongs ? $mentorId : null]);

        session()->flash('bedrijf-status', 'Mentor bijgewerkt.');
    }

    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $stages = $company
            ? $company->stages()->with(['student.user', 'mentor.user'])->get()
            : collect();

        $mentors = $company
            ? $company->mentors()->with('user')->get()
            : collect();

        return view('livewire.bedrijf.mentor-toewijzen', [
            'company' => $company,
            'stages' => $stages,
            'mentors' => $mentors,
        ]);
    }
}
