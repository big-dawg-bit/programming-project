<?php

namespace App\Livewire\Weeklogs;

use App\Models\Stage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Weeklogboeken')]
class WeeklogList extends Component
{
    public function render()
    {
        // Voorlopig nemen we de eerste stage (testdata uit StageSeeder).
        // Later wordt dit de stage van de ingelogde student.
        $stage = Stage::with('student.user')->first();

        $weeklogs = $stage
            ? $stage->weeklogs()->orderBy('week_number')->get()
            : collect();

        return view('livewire.weeklogs.weeklog-list', [
            'stage' => $stage,
            'weeklogs' => $weeklogs,
        ]);
    }
}
