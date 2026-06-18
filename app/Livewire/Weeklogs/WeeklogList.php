<?php

namespace App\Livewire\Weeklogs;

use App\Models\Stage;
use App\Models\Weeklog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Mijn weeklogs')]
class WeeklogList extends Component
{
    // Of het invulformulier zichtbaar is.
    public bool $showForm = false;

    // Welk logboek zijn comment-thread open heeft staan (null = geen).
    public ?int $openWeeklogId = null;

    // Tekst van een nieuwe reactie.
    public string $newComment = '';

    // Formuliervelden voor een nieuw logboek.
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
     * De (laatste) stage van de ingelogde student. Student A ziet nooit die van B.
     */
    private function currentStage(): ?Stage
    {
        return Auth::user()?->student?->stages()->with('student.user')->latest()->first();
    }

    /**
     * Stel bij het openen het eerstvolgende, nog niet ingevulde weeknummer voor.
     */
    public function mount(): void
    {
        $stage = $this->currentStage();

        if ($stage) {
            $this->week_number = max(1, (int) $stage->weeklogs()->max('week_number') + 1);
        }
    }

    /**
     * Sla een nieuw weeklogboek op voor de stage. Maximaal één logboek per week.
     */
    public function save(): void
    {
        $stage = $this->currentStage();

        if (! $stage) {
            return;
        }

        $validated = $this->validate();

        // Eén weeklog per week: dubbele weeknummers blokkeren.
        if ($stage->weeklogs()->where('week_number', $validated['week_number'])->exists()) {
            $this->addError('week_number', "Er bestaat al een weeklog voor week {$validated['week_number']}.");

            return;
        }

        $stage->weeklogs()->create([
            ...$validated,
            'status' => 'ingediend',
            'submitted_at' => now(),
        ]);

        $this->reset(['period_start', 'period_end', 'content', 'hours_worked']);
        $this->week_number++;
        $this->showForm = false;

        session()->flash('weeklog-saved', 'Logboek opgeslagen.');
    }

    /**
     * Open/sluit de comment-thread van een logboek.
     */
    public function toggleComments(int $weeklogId): void
    {
        $this->openWeeklogId = $this->openWeeklogId === $weeklogId ? null : $weeklogId;
        $this->reset('newComment');
        $this->resetValidation();
    }

    /**
     * Voeg een reactie toe aan een logboek (door de ingelogde gebruiker).
     */
    public function addComment(int $weeklogId): void
    {
        $this->validate(['newComment' => 'required|string|min:2']);

        $weeklog = Weeklog::findOrFail($weeklogId);

        $weeklog->comments()->create([
            'author_id' => Auth::id(),
            'comment' => $this->newComment,
        ]);

        $this->reset('newComment');
    }

    public function render()
    {
        // Een weeklog wordt meteen ingediend; de docent reageert erop maar keurt
        // niet goed/af. Er is dus geen statusfilter — alle weeklogs worden getoond.
        $stage = $this->currentStage();

        $weeklogs = $stage
            ? $stage->weeklogs()->with('comments.author')->orderByDesc('week_number')->get()
            : collect();

        return view('livewire.weeklogs.weeklog-list', [
            'stage' => $stage,
            'weeklogs' => $weeklogs,
        ]);
    }
}
