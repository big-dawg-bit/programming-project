<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <flux:heading size="xl">Evaluatie</flux:heading>
        <flux:subheading>
            {{ $type === 'final' ? 'Eindevaluatie' : 'Tussentijdse evaluatie' }} —
            vul een score in voor elke competentie.
        </flux:subheading>
    </div>

    <x-form.success-message :message="session('status')" class="mb-4" />

    <div class="space-y-4">
        @foreach ($competencies as $competency)
            <div wire:key="comp-{{ $competency->id }}"
                 class="rounded-xl border border-neutral-200 bg-white p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="font-semibold">{{ $competency->title }}</h3>
                        <p class="mt-0.5 text-sm text-neutral-500">
                            Gewicht: {{ $competency->weight }}%
                        </p>
                    </div>
                    <div class="w-32 shrink-0">
                        <flux:input
                            type="number"
                            wire:model="scores.{{ $competency->id }}"
                            min="0"
                            max="100"
                            placeholder="0–100" />
                    </div>
                </div>
                @error("scores.{$competency->id}")
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
    </div>

    <div class="mt-6 flex justify-end">
        <flux:button variant="primary" wire:click="submit">
            Evaluatie opslaan
        </flux:button>
    </div>
</div>
