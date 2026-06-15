<x-layouts.portal title="Stage aanvragen">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <flux:heading size="xl">Stage aanvragen</flux:heading>
            <flux:subheading>Vul het formulier in om je stage aan te vragen.</flux:subheading>
        </div>

        <x-form.success-message :message="session('status')" class="mb-4" />

        <div class="space-y-4 rounded-xl border border-neutral-200 bg-white p-6">
            <div>
                <label class="mb-1 block text-sm font-medium">Bedrijf</label>
                <select wire:model="company_id"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                    <option value="">— Kies een bedrijf —</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <flux:input wire:model="position_title" label="Functietitel" />
            @error('position_title') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <flux:textarea wire:model="description" label="Motivatie" rows="4" />
            @error('description') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <flux:input type="date" wire:model="start_date" label="Startdatum" />
                    @error('start_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <flux:input type="date" wire:model="end_date" label="Einddatum" />
                    @error('end_date') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <flux:input wire:model="proposed_mentor_name" label="Voorgestelde mentor (optioneel)" />
            @error('proposed_mentor_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

            <flux:button variant="primary" wire:click="submit">
                Aanvraag indienen
            </flux:button>
        </div>
    </div>
</x-layouts.portal>
