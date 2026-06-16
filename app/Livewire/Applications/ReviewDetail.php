<?php


namespace App\Livewire\Applications;

use App\Models\StageApplication;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.portal')]
class ReviewDetail extends Component
{
    public StageApplication $application;

    public function mount(StageApplication $application): void
    {
        $this->application = $application->load([
            'student.user',
            'company',
            'reviews.reviewer',
        ]);
    }

    public function render()
    {
        return view('livewire.applications.review-detail');
    }
}
