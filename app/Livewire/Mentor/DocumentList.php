<?php

namespace App\Livewire\Mentor;

use App\Models\File;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Documenten')]
class DocumentList extends Component
{
    // De stage (= student) waarvan de documenten worden bekeken (null = lijst).
    public ?int $stageId = null;

    public function openStudent(int $stageId): void
    {
        $this->stageId = $stageId;
    }

    public function closeStudent(): void
    {
        $this->stageId = null;
    }

    /** De stages van de ingelogde mentor (basis voor alle scoping). */
    private function stagesQuery()
    {
        $mentor = Auth::user()?->mentor;

        return $mentor ? $mentor->stages() : null;
    }

    /**
     * Download een eigen document van één van de eigen stagiairs.
     * Een mentor kan enkel bestanden ophalen van studenten die hij begeleidt.
     */
    public function download(int $fileId)
    {
        $query = $this->stagesQuery();
        abort_if($query === null, 403);

        $studentUserIds = $query->with('student.user')->get()
            ->pluck('student.user.id')
            ->filter()
            ->all();

        $file = File::whereIn('uploaded_by', $studentUserIds)->findOrFail($fileId);

        return Storage::download($file->storage_path, $file->original_name);
    }

    public function render()
    {
        $query = $this->stagesQuery();

        // Lijst van stagiairs (nieuw → oud).
        $stages = $query
            ? $query->with(['student.user', 'company'])->latest()->get()
            : collect();

        // Detail van één geselecteerde stagiair.
        $selected = null;
        $application = null;
        $weeklogs = collect();
        $files = collect();

        if ($query && $this->stageId) {
            /** @var Stage|null $selected */
            $selected = $query->with([
                'student.user', 'company',
                'application.company', 'application.agreement',
                'application.student.user', 'application.stage.mentor.user', 'application.stage.docent.user',
            ])->find($this->stageId);

            if ($selected) {
                $application = $selected->application;
                $weeklogs = $selected->weeklogs()->orderByDesc('week_number')->get();

                $studentUserId = $selected->student?->user?->id;
                $files = $studentUserId
                    ? File::where('uploaded_by', $studentUserId)->orderByDesc('uploaded_at')->get()
                    : collect();
            }
        }

        return view('livewire.mentor.documents', [
            'stages' => $stages,
            'selected' => $selected,
            'application' => $application,
            'weeklogs' => $weeklogs,
            'files' => $files,
        ]);
    }
}
