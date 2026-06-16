<div class="max-w-3xl">
    <a href="{{ route('applications.review') }}" class="text-sm text-[#E2231A] underline">
        ← Terug naar wachtrij
    </a>

    <h1 class="mt-2 text-2xl font-bold">Aanvraag van {{ $application->student?->user?->name }}</h1>
    <p class="mb-4 text-sm text-gray-500">Status: {{ $application->status }}</p>

    <dl class="grid grid-cols-3 gap-2 rounded border border-gray-200 p-4 text-sm">
        <dt class="font-semibold">Student</dt>
        <dd class="col-span-2">{{ $application->student?->user?->name }}</dd>

        <dt class="font-semibold">Bedrijf</dt>
        <dd class="col-span-2">{{ $application->company?->name }}</dd>

        <dt class="font-semibold">Adres</dt>
        <dd class="col-span-2">{{ $application->company?->address ?? '—' }}</dd>

        <dt class="font-semibold">Contact</dt>
        <dd class="col-span-2">{{ $application->company?->contact_email ?? '—' }}</dd>

        <dt class="font-semibold">Functie</dt>
        <dd class="col-span-2">{{ $application->position_title }}</dd>

        <dt class="font-semibold">Periode</dt>
        <dd class="col-span-2">
            {{ $application->start_date?->format('d-m-Y') }} – {{ $application->end_date?->format('d-m-Y') }}
        </dd>

        <dt class="font-semibold">Voorgestelde mentor</dt>
        <dd class="col-span-2">{{ $application->proposed_mentor_name ?? '—' }}</dd>

        <dt class="font-semibold">Omschrijving</dt>
        <dd class="col-span-2 whitespace-pre-line">{{ $application->description }}</dd>
    </dl>

    @if ($application->reviews->isNotEmpty())
        <h2 class="mt-6 mb-2 text-lg font-semibold">Beoordelingsgeschiedenis</h2>
        <ul class="space-y-2">
            @foreach ($application->reviews as $review)
                <li wire:key="review-{{ $review->id }}" class="rounded border border-gray-200 p-3 text-sm">
                    <span class="font-medium">{{ $review->decision }}</span>
                    door {{ $review->reviewer?->name ?? 'onbekend' }}
                    op {{ $review->reviewed_at?->format('d-m-Y') }}
                    @if ($review->feedback)
                        <p class="mt-1 text-gray-600">{{ $review->feedback }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
