<?php

namespace App\Livewire\Student;

use App\Models\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.portal')]
#[Title('Documenten')]
class DocumentList extends Component
{
    use WithFileUploads;

    // De tabbladen in de zijbalk. De eerste drie tonen bestaande documenten
    // (leesbaar), het laatste tabblad bevat de eigen geüploade bestanden.
    public const TABS = ['Stageaanvraag', 'Stageovereenkomst', 'Logboeken', 'Eigen documenten'];

    // Soorten voor een eigen upload (vrij in te vullen, vastgelegd in description).
    public const SOORTEN = ['CV', 'Motivatiebrief', 'Andere'];

    public string $tab = 'Stageaanvraag';

    // Of de upload-modal open staat.
    public bool $showUpload = false;

    // Het gekozen bestand: PDF/DOCX of een foto, max 10 MB.
    #[Validate('required|file|mimes:pdf,docx,jpg,jpeg,png,webp|max:10240')]
    public $upload = null;

    // Soort eigen document (CV, motivatiebrief, …).
    #[Validate('required|string|max:100')]
    public string $soort = 'CV';

    public function openUpload(): void
    {
        $this->tab = 'Eigen documenten';
        $this->showUpload = true;
    }

    public function closeUpload(): void
    {
        $this->showUpload = false;
        $this->reset(['upload', 'soort']);
        $this->resetValidation();
    }

    /**
     * Sla een eigen document op. Het krijgt categorie 'Eigen' zodat begeleiders
     * (mentor) het bij de student terugvinden; de soort bewaren we als beschrijving.
     */
    public function save(): void
    {
        $this->validate();

        // Metadata uitlezen vóór store(): die verplaatst het tijdelijke bestand.
        $originalName = $this->upload->getClientOriginalName();
        $mimeType = $this->upload->getMimeType();
        $sizeBytes = $this->upload->getSize();

        $path = $this->upload->store('documenten');

        File::create([
            'original_name' => $originalName,
            'storage_path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'category' => 'Eigen',
            'description' => $this->soort,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        $this->tab = 'Eigen documenten';
        $this->closeUpload();

        session()->flash('document-uploaded', 'Document geüpload.');
    }

    /**
     * Download een eigen bestand. Een student kan enkel zijn eigen uploads ophalen.
     */
    public function download(int $fileId)
    {
        $file = File::where('uploaded_by', Auth::id())->findOrFail($fileId);

        return Storage::download($file->storage_path, $file->original_name);
    }

    public function render()
    {
        $student = Auth::user()?->student;

        // Alle aanvragen van de student (nieuw → oud).
        $applications = $student
            ? $student->applications()->with('company')->latest()->get()
            : collect();

        // De goedgekeurde aanvraag draagt de overeenkomst.
        $approved = $applications->firstWhere('status', 'approved');
        $approved?->loadMissing(['company', 'agreement', 'student.user', 'stage.mentor.user', 'stage.docent.user']);

        // Weeklogs van de (laatste) stage.
        $stage = $student?->stages()->latest()->first();
        $weeklogs = $stage
            ? $stage->weeklogs()->orderByDesc('week_number')->get()
            : collect();

        // Eigen uploads van de student (nieuw → oud).
        $eigenFiles = File::where('uploaded_by', Auth::id())
            ->orderByDesc('uploaded_at')
            ->get();

        return view('livewire.student.documents', [
            'applications' => $applications,
            'approved' => $approved,
            'weeklogs' => $weeklogs,
            'eigenFiles' => $eigenFiles,
        ]);
    }
}
