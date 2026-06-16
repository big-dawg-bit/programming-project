<?php

namespace App\Livewire\Weeklogs;

use App\Models\File;
use App\Models\FinalReport;
use App\Models\Stage;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.portal')]
#[Title('Eindrapport')]
class FinalReportUpload extends Component
{
    use WithFileUploads;

    // Het geüploade bestand (tijdelijk, tot we het opslaan).
    public $report;

    // Korte samenvatting bij het rapport.
    public string $summary = '';

    /**
     * Voorlopig de eerste stage (testdata). Later: stage van de ingelogde student.
     */
    private function currentStage(): ?Stage
    {
        return Stage::with(['student.user', 'finalReport.file'])->first();
    }

    /**
     * Sla het eindrapport op: bestand bewaren, File-record maken, FinalReport koppelen.
     */
    public function save(): void
    {
        $stage = $this->currentStage();

        if (! $stage) {
            return;
        }

        $this->validate([
            'report' => 'required|file|mimes:pdf,doc,docx|max:10240', // max 10 MB
            'summary' => 'nullable|string|max:2000',
        ]);

        // Bestand op de schijf bewaren.
        $path = $this->report->store('final-reports');

        // File-record aanmaken (zoals in Arnaud's datamodel).
        $file = File::create([
            'original_name' => $this->report->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $this->report->getMimeType(),
            'size_bytes' => $this->report->getSize(),
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        // Eindrapport koppelen (één per stage; bestaat het al, dan bijwerken).
        FinalReport::updateOrCreate(
            ['stage_id' => $stage->id],
            [
                'file_id' => $file->id,
                'summary' => $this->summary,
                'submitted_at' => now(),
            ]
        );

        $this->reset('report');

        session()->flash('report-saved', 'Eindrapport geüpload.');
    }

    public function render()
    {
        $stage = $this->currentStage();

        return view('livewire.weeklogs.final-report-upload', [
            'stage' => $stage,
            'finalReport' => $stage?->finalReport,
        ]);
    }
}
