<div class="mx-auto flex max-w-4xl flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900">Gebruikersbeheer</h1>
        <p class="mt-1 text-sm text-neutral-500">Maak gebruikers aan, wijzig rollen en activeer/deactiveer accounts.</p>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Nieuwe gebruiker --}}
    <div class="rounded-xl border border-neutral-200 bg-white p-6">
        <h2 class="mb-4 text-lg font-semibold text-neutral-900">Nieuwe gebruiker</h2>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">Naam</label>
                <input type="text" wire:model="name"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                @error('name') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">E-mail</label>
                <input type="email" wire:model="email"
                       class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                @error('email') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="mb-1 block text-sm font-medium text-neutral-700">Rol</label>
                <select wire:model.live="selectedRole"
                        class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
                @error('selectedRole') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
            </div>

            {{-- Bedrijf enkel relevant voor een mentor --}}
            @if ($selectedRole === 'mentor')
                <div>
                    <label class="mb-1 block text-sm font-medium text-neutral-700">Bedrijf</label>
                    <select wire:model="companyId"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                        <option value="">— kies een bedrijf —</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    @error('companyId') <p class="mt-1 text-xs text-[#E2231A]">{{ $message }}</p> @enderror
                </div>
            @endif
        </div>

        <div class="mt-4 flex justify-end">
            <button wire:click="createUser"
                    class="rounded-lg bg-[#E2231A] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#c41e16] focus:ring-2 focus:ring-[#E2231A]/40 focus:outline-none">
                Gebruiker aanmaken
            </button>
        </div>
    </div>

    {{-- Gebruikerslijst --}}
    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                <tr>
                    <th class="px-4 py-3">Naam</th>
                    <th class="px-4 py-3">E-mail</th>
                    <th class="px-4 py-3 w-44">Rol</th>
                    <th class="px-4 py-3 w-32">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @foreach ($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 font-medium text-neutral-900">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-neutral-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <select wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                    class="w-full rounded-lg border border-neutral-300 px-2 py-1.5 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" @selected($user->role_id === $role->id)>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleActive({{ $user->id }})"
                                    @class([
                                        'rounded-full px-3 py-1 text-xs font-medium ring-1 ring-inset transition',
                                        'bg-green-50 text-green-700 ring-green-200 hover:bg-green-100' => $user->is_active,
                                        'bg-neutral-100 text-neutral-500 ring-neutral-200 hover:bg-neutral-200' => ! $user->is_active,
                                    ])>
                                {{ $user->is_active ? 'Actief' : 'Inactief' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>{{ $users->links() }}</div>
</div>
