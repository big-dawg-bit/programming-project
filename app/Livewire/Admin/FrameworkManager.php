<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Competency;
use App\Models\CompetencyFramework;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Evaluatiekader beheren')]
class FrameworkManager extends Component
{
    // Het kader (versie) dat momenteel bekeken/bewerkt wordt.
    public ?int $frameworkId = null;

    // Velden voor een nieuwe competentie.
    public string $code = '';
    public string $title = '';
    public string $description = '';
    public int $weight = 0;

    // Bewerken van een bestaande competentie (inline).
    public ?int $editId = null;
    public string $editCode = '';
    public string $editTitle = '';
    public string $editDescription = '';
    public int $editWeight = 0;

    public function mount(): void
    {
        // Start bij het actieve kader, of maak er een als er nog geen is.
        $framework = CompetencyFramework::where('is_active', true)->first()
            ?? CompetencyFramework::create([
                'name' => 'Nieuw evaluatiekader',
                'version' => 1,
                'is_active' => true,
            ]);

        $this->frameworkId = $framework->id;
    }

    // --- Versiebeheer ---------------------------------------------------

    /** Wissel naar een andere versie om te bekijken/bewerken. */
    public function selectFramework(int $frameworkId): void
    {
        CompetencyFramework::findOrFail($frameworkId);
        $this->frameworkId = $frameworkId;
        $this->cancelEdit();
    }

    /** Maak een nieuwe versie als kopie van de huidige (competenties inbegrepen, inactief). */
    public function createVersion(): void
    {
        $current = CompetencyFramework::with('competencies')->find($this->frameworkId);

        $new = CompetencyFramework::create([
            'name' => $current?->name ?? 'Evaluatiekader',
            'study_program' => $current?->study_program,
            'version' => (int) (CompetencyFramework::max('version') ?? 0) + 1,
            'is_active' => false,
            'created_by' => auth()->id(),
        ]);

        foreach ($current?->competencies ?? [] as $competency) {
            $new->competencies()->create([
                'code' => $competency->code,
                'title' => $competency->title,
                'description' => $competency->description,
                'weight' => $competency->weight,
                'sort_order' => $competency->sort_order,
            ]);
        }

        // Ga meteen de nieuwe (concept)versie bewerken.
        $this->frameworkId = $new->id;
        $this->cancelEdit();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => "Nieuwe kaderversie (v{$new->version}) aangemaakt",
            'entity_type' => 'CompetencyFramework',
            'entity_id' => $new->id,
        ]);

        session()->flash('success', "Versie {$new->version} aangemaakt als concept. Activeer ze wanneer ze klaar is.");
    }

    /** Activeer een versie; alle andere worden inactief (er is er altijd maar één actief). */
    public function activate(int $frameworkId): void
    {
        // Een evaluatiekader mag pas actief worden als de gewichten samen exact 100 zijn.
        $total = (int) Competency::where('framework_id', $frameworkId)->sum('weight');

        if ($total !== 100) {
            session()->flash('error', "Activeren kan niet: de gewichten moeten samen 100 zijn (nu {$total}).");

            return;
        }

        CompetencyFramework::query()->update(['is_active' => false]);
        CompetencyFramework::whereKey($frameworkId)->update(['is_active' => true]);

        $this->frameworkId = $frameworkId;

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Evaluatiekader geactiveerd',
            'entity_type' => 'CompetencyFramework',
            'entity_id' => $frameworkId,
        ]);

        session()->flash('success', 'Deze versie is nu het actieve evaluatiekader.');
    }

    // --- Competentiebeheer ----------------------------------------------

    public function addCompetency(): void
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'weight' => 'required|integer|min:0|max:100',
        ]);

        $competency = Competency::create([
            'framework_id' => $this->frameworkId,
            'code' => $data['code'],
            'title' => $data['title'],
            'description' => $data['description'] ?: null,
            'weight' => $data['weight'],
            'sort_order' => Competency::where('framework_id', $this->frameworkId)->count() + 1,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Competentie toegevoegd: '.$competency->title,
            'entity_type' => 'Competency',
            'entity_id' => $competency->id,
        ]);

        $this->reset('code', 'title', 'description', 'weight');
    }

    public function startEdit(int $competencyId): void
    {
        $competency = Competency::findOrFail($competencyId);

        $this->editId = $competency->id;
        $this->editCode = $competency->code ?? '';
        $this->editTitle = $competency->title;
        $this->editDescription = $competency->description ?? '';
        $this->editWeight = $competency->weight;
        $this->resetValidation();
    }

    public function saveEdit(): void
    {
        $data = $this->validate([
            'editTitle' => 'required|string|max:255',
            'editCode' => 'nullable|string|max:50',
            'editDescription' => 'nullable|string|max:1000',
            'editWeight' => 'required|integer|min:0|max:100',
        ]);

        $competency = Competency::findOrFail($this->editId);
        $competency->update([
            'code' => $data['editCode'] ?: null,
            'title' => $data['editTitle'],
            'description' => $data['editDescription'] ?: null,
            'weight' => $data['editWeight'],
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Competentie bewerkt: '.$competency->title,
            'entity_type' => 'Competency',
            'entity_id' => $competency->id,
        ]);

        $this->cancelEdit();
    }

    public function cancelEdit(): void
    {
        $this->reset('editId', 'editCode', 'editTitle', 'editDescription', 'editWeight');
        $this->resetValidation();
    }

    public function deleteCompetency(int $competencyId): void
    {
        $competency = Competency::findOrFail($competencyId);
        $title = $competency->title;
        $competency->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Competentie verwijderd: '.$title,
            'entity_type' => 'Competency',
            'entity_id' => $competencyId,
        ]);

        if ($this->editId === $competencyId) {
            $this->cancelEdit();
        }
    }

    public function render()
    {
        $framework = CompetencyFramework::with('competencies')->find($this->frameworkId);

        return view('livewire.admin.framework-manager', [
            'framework' => $framework,
            'frameworks' => CompetencyFramework::orderByDesc('version')->get(),
            'competencies' => $framework->competencies()->orderBy('sort_order')->get(),
            'totalWeight' => (int) $framework->competencies()->sum('weight'),
        ]);
    }
}
