<?php

namespace App\Livewire\Bedrijf;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Logboeken')]
class LogboekList extends Component
{
    public ?int $openStageId = null;

    public function toggle(int $stageId): void
    {
        $this->openStageId = $this->openStageId === $stageId ? null : $stageId;
    }

    public function render()
    {
        $company = Company::where('user_id', Auth::id())->first();

        $stages = $company
            ? $company->stages()
                ->with(['student.user', 'weeklogs' => fn ($q) => $q->orderByDesc('week_number')])
                ->get()
            : collect();

        return view('livewire.bedrijf.logboek-list', [
            'company' => $company,
            'stages' => $stages,
        ]);
    }
}
