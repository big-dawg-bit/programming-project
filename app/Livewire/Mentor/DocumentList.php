<?php

namespace App\Livewire\Mentor;

use App\Models\File;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Documenten')]
class DocumentList extends Component
{
    // Actieve documentcategorie.
    public string $categorie = 'Stage-aanvraag';

    public const CATEGORIEEN = [
        'Stage-aanvraag',
        'Overeenkomst',
        'Logboeken',
        'Evaluaties',
        'Anders',
    ];

    public function render()
    {
        $files = File::where('category', $this->categorie)
            ->orderByDesc('uploaded_at')
            ->get();

        return view('livewire.mentor.documents', [
            'files' => $files,
        ]);
    }
}
