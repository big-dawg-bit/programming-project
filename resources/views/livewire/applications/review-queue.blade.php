<div>
    <h1 class="text-2xl font-bold mb-4">Aanvragen ter beoordeling</h1>

    @if ($applications->isEmpty())
        <p class="text-gray-500">Er zijn geen openstaande aanvragen.</p>
    @else
        <table class="w-full border-collapse border border-gray-300">
            <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 p-2 text-left">Student</th>
                <th class="border border-gray-300 p-2 text-left">Bedrijf</th>
                <th class="border border-gray-300 p-2 text-left">Functie</th>
                <th class="border border-gray-300 p-2 text-left">Ingediend</th>
                <th class="border border-gray-300 p-2 text-left">Actie</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($applications as $application)
                <tr wire:key="app-{{ $application->id }}">
                    <td class="border border-gray-300 p-2">{{ $application->student?->user?->name }}</td>
                    <td class="border border-gray-300 p-2">{{ $application->company?->name }}</td>
                    <td class="border border-gray-300 p-2">{{ $application->position_title }}</td>
                    <td class="border border-gray-300 p-2">{{ $application->submitted_at?->format('d-m-Y') }}</td>
                    <td class="border border-gray-300 p-2">
                        <div class="flex flex-col gap-2">
                            <button wire:click="approve({{ $application->id }})"
                                    class="rounded bg-[#E2231A] px-3 py-1 text-white">
                                Goedkeuren
                            </button>

                            <textarea wire:model="feedback.{{ $application->id }}"
                                      placeholder="Reden / feedback"
                                      class="border border-gray-300 p-1 text-sm"></textarea>

                            @error('feedback.'.$application->id)
                            <span class="text-xs text-red-600">{{ $message }}</span>
                            @enderror

                            <button wire:click="reject({{ $application->id }})"
                                    class="rounded border border-gray-400 px-3 py-1">
                                Afwijzen
                            </button>

                            <button wire:click="requestChanges({{ $application->id }})"
                                    class="rounded border border-amber-500 px-3 py-1 text-amber-700">
                                Aanpassingen vereist
                            </button>

                            <a href="{{ route('applications.show', $application->id) }}"
                               class="text-center text-sm text-[#E2231A] underline">
                                Bekijk details
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $applications->links() }}</div>
    @endif
</div>
