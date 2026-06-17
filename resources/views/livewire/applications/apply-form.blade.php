<div class="mx-auto max-w-2xl">
    <div class="mb-6">
        <flux:heading size="xl">{{ $application ? 'Aanvraag bijwerken' : 'Stage aanvragen' }}</flux:heading>
        <flux:subheading>Vul het formulier in om je stage aan te vragen.</flux:subheading>
    </div>

    <x-form.success-message :message="session('status')" class="mb-4" />

    {{-- Feedback van de commissie bij "aanpassingen vereist" --}}
    @if ($feedback)
        <div class="mb-4 rounded-xl border border-amber-300 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
                <flux:icon name="exclamation-triangle" class="mt-0.5 size-5 shrink-0 text-amber-600" />
                <div>
                    <p class="font-medium text-amber-900">Aanpassingen gevraagd door de commissie</p>
                    <p class="mt-1 text-sm text-amber-800">{{ $feedback }}</p>
                </div>
            </div>
        </div>
    @endif

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

            @unless ($addingCompany)
                <button type="button" wire:click="$set('addingCompany', true)"
                        class="mt-2 text-sm font-medium text-[#E2231A] hover:underline">
                    + Nieuw bedrijf toevoegen
                </button>
            @endunless
        </div>

        {{-- Inline nieuw bedrijf --}}
        @if ($addingCompany)
            <div class="space-y-3 rounded-xl border border-neutral-200 bg-neutral-50 p-4">
                <p class="text-sm font-medium">Nieuw bedrijf</p>

                <div>
                    <label class="mb-1 block text-sm">Naam</label>
                    <input type="text" wire:model="new_company_name"
                           class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                    @error('new_company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm">Adres</label>
                    <input type="text" wire:model="new_company_address"
                           class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                    @error('new_company_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm">Btw-nummer</label>
                        <input type="text" wire:model="new_company_vat"
                               class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                        @error('new_company_vat') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm">Contact-e-mail</label>
                        <input type="email" wire:model="new_company_email"
                               class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                        @error('new_company_email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-2">
                    <flux:button variant="primary" wire:click="addCompany">Bedrijf opslaan</flux:button>
                    <flux:button variant="ghost" wire:click="$set('addingCompany', false)">Annuleren</flux:button>
                </div>
            </div>
        @endif

        <div>
            <label class="mb-1 block text-sm font-medium">Functietitel</label>
            <input type="text" wire:model="position_title"
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
            @error('position_title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Motivatie</label>
            <textarea wire:model="description" rows="4"
                      class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none"></textarea>
            @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Startdatum</label>
                <input type="date" wire:model="start_date"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Einddatum</label>
                <input type="date" wire:model="end_date"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
                @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Voorgestelde mentor (optioneel)</label>
            <input type="text" wire:model="proposed_mentor_name"
                   class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:outline-none">
            @error('proposed_mentor_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <flux:button variant="primary" wire:click="submit">
            {{ $application ? 'Opnieuw indienen' : 'Aanvraag indienen' }}
        </flux:button>
    </div>
</div>
