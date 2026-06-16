<?php

namespace App\Livewire\Admin;

use App\Models\Competency;
use App\Models\CompetencyFramework;
use Livewire\Component;

class FrameworkManager extends Component
{
    public ?int $frameworkId = null;

    // velden voor een nieuwe competentie
    public string $code = '';
    public string $title = '';
    public int $weight = 0;

    public function mount(): void
    {
        // pak het actieve framework, of maak er een als er nog geen is
        $framework = CompetencyFramework::where('is_active', true)->first()
            ?? CompetencyFramework::create([
                'name' => 'Nieuw evaluatiekader',
                'version' => 1,
                'is_active' => true,
            ]);

        $this->frameworkId = $framework->id;
    }

    public function addCompetency(): void
    {
        $data = $this->validate([
            'title'  => 'required|string|max:255',
            'code'   => 'nullable|string|max:50',
            'weight' => 'required|integer|min:0|max:100',
        ]);

        Competency::create([
            'framework_id' => $this->frameworkId,
            'code'         => $data['code'],
            'title'        => $data['title'],
            'weight'       => $data['weight'],
            'sort_order'   => Competency::where('framework_id', $this->frameworkId)->count() + 1,
        ]);

        $this->reset('code', 'title', 'weight');
    }

    public function updateWeight(int $competencyId, int $weight): void
    {
        Competency::findOrFail($competencyId)->update(['weight' => $weight]);
    }

    public function deleteCompetency(int $competencyId): void
    {
        Competency::findOrFail($competencyId)->delete();
    }

    public function render()
    {
        $framework = CompetencyFramework::with('competencies')->find($this->frameworkId);

        return view('livewire.admin.framework-manager', [
            'framework'   => $framework,
            'competencies' => $framework->competencies()->orderBy('sort_order')->get(),
            'totalWeight' => $framework->competencies()->sum('weight'),
        ]);
    }
}
