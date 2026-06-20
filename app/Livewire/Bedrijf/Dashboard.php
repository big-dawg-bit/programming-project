<?php

namespace App\Livewire\Bedrijf;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $stages = $company
            ? $company->stages()->with(['student.user', 'mentor.user'])->get()
            : collect();

        $mentorsCount = $company ? $company->mentors()->count() : 0;

        return view('livewire.bedrijf.dashboard', [
            'company' => $company,
            'stages' => $stages,
            'mentorsCount' => $mentorsCount,
        ]);
    }
}
