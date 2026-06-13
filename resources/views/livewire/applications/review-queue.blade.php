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
            </tr>
            </thead>
            <tbody>
            @foreach ($applications as $application)
                <tr wire:key="app-{{ $application->id }}">
                    <td class="border border-gray-300 p-2">{{ $application->student?->user?->name }}</td>
                    <td class="border border-gray-300 p-2">{{ $application->company?->name }}</td>
                    <td class="border border-gray-300 p-2">{{ $application->position_title }}</td>
                    <td class="border border-gray-300 p-2">{{ $application->submitted_at?->format('d-m-Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4">{{ $applications->links() }}</div>
    @endif
</div>
