<div>
    <h1 class="text-2xl font-bold mb-4">Gebruikersbeheer</h1>

    <table class="w-full border-collapse border border-gray-300">
        <thead>
        <tr class="bg-gray-100">
            <th class="border border-gray-300 p-2">Naam</th>
            <th class="border border-gray-300 p-2">E-mail</th>
            <th class="border border-gray-300 p-2">Rol</th>
            <th class="border border-gray-300 p-2">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td class="border border-gray-300 p-2">{{ $user->name }}</td>
                <td class="border border-gray-300 p-2">{{ $user->email }}</td>
                <td class="border border-gray-300 p-2">{{ $user->role?->name }}</td>
                <td class="border border-gray-300 p-2">
                    {{ $user->is_active ? 'Actief' : 'Inactief' }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>

