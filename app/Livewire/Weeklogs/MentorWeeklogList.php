<?php

namespace App\Livewire\Weeklogs;

use App\Models\Mentor;
use App\Models\Weeklog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Weeklogs')]
class MentorWeeklogList extends Component
{
    // Actieve statusfilter (alle | ingediend | goedgekeurd | aanpassing).
    public string $filter = 'te beoordelen';

    // Welk logboek zijn comment-thread open heeft staan (null = geen).
    public ?int $openWeeklogId = null;

    // Tekst van een nieuwe reactie.
    public string $newComment = '';

    /**
     * De ingelogde mentor.
     */
    private function mentor(): ?Mentor
    {
        return Mentor::where('user_id', Auth::id())->first();
    }

    /**
     * Alle stage-id's van de ingelogde mentor (kan meerdere stagiairs hebben).
     */
    private function stageIds(): \Illuminate\Support\Collection
    {
        return $this->mentor()?->stages()->pluck('id') ?? collect();
    }

    /**
     * Haal een weeklog op, maar enkel binnen de eigen stages van de mentor.
     */
    private function ownedWeeklog(int $weeklogId): Weeklog
    {
        return Weeklog::whereIn('stage_id', $this->stageIds())->findOrFail($weeklogId);
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
     * Voeg een reactie toe aan een logboek (door de ingelogde mentor).
     */
    public function addComment(int $weeklogId): void
    {
        $this->validate(['newComment' => 'required|string|min:2']);

        $weeklog = $this->ownedWeeklog($weeklogId);

        $weeklog->comments()->create([
            'author_id' => Auth::id(),
            'comment' => $this->newComment,
        ]);

        $this->reset('newComment');
    }

    /**
     * Keur een weeklog goed.
     */
        public function approve(int $weeklogId): void
    {
        $weeklog = $this->ownedWeeklog($weeklogId);
        $weeklog->update(['status' => 'goedgekeurd']);

        $this->openWeeklogId = null;

        session()->flash('weeklog-saved', 'Weeklog goedgekeurd.');
    }


    /**
     * Vraag aanpassingen aan de student.
     */
    public function requestChanges(int $weeklogId): void
    {
        $weeklog = $this->ownedWeeklog($weeklogId);
        $weeklog->update(['status' => 'aanpassing_gevraagd']);

        $this->openWeeklogId = null;

        session()->flash('weeklog-saved', 'Aanpassing gevraagd aan de student.');
    }

    /**
     * Welke databasestatussen onder elke filterpill vallen.
     */
    public const FILTERS = [
        'te beoordelen' => ['ingediend'],
        'goedgekeurd' => ['goedgekeurd', 'gevalideerd'],
        'alle weken' => null,
    ];

    public function render()
    {
        // Weeklogs van ALLE stages van de mentor (kan meerdere stagiairs hebben).
        $stageIds = $this->stageIds();

        $query = $stageIds->isNotEmpty()
            ? Weeklog::whereIn('stage_id', $stageIds)
                ->with(['comments.author', 'stage.student.user'])
                ->orderByDesc('submitted_at')
                ->orderByDesc('week_number')
            : null;

        // Aantal weeklogs dat nog beoordeeld moet worden (voor de teller op de tab).
        $teBeoordelenCount = $stageIds->isNotEmpty()
            ? Weeklog::whereIn('stage_id', $stageIds)->where('status', 'ingediend')->count()
            : 0;

        if ($query && ($statuses = self::FILTERS[$this->filter] ?? null)) {
            $query->whereIn('status', $statuses);
        }

        return view('livewire.weeklogs.mentor-weeklog-list', [
            'hasStages' => $stageIds->isNotEmpty(),
            'weeklogs' => $query ? $query->get() : collect(),
            'teBeoordelenCount' => $teBeoordelenCount,
        ]);
    }
}
