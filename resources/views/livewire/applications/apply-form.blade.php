<div class="max-w-2xl">
    <h1 class="text-2xl font-bold mb-4">Stage aanvragen</h1>

    @if (session('status'))
        <div class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="space-y-4">
        <div>
            <label class="mb-2 flex items-center gap-2 text-sm font-medium">
                <input type="checkbox" wire:model.live="newCompany" class="rounded border-gray-300">
                Nieuw bedrijf toevoegen
            </label>

            @if (! $newCompany)
                <select wire:model="company_id" class="w-full rounded border border-gray-300 p-2">
                    <option value="">— Kies een bedrijf —</option>
                    @foreach ($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
                @error('company_id') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            @else
                <div class="space-y-3 rounded border border-gray-200 p-3">
                    <div>
                        <label class="mb-1 block text-sm font-medium">Bedrijfsnaam</label>
                        <input type="text" wire:model="company_name" class="w-full rounded border border-gray-300 p-2">
                        @error('company_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Adres</label>
                        <input type="text" wire:model="company_address" class="w-full rounded border border-gray-300 p-2">
                        @error('company_address') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium">Btw-nummer</label>
                        <input type="text" wire:model="company_vat_number" class="w-full rounded border border-gray-300 p-2">
                        @error('company_vat_number') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium">Contact e-mail</label>
                            <input type="email" wire:model="company_contact_email" class="w-full rounded border border-gray-300 p-2">
                            @error('company_contact_email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium">Contact telefoon</label>
                            <input type="text" wire:model="company_contact_phone" class="w-full rounded border border-gray-300 p-2">
                            @error('company_contact_phone') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif
        </div>

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
            Aanvraag indienen
        </button>
    </div>
</div>
