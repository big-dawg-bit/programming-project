<?php

namespace App\Livewire\Bedrijf;

use App\Models\Company;
use App\Models\Weeklog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Logboeken')]
class LogboekList extends Component
{
    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $weeklogs = $company
            ? Weeklog::whereIn('stage_id', $company->stages()->pluck('id'))
                ->with('stage.student.user')
                ->orderByDesc('week_number')
                ->get()
            : collect();

        return view('livewire.bedrijf.logboek-list', [
            'company' => $company,
            'weeklogs' => $weeklogs,
        ]);
    }
}
