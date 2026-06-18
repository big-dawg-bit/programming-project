<div class="mx-auto flex max-w-5xl flex-col gap-6">

    <div>
        <h1 class="text-2xl font-bold text-neutral-900">Aanvragen ter beoordeling</h1>
        <p class="mt-1 text-sm text-neutral-500">
            Beoordeel ingediende stageaanvragen: goedkeuren, afwijzen of aanpassingen vragen.
        </p>
    </div>

    @forelse ($applications as $application)
        @php $mentors = $mentorsByCompany[$application->company_id] ?? collect(); @endphp
        <div wire:key="app-{{ $application->id }}"
             class="rounded-xl border border-neutral-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">

                {{-- Aanvraag-info --}}
                <div class="flex items-start gap-4">
                    <div class="grid size-11 shrink-0 place-items-center rounded-full bg-[#E2231A]/10 text-[#E2231A]">
                        <flux:icon.user class="size-5" />
                    </div>
                    <div>
                        <p class="font-semibold text-neutral-900">
                            {{ $application->student?->user?->name ?? 'Onbekende student' }}
                        </p>
                        <p class="text-sm text-neutral-600">{{ $application->position_title }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-neutral-500">
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.briefcase class="size-4" /> {{ $application->company?->name ?? '—' }}
                            </span>
                            <span class="inline-flex items-center gap-1">
                                <flux:icon.calendar class="size-4" />
                                Ingediend {{ $application->submitted_at?->format('d/m/Y') ?? '—' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Acties --}}
                <div class="flex w-full shrink-0 flex-col gap-2 lg:w-72">

                    {{-- Toewijzing bij goedkeuring --}}
                    <select wire:model="docentId.{{ $application->id }}"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                        <option value="">— Kies docent —</option>
                        @foreach ($docenten as $docent)
                            <option value="{{ $docent->id }}">{{ $docent->user?->name ?? 'Docent #'.$docent->id }}</option>
                        @endforeach
                    </select>
                    @error('docentId.'.$application->id)
                    <span class="text-xs text-[#E2231A]">{{ $message }}</span>
                    @enderror

                    <select wire:model="mentorId.{{ $application->id }}"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                        <option value="">— Kies mentor —</option>
                        @forelse ($mentors as $mentor)
                            <option value="{{ $mentor->id }}">{{ $mentor->user?->name ?? 'Mentor #'.$mentor->id }}</option>
                        @empty
                            <option value="" disabled>Geen mentor voor dit bedrijf</option>
                        @endforelse
                    </select>
                    @error('mentorId.'.$application->id)
                    <span class="text-xs text-[#E2231A]">{{ $message }}</span>
                    @enderror

                    <select wire:model="frameworkId.{{ $application->id }}"
                            class="w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none">
                        <option value="">— Kies evaluatiekader —</option>
                        @foreach ($frameworks as $framework)
                            <option value="{{ $framework->id }}">{{ $framework->name }}</option>
                        @endforeach
                    </select>
                    @error('frameworkId.'.$application->id)
                    <span class="text-xs text-[#E2231A]">{{ $message }}</span>
                    @enderror

                    <button wire:click="approve({{ $application->id }})"
                            class="rounded-lg bg-[#E2231A] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#c41e16] focus:ring-2 focus:ring-[#E2231A]/40 focus:outline-none">
                        Goedkeuren
                    </button>

                    <textarea wire:model="feedback.{{ $application->id }}" rows="2"
                              placeholder="Reden / feedback (bij afwijzen of aanpassingen)"
                              class="w-full resize-none rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-[#E2231A] focus:ring-1 focus:ring-[#E2231A] focus:outline-none"></textarea>
                    @error('feedback.'.$application->id)
                    <span class="text-xs text-[#E2231A]">{{ $message }}</span>
                    @enderror

                    <div class="flex gap-2">
                        <button wire:click="reject({{ $application->id }})"
                                class="flex-1 rounded-lg border border-neutral-300 px-3 py-2 text-sm font-medium text-neutral-700 transition hover:bg-neutral-50">
                            Afwijzen
                        </button>
                        <button wire:click="requestChanges({{ $application->id }})"
                                class="flex-1 rounded-lg border border-amber-400 px-3 py-2 text-sm font-medium text-amber-700 transition hover:bg-amber-50">
                            Aanpassingen
                        </button>
                    </div>

                    <a href="{{ route('applications.show', $application->id) }}" wire:navigate
                       class="mt-1 text-center text-sm font-medium text-[#E2231A] hover:underline">
                        Bekijk details
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-neutral-300 bg-white p-10 text-center">
            <p class="text-sm text-neutral-500">Er zijn geen openstaande aanvragen.</p>
        </div>
    @endforelse

    @if ($applications->hasPages())
        <div>{{ $applications->links() }}</div>
    @endif
</div>
