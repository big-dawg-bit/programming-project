<div class="mx-auto flex max-w-4xl flex-col gap-6">

    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-neutral-900">Evaluatiekader beheren</h1>
            <p class="mt-1 text-sm text-neutral-500">
                {{ $framework?->name ?? 'Evaluatiekader' }} — beheer competenties, gewichten en omschrijving.
            </p>
        </div>
        @php $gewichtOk = $totalWeight === 100; @endphp
        <span class="rounded-lg px-4 py-2 text-sm font-semibold ring-1 ring-inset {{ $gewichtOk ? 'bg-green-50 text-green-700 ring-green-200' : 'bg-amber-50 text-amber-700 ring-amber-200' }}">
            Totaal gewicht: {{ $totalWeight }} / 100
            @unless ($gewichtOk) — moet 100 zijn @endunless
        </span>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Versiebeheer --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-5">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-neutral-700">Versie:</span>
                @foreach ($frameworks as $fw)
                    <button wire:click="selectFramework({{ $fw->id }})"
                        @class([
                            'rounded-lg px-3 py-1.5 text-sm font-medium ring-1 ring-inset transition',
                            'bg-[#E2231A]/5 text-[#E2231A] ring-[#E2231A]/30' => $fw->id === $framework?->id,
                            'text-neutral-600 ring-neutral-200 hover:bg-neutral-50' => $fw->id !== $framework?->id,
                        ])>
                        v{{ $fw->version }}@if ($fw->is_active) · actief @endif
                    </button>
                @endforeach
            </div>
            <div class="flex items-center gap-2">
                @if (! $framework?->is_active)
                    <button wire:click="activate({{ $framework?->id }})"
                            class="rounded-lg bg-[#E2231A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#c41e16]">
                        Deze versie activeren
                    </button>
                @else
                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-200">
                        Actieve versie
                    </span>
                @endif
                <button wire:click="createVersion"
                        class="rounded-lg border border-neutral-300 px-4 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50">
                    + Nieuwe versie
                </button>
            </div>
        </div>
        @unless ($framework?->is_active)
            <p class="mt-3 text-xs text-neutral-500">
                Je bewerkt een conceptversie. Wijzigingen hier raken bestaande evaluaties niet — die bewaren hun eigen gewicht-snapshot.
            </p>
        @endunless
    </div>

    {{-- Competentielijst met inline bewerken --}}
    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                <tr>
                    <th class="px-4 py-3 w-24">Code</th>
                    <th class="px-4 py-3">Titel &amp; omschrijving</th>
                    <th class="px-4 py-3 w-24">Gewicht</th>
                    <th class="px-4 py-3 w-40"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($competencies as $competency)
                    <tr wire:key="comp-{{ $competency->id }}">
                        @if ($editId === $competency->id)
                            {{-- Bewerk-modus --}}
                            <td class="px-4 py-3 align-top">
                                <input type="text" wire:model="editCode"
                                       class="w-20 rounded-lg border border-neutral-300 px-2 py-1.5 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text" wire:model="editTitle"
                                       class="w-full rounded-lg border border-neutral-300 px-2 py-1.5 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                                @error('editTitle') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
                                <textarea wire:model="editDescription" rows="2" placeholder="Omschrijving"
                                          class="mt-2 w-full resize-none rounded-lg border border-neutral-300 px-2 py-1.5 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none"></textarea>
                            </td>
                            <td class="px-4 py-3 align-top">
                                <input type="number" wire:model="editWeight" min="0" max="100"
                                       class="w-20 rounded-lg border border-neutral-300 px-2 py-1.5 text-right text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                                @error('editWeight') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex gap-2">
                                    <button wire:click="saveEdit"
                                            class="rounded-lg bg-[#E2231A] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[#c41e16]">
                                        Opslaan
                                    </button>
                                    <button wire:click="cancelEdit"
                                            class="rounded-lg border border-neutral-300 px-3 py-1.5 text-xs font-medium text-neutral-700 hover:bg-neutral-50">
                                        Annuleer
                                    </button>
                                </div>
                            </td>
                        @else
                            {{-- Weergave --}}
                            <td class="px-4 py-3 align-top font-mono text-xs text-neutral-500">{{ $competency->code ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-neutral-900">{{ $competency->title }}</p>
                                @if ($competency->description)
                                    <p class="mt-0.5 text-xs text-neutral-500">{{ $competency->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top font-semibold text-neutral-900">{{ $competency->weight }}</td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex gap-3 text-sm">
                                    <button wire:click="startEdit({{ $competency->id }})"
                                            class="font-medium text-[#E2231A] hover:underline">Bewerken</button>
                                    <button wire:click="deleteCompetency({{ $competency->id }})"
                                            class="font-medium text-neutral-500 hover:underline">Verwijderen</button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-neutral-500">
                            Nog geen competenties. Voeg er hieronder een toe.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Nieuwe competentie toevoegen --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-neutral-900">Competentie toevoegen</h2>

        <div class="grid gap-4 sm:grid-cols-[7rem_1fr_7rem]">
            <div>
                <input type="text" wire:model="code" placeholder="Code"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                @error('code') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="text" wire:model="title" placeholder="Titel"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                @error('title') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
            </div>
            <div>
                <input type="number" wire:model="weight" min="0" max="100" placeholder="Gewicht"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-right text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                @error('weight') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
            </div>
        </div>

        <textarea wire:model="description" rows="2" placeholder="Omschrijving (optioneel)"
                  class="mt-4 w-full resize-none rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none"></textarea>
        @error('description') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror

        <div class="mt-4 flex justify-end">
            <button wire:click="addCompetency"
                    class="rounded-lg bg-[#E2231A] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#c41e16] focus:ring-2 focus:ring-[#E2231A]/40 focus:outline-none">
                Toevoegen
            </button>
        </div>
    </div>
</div>
