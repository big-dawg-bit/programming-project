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
}
