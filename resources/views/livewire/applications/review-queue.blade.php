<div class="mx-auto max-w-5xl">
    <div class="mb-6">
        <flux:heading size="xl">Aanvragen ter beoordeling</flux:heading>
        <flux:subheading>Beoordeel de openstaande stage-aanvragen.</flux:subheading>
    </div>

    <x-form.success-message :message="session('status')" class="mb-4" />

    @if ($applications->isEmpty())
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center">
            <flux:heading size="lg">Geen openstaande aanvragen</flux:heading>
            <flux:subheading class="mt-1">Er zijn momenteel geen aanvragen ter beoordeling.</flux:subheading>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach ($applications as $application)
                <div wire:key="app-{{ $application->id }}"
                     class="rounded-xl border border-neutral-200 bg-white p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold">{{ $application->student?->user?->name }}</h3>
                                <x-status-badge :status="$application->status ?? 'pending'" />
                            </div>
                            <p class="mt-1 text-sm text-neutral-500">
                                {{ $application->company?->name }} · {{ $application->position_title }}
                            </p>
                            <p class="mt-0.5 text-xs text-neutral-400">
                                Ingediend op {{ $application->submitted_at?->format('d-m-Y') ?? '—' }}
                            </p>
                        </div>

                        <a href="{{ route('applications.show', $application->id) }}"
                           class="shrink-0 text-sm font-medium text-[#E2231A] hover:underline">
                            Bekijk details
                        </a>
                    </div>

                    <div class="mt-4 flex flex-col gap-3 border-t border-neutral-100 pt-4">
                        <flux:textarea wire:model="feedback.{{ $application->id }}"
                                       placeholder="Reden van afwijzing of feedback bij aanpassingen" rows="2" />
                        @error('feedback.'.$application->id)
                        <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="flex flex-wrap gap-3">
                            <flux:button variant="primary" wire:click="approve({{ $application->id }})">
                                Goedkeuren
                            </flux:button>
                            <flux:button variant="ghost" wire:click="reject({{ $application->id }})">
                                Afwijzen
                            </flux:button>
                            <flux:button variant="ghost" wire:click="requestChanges({{ $application->id }})">
                                Aanpassingen vereist
                            </flux:button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">{{ $applications->links() }}</div>
    @endif
</div>
