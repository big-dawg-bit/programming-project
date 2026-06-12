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
    // Actieve statusfilter (alle | concept | ingediend | goedgekeurd | aanpassing).
    public string $filter = 'alle';

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

    /**
     * Welke databasestatussen onder elke filterpill vallen.
     * (De status is een vrije string; 'draft' komt uit de migration-default.)
     */
    public const FILTERS = [
        'alle' => null,
        'concept' => ['draft', 'concept'],
        'ingediend' => ['ingediend'],
        'goedgekeurd' => ['goedgekeurd', 'gevalideerd'],
        'aanpassing' => ['aanpassing', 'aanpassing_gevraagd'],
    ];

    public function render()
    {
        $stage = $this->currentStage();

        $query = $stage
            ? $stage->weeklogs()->with('comments.author')->orderByDesc('week_number')
            : null;

        if ($query && ($statuses = self::FILTERS[$this->filter] ?? null)) {
            $query->whereIn('status', $statuses);
        }

        return view('livewire.weeklogs.weeklog-list', [
            'stage' => $stage,
            'weeklogs' => $query ? $query->get() : collect(),
        ]);
    }
}
