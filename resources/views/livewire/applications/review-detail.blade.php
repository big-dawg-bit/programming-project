<div class="mx-auto max-w-3xl">
    <a href="{{ route('applications.review') }}"
       class="inline-flex items-center gap-1 text-sm font-medium text-[#E2231A] hover:underline">
        ← Terug naar wachtrij
    </a>

    <div class="mt-3 mb-6 flex items-center gap-3">
        <flux:heading size="xl">Aanvraag van {{ $application->student?->user?->name }}</flux:heading>
        <x-status-badge :status="$application->status ?? 'pending'" />
    </div>

    <dl class="grid grid-cols-3 gap-y-3 rounded-xl border border-neutral-200 bg-white p-6 text-sm">
        <dt class="font-medium text-neutral-500">Student</dt>
        <dd class="col-span-2">{{ $application->student?->user?->name }}</dd>

        <dt class="font-medium text-neutral-500">Bedrijf</dt>
        <dd class="col-span-2">{{ $application->company?->name }}</dd>

        <dt class="font-medium text-neutral-500">Adres</dt>
        <dd class="col-span-2">{{ $application->company?->address ?? '—' }}</dd>

        <dt class="font-medium text-neutral-500">Contact</dt>
        <dd class="col-span-2">{{ $application->company?->contact_email ?? '—' }}</dd>

        <dt class="font-medium text-neutral-500">Functie</dt>
        <dd class="col-span-2">{{ $application->position_title }}</dd>

        <dt class="font-medium text-neutral-500">Periode</dt>
        <dd class="col-span-2">
            {{ $application->start_date?->format('d-m-Y') }} – {{ $application->end_date?->format('d-m-Y') }}
        </dd>

        <dt class="font-medium text-neutral-500">Voorgestelde mentor</dt>
        <dd class="col-span-2">{{ $application->proposed_mentor_name ?? '—' }}</dd>

        <dt class="font-medium text-neutral-500">Omschrijving</dt>
        <dd class="col-span-2 whitespace-pre-line">{{ $application->description }}</dd>
    </dl>

    @if ($application->reviews->isNotEmpty())
        <h2 class="mt-6 mb-2 text-lg font-semibold">Beoordelingsgeschiedenis</h2>
        <ul class="space-y-2">
            @foreach ($application->reviews as $review)
                <li wire:key="review-{{ $review->id }}" class="rounded-lg border border-neutral-200 bg-white p-3 text-sm">
                    <span class="font-medium">{{ $review->decision }}</span>
                    door {{ $review->reviewer?->name ?? 'onbekend' }}
                    op {{ $review->reviewed_at?->format('d-m-Y') }}
                    @if ($review->feedback)
                        <p class="mt-1 text-neutral-600">{{ $review->feedback }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
</div>
