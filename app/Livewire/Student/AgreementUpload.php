<?php

namespace App\Livewire\Student;

use App\Models\File;
use App\Models\StageAgreement;
use App\Models\StageApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.portal')]
#[Title('Stageovereenkomst')]
class AgreementUpload extends Component
{
    use WithFileUploads;

    // De getekende overeenkomst (PDF of DOCX, max 10 MB).
    #[Validate('required|file|mimes:pdf,docx|max:10240')]
    public $upload = null;

    // De student bevestigt dat de verzekering in orde is.
    #[Validate('accepted')]
    public bool $insuranceConfirmed = false;

    /** De goedgekeurde aanvraag van de ingelogde student (daar hoort de overeenkomst bij). */
    public function application(): ?StageApplication
    {
        return auth()->user()?->student?->applications()
            ->where('status', 'approved')
            ->latest()
            ->first();
    }

    public function save(): void
    {
        $application = $this->application();
        abort_unless($application !== null, 403);

        $this->validate();

        // Metadata uitlezen vóór store() (die verplaatst het tijdelijke bestand).
        $originalName = $this->upload->getClientOriginalName();
        $mimeType = $this->upload->getMimeType();
        $sizeBytes = $this->upload->getSize();
        $path = $this->upload->store('overeenkomsten');

        $file = File::create([
            'original_name' => $originalName,
            'storage_path' => $path,
            'mime_type' => $mimeType,
            'size_bytes' => $sizeBytes,
            'category' => 'Overeenkomst',
            'uploaded_by' => Auth::id(),
            'uploaded_at' => now(),
        ]);

        // Eén overeenkomst per aanvraag: bestaande bijwerken of nieuwe aanmaken.
        StageAgreement::updateOrCreate(
            ['application_id' => $application->id],
            [
                'file_id' => $file->id,
                'insurance_confirmed' => true,
                'uploaded_by' => Auth::id(),
                'status' => 'ingediend',
                'uploaded_at' => now(),
            ]
        );

        $this->reset('upload', 'insuranceConfirmed');

        session()->flash('success', 'Stageovereenkomst opgeladen. De stagecommissie bevestigt de ondertekening.');
    }

    public function render()
    {
        $application = $this->application();

        return view('livewire.student.agreement-upload', [
            'application' => $application,
            'agreement' => $application?->agreement,
        ]);
    }
}
