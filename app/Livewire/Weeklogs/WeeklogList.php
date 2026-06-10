<?php

namespace App\Livewire\Weeklogs;

use App\Models\Stage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Weeklogboeken')]
class WeeklogList extends Component
{
    // Of het invulformulier zichtbaar is.
    public bool $showForm = false;

    // Formuliervelden.
    #[Validate('required|integer|min:1|max:52')]
    public int $week_number = 1;

    #[Validate('nullable|date')]
    public ?string $period_start = null;

    #[Validate('nullable|date|after_or_equal:period_start')]
    public ?string $period_end = null;

    #[Validate('required|string|min:5')]
    public string $content = '';

    #[Validate('nullable|numeric|min:0|max:80')]
    public $hours_worked = null;

    /**
     * Voorlopig de eerste stage (testdata). Later: stage van de ingelogde student.
     */
    private function currentStage(): ?Stage
    {
        return Stage::with('student.user')->first();
    }

    /**
     * Sla een nieuw weeklogboek op voor de stage.
     */
    public function save(): void
    {
        $stage = $this->currentStage();

        if (! $stage) {
            return;
        }

        $validated = $this->validate();

        $stage->weeklogs()->create([
            ...$validated,
            'status' => 'ingediend',
            'submitted_at' => now(),
        ]);

        // Formulier leegmaken en klaarzetten voor de volgende week.
        $this->reset(['period_start', 'period_end', 'content', 'hours_worked']);
        $this->week_number++;
        $this->showForm = false;

        session()->flash('weeklog-saved', 'Logboek opgeslagen.');
    }

    public function render()
    {
        $stage = $this->currentStage();

        $weeklogs = $stage
            ? $stage->weeklogs()->orderBy('week_number')->get()
            : collect();

        return view('livewire.weeklogs.weeklog-list', [
            'stage' => $stage,
            'weeklogs' => $weeklogs,
        ]);
    }
}
