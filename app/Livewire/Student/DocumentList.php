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

    // Actieve documentcategorie.
    public string $categorie = 'Stage-aanvraag';

    // Of de upload-modal open staat.
    public bool $showUpload = false;

    // Het gekozen bestand: documenten (PDF/DOCX) of foto's voor de logboeken, max 10 MB.
    #[Validate('required|file|mimes:pdf,docx,jpg,jpeg,png,webp|max:10240')]
    public $upload = null;

    // Categorie waaronder het bestand wordt opgeslagen.
    #[Validate('required|string')]
    public string $uploadCategorie = 'Stage-aanvraag';

    #[Validate('nullable|string|max:500')]
    public string $beschrijving = '';

    public const CATEGORIEEN = [
        'Stage-aanvraag',
        'Stageovereenkomst',
        'Logboeken',
    ];

    /**
     * Open de modal; de categorie-select start op de actieve categorie.
     */
    public function openUpload(): void
    {
        $this->uploadCategorie = $this->categorie;
        $this->showUpload = true;
    }

    public function closeUpload(): void
    {
        $this->showUpload = false;
        $this->reset(['upload', 'beschrijving']);
        $this->resetValidation();
    }

    /**
     * Sla het bestand op schijf op en registreer het in de files-tabel.
     */
    public function save(): void
    {
        $this->validate();

        if (! in_array($this->uploadCategorie, self::CATEGORIEEN, true)) {
            $this->addError('uploadCategorie', 'Ongeldige categorie.');

            return;
        }

        // Metadata uitlezen vóór store(): die verplaatst het tijdelijke bestand,
        // waarna getSize()/getMimeType() niet meer werken.
        $originalName = $this->upload->getClientOriginalName();
        $mimeType = $this->upload->getMimeType();
        $sizeBytes = $this->upload->getSize();

        $path = $this->upload->store('documenten');

        File::create([
            'original_name' => $originalName,
            'storage_path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'category' => $this->uploadCategorie,
            'description' => $this->beschrijving ?: null,
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        // Spring naar de categorie waarin het bestand terechtkwam.
        $this->categorie = $this->uploadCategorie;
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
        // Enkel de eigen documenten van de ingelogde student; student A ziet nooit die van B.
        $files = File::where('uploaded_by', Auth::id())
            ->where('category', $this->categorie)
            ->orderByDesc('uploaded_at')
            ->get();

        return view('livewire.student.documents', [
            'files' => $files,
        ]);
    }
}
