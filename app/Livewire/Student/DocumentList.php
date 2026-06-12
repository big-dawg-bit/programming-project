<?php

namespace App\Livewire\Student;

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
        return view('livewire.student.documents');
    }
}
