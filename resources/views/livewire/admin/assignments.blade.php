<div class="mx-auto flex max-w-4xl flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900 dark:text-neutral-100">Toewijzingen</h1>
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Wijs een begeleidende docent toe aan de stage van een student.</p>
    </div>

    @if (session('success'))
        <div class="rounded-lg border border-green-300 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-900/50 dark:bg-green-950/40 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-neutral-200 bg-white dark:border-neutral-800 dark:bg-neutral-900">
        <table class="w-full text-sm">
            <thead class="bg-neutral-50 text-left text-xs font-medium uppercase tracking-wide text-neutral-500 dark:bg-neutral-800/50 dark:text-neutral-400">
                <tr>
                    <th class="px-4 py-3">Student</th>
                    <th class="px-4 py-3">Bedrijf</th>
                    <th class="px-4 py-3 w-64">Begeleidende docent</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100 dark:divide-neutral-800">
                @forelse ($stages as $stage)
                    <tr wire:key="stage-{{ $stage->id }}">
                        <td class="px-4 py-3 font-medium text-neutral-900 dark:text-neutral-100">
                            {{ $stage->student?->user?->name ?? 'Onbekende student' }}
                        </td>
                        <td class="px-4 py-3 text-neutral-600 dark:text-neutral-400">
                            {{ $stage->company?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <select wire:change="assignDocent({{ $stage->id }}, $event.target.value)"
                                    class="w-full rounded-lg border border-neutral-300 px-2 py-1.5 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-800">
                                <option value="">— geen docent —</option>
                                @foreach ($docenten as $docent)
                                    <option value="{{ $docent->id }}" @selected($stage->docent_id === $docent->id)>
                                        {{ $docent->user?->name ?? 'Docent #'.$docent->id }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-sm text-neutral-500 dark:text-neutral-400">
                            Nog geen stages om toe te wijzen.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
