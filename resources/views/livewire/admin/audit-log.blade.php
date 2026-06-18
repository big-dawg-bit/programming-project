<div class="mx-auto flex max-w-4xl flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900">Logboek</h1>
        <p class="mt-1 text-sm text-neutral-500">Overzicht van wijzigingen in het systeem (wie, wat, wanneer).</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-left text-xs font-medium uppercase tracking-wide text-neutral-500">
                <tr>
                    <th class="px-4 py-3 w-44">Wanneer</th>
                    <th class="px-4 py-3 w-48">Wie</th>
                    <th class="px-4 py-3">Actie</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
                @forelse ($logs as $log)
                    <tr wire:key="log-{{ $log->id }}">
                        <td class="px-4 py-3 text-neutral-500">{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-neutral-700">{{ $log->user?->name ?? 'Systeem' }}</td>
                        <td class="px-4 py-3 text-neutral-900">{{ $log->action }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-neutral-500">
                            Nog geen activiteit geregistreerd.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $logs->links() }}</div>
</div>
