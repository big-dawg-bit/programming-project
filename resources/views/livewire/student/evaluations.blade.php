<div class="mx-auto flex max-w-5xl flex-col gap-6">
    {{-- Tabs --}}
    <div class="border-b border-neutral-200">
        <nav class="-mb-px flex gap-8">
            @foreach (['tussentijds' => 'Tussentijds', 'eind' => 'Eind'] as $key => $label)
                <button type="button" wire:click="$set('tab', '{{ $key }}')"
                    @class([
                        'border-b-2 pb-3 text-sm font-medium transition',
                        'border-[#E2231A] text-neutral-900' => $tab === $key,
                        'border-transparent text-neutral-500 hover:text-neutral-700' => $tab !== $key,
                    ])>
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- NB: voorlopig statische voorbeeldinhoud; later koppelen aan het Evaluation-model. --}}
    @if ($tab === 'tussentijds')
        <div class="rounded-xl border border-neutral-200 bg-white p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold">Tussentijdse evaluatie</h3>
                    <p class="mt-2 flex items-center gap-1.5 text-sm text-neutral-500">
                        <flux:icon name="calendar" class="size-4" /> 12 april 2026
                    </p>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-neutral-500">
                        <flux:icon name="user" class="size-4" /> Margaux Schodts, Prof. Benoit Bourguignon
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <p class="text-right">
                        <span class="text-3xl font-bold">15</span>
                        <span class="block text-sm text-neutral-400">/20</span>
                    </p>
                    <flux:icon name="arrow-down-tray" class="size-5 text-neutral-400" />
                    <span class="rounded-full bg-neutral-100 px-3 py-1 text-sm font-medium text-neutral-600">Bekeken</span>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
            <flux:heading size="lg">Nog geen eindevaluatie</flux:heading>
            <flux:subheading class="mt-1">
                Je eindevaluatie verschijnt hier zodra je mentor en docent ze hebben ingevuld.
            </flux:subheading>
        </div>
    @endif
</div>
