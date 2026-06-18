<div class="mx-auto flex max-w-5xl flex-col gap-6">
    <div>
        <h2 class="text-lg font-semibold">Weeklogs van mijn studenten</h2>
        <p class="text-sm text-neutral-500 dark:text-neutral-400">Lees de weeklogs en geef feedback via een reactie.</p>
    </div>

    @forelse ($weeklogs as $weeklog)
        @php
            $periode = collect([$weeklog->period_start, $weeklog->period_end])
                ->filter()
                ->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->locale('nl')->translatedFormat('j M Y'))
                ->implode(' – ');
        @endphp

        <div class="rounded-xl border border-neutral-200 bg-white p-5 dark:border-neutral-800 dark:bg-neutral-900">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h3 class="font-semibold">{{ $weeklog->stage?->student?->user?->name ?? 'Onbekende student' }}</h3>
                    <p class="mt-0.5 text-sm text-neutral-500 dark:text-neutral-400">
                        Week {{ $weeklog->week_number }}@if ($periode) · {{ $periode }}@endif
                    </p>
                </div>
                <span class="shrink-0 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-600 dark:bg-blue-950/50 dark:text-blue-300">Ingediend</span>
            </div>

            <p class="mt-3 text-sm text-neutral-600 dark:text-neutral-300">{{ $weeklog->content }}</p>

            {{-- Reacties --}}
            <button type="button" wire:click="toggleComments({{ $weeklog->id }})"
                class="mt-3 flex items-center gap-1.5 text-sm text-neutral-400 hover:text-neutral-600 dark:text-neutral-500 dark:hover:text-neutral-300">
                <flux:icon name="chat-bubble-left-right" class="size-4" />
                {{ $weeklog->comments->count() }} {{ $weeklog->comments->count() === 1 ? 'reactie' : 'reacties' }}
            </button>

            @if ($openWeeklogId === $weeklog->id)
                <div class="mt-3 space-y-3 border-t border-neutral-100 pt-3 dark:border-neutral-800">
                    @forelse ($weeklog->comments as $comment)
                        <div class="rounded-lg bg-neutral-50 p-3 dark:bg-neutral-800/50">
                            <div class="text-xs text-neutral-500 dark:text-neutral-400">
                                {{ $comment->author?->name ?? 'Onbekend' }} · {{ $comment->created_at?->diffForHumans() }}
                            </div>
                            <div class="mt-1 text-sm">{{ $comment->comment }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Nog geen reacties.</p>
                    @endforelse

                    <form wire:submit="addComment({{ $weeklog->id }})" class="flex flex-col gap-2 sm:flex-row sm:items-start">
                        <div class="flex-1">
                            <flux:textarea wire:model="newComment" rows="2" placeholder="Schrijf feedback…" />
                            @error('newComment') <p class="mt-1 text-sm text-[#E2231A]">{{ $message }}</p> @enderror
                        </div>
                        <flux:button type="submit" variant="primary">Reageren</flux:button>
                    </form>
                </div>
            @endif
        </div>
    @empty
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Nog geen weeklogs</flux:heading>
            <flux:subheading class="mt-1">Zodra je studenten weeklogs indienen, verschijnen ze hier.</flux:subheading>
        </div>
    @endforelse
</div>
