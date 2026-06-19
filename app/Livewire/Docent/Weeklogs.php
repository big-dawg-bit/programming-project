<?php

namespace App\Livewire\Docent;

use App\Models\Weeklog;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Weeklogs')]
class Weeklogs extends Component
{
    // Welk logboek zijn reactie-thread open heeft staan (null = geen).
    public ?int $openWeeklogId = null;

    // Tekst van een nieuwe reactie.
    public string $newComment = '';

    /** Id's van de stages die deze docent begeleidt. */
    private function stageIds()
    {
        return auth()->user()?->docent?->stages()->pluck('id') ?? collect();
    }

    public function toggleComments(int $weeklogId): void
    {
        $this->openWeeklogId = $this->openWeeklogId === $weeklogId ? null : $weeklogId;
        $this->reset('newComment');
        $this->resetValidation();
    }

    /**
     * De docent reageert op een weeklog (keurt niet goed/af).
     */
    public function addComment(int $weeklogId): void
    {
        $this->validate(['newComment' => 'required|string|min:2']);

        // Scoping: enkel weeklogs van de eigen begeleide stages.
        $weeklog = Weeklog::whereIn('stage_id', $this->stageIds())->findOrFail($weeklogId);

        $weeklog->comments()->create([
            'author_id' => Auth::id(),
            'comment' => $this->newComment,
        ]);

        $this->reset('newComment');
    }

    public function render()
    {
        $weeklogs = Weeklog::whereIn('stage_id', $this->stageIds())
            ->whereNotNull('submitted_at')
            ->with(['stage.student.user', 'comments.author'])
            ->orderByDesc('submitted_at')
            ->get();

        return view('livewire.docent.weeklogs', [
            'weeklogs' => $weeklogs,
        ]);
    }
}
