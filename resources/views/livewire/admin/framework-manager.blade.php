<div>
    <h1>Evaluatiekader beheren</h1>
    <p>Totaal gewicht: <strong>{{ $totalWeight }}</strong> / 100</p>

    <table>
        <thead>
        <tr>
            <th>Code</th>
            <th>Titel</th>
            <th>Gewicht</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($competencies as $competency)
            <tr>
                <td>{{ $competency->code }}</td>
                <td>{{ $competency->title }}</td>
                <td>{{ $competency->weight }}</td>
                <td>
                    <button wire:click="deleteCompetency({{ $competency->id }})">
                        Verwijderen
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <h2>Competentie toevoegen</h2>
    <input type="text" wire:model="code" placeholder="Code (bv. TC)">
    @error('code') <span>{{ $message }}</span> @enderror

    <input type="text" wire:model="title" placeholder="Titel">
    @error('title') <span>{{ $message }}</span> @enderror

    <input type="number" wire:model="weight" placeholder="Gewicht">
    @error('weight') <span>{{ $message }}</span> @enderror

    <button wire:click="addCompetency">Toevoegen</button>
</div>
