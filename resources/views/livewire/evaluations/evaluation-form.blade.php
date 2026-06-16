<div>
    <h1>Evaluatie ({{ $type }})</h1>

    @foreach ($competencies as $competency)
        <div wire:key="comp-{{ $competency->id }}">
            <label>
                {{ $competency->title }}
                <span>(gewicht: {{ $competency->weight }})</span>
            </label>
            <input
                type="number"
                wire:model="scores.{{ $competency->id }}"
                min="0"
                max="100"
                placeholder="Score 0-100"
            >
        </div>
    @endforeach
</div>
