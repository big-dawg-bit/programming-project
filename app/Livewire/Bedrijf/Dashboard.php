<?php

namespace App\Livewire\Bedrijf;

use App\Models\Company;
use App\Models\StageAgreement;
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

        $openAgreementsCount = $company
            ? StageAgreement::whereIn('application_id', $company->applications()->pluck('id'))
                ->whereNull('company_signed_at')
                ->count()
            : 0;

        return view('livewire.bedrijf.dashboard', [
            'company' => $company,
            'stages' => $stages,
            'mentorsCount' => $mentorsCount,
            'openAgreementsCount' => $openAgreementsCount,
        ]);
    }
}
