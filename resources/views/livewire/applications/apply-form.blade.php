<div class="max-w-2xl">
    <h1 class="text-2xl font-bold mb-4">

        {{ $application ? 'Aanvraag bijwerken' : 'Stage aanvragen' }}
    </h1>

    @if (session('status'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-800">

            {{ session('status') }}
        </div>

    @endif

    {{-- Feedback van de commissie bij "aanpassingen vereist" (Maxime mag dit nog stylen) --}}

    @if ($feedback)
        <div class="mb-4 rounded border border-amber-200 bg-amber-50 p-3 text-amber-900">
            <p class="font-medium">Aanpassingen gevraagd door de commissie:</p>
            <p class="mt-1">{{ $feedback }}</p>
        </div>

    @endif

    <div class="space-y-4">
        <div>
            <label class="mb-1 block text-sm font-medium">Bedrijf</label>
            <select wire:model="company_id" class="w-full rounded border border-gray-300 p-2">
                <option value="">— Kies een bedrijf —</option>

                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>

                @endforeach
            </select>

            @error('company_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror

            @unless ($addingCompany)
                <button type="button" wire:click="$set('addingCompany', true)"

                        class="mt-2 text-sm text-[#E2231A] hover:underline">

                    + Nieuw bedrijf toevoegen
                </button>

            @endunless
        </div>

        {{-- Inline nieuw bedrijf --}}

        @if ($addingCompany)
            <div class="space-y-3 rounded border border-gray-200 bg-gray-50 p-4">
                <p class="text-sm font-medium">Nieuw bedrijf</p>

                <div>
                    <label class="mb-1 block text-sm">Naam</label>
                    <input type="text" wire:model="new_company_name" class="w-full rounded border border-gray-300 p-2">

                    @error('new_company_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-sm">Adres</label>
                    <input type="text" wire:model="new_company_address" class="w-full rounded border border-gray-300 p-2">

                    @error('new_company_address') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                </div>

                <div class="grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm">Btw-nummer</label>
                        <input type="text" wire:model="new_company_vat" class="w-full rounded border border-gray-300 p-2">

                        @error('new_company_vat') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm">Contact-e-mail</label>
                        <input type="email" wire:model="new_company_email" class="w-full rounded border border-gray-300 p-2">

                        @error('new_company_email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="button" wire:click="addCompany"

                            class="rounded bg-[#E2231A] px-4 py-2 text-sm text-white hover:opacity-90">

                        Bedrijf opslaan
                    </button>
                    <button type="button" wire:click="$set('addingCompany', false)"

                            class="rounded border border-gray-300 px-4 py-2 text-sm hover:bg-gray-100">

                        Annuleren
                    </button>
                </div>
            </div>

        @endif

        <div>
            <label class="mb-1 block text-sm font-medium">Functietitel</label>
            <input type="text" wire:model="position_title" class="w-full rounded border border-gray-300 p-2">

            @error('position_title') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Motivatie</label>
            <textarea wire:model="description" rows="4" class="w-full rounded border border-gray-300 p-2"></textarea>

            @error('description') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium">Startdatum</label>
                <input type="date" wire:model="start_date" class="w-full rounded border border-gray-300 p-2">

                @error('start_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium">Einddatum</label>
                <input type="date" wire:model="end_date" class="w-full rounded border border-gray-300 p-2">

                @error('end_date') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
        </div>

        <div>
            <label class="mb-1 block text-sm font-medium">Voorgestelde mentor (optioneel)</label>
            <input type="text" wire:model="proposed_mentor_name" class="w-full rounded border border-gray-300 p-2">

            @error('proposed_mentor_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </div>

        <button wire:click="submit" class="rounded bg-[#E2231A] px-5 py-2 text-white hover:opacity-90">

            {{ $application ? 'Opnieuw indienen' : 'Aanvraag indienen' }}
        </button>
    </div>
</div>
