<div class="mx-auto flex max-w-4xl flex-col gap-6">
    {{-- Zoek + export --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <input type="text" wire:model.live.debounce.300ms="zoek" placeholder="Zoek student…"
            class="w-full max-w-sm rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm focus:border-[#E2231A] focus:outline-none dark:border-neutral-700 dark:bg-neutral-900 dark:text-neutral-100 dark:placeholder-neutral-500" />
        <button type="button"
            class="flex items-center gap-2 rounded-lg border border-neutral-200 bg-white px-4 py-2 text-sm font-medium transition hover:bg-neutral-50 dark:border-neutral-700 dark:bg-neutral-900 dark:hover:bg-neutral-800">
            <flux:icon name="arrow-down-tray" class="size-4" /> Exporteer alle rapporten
        </button>
    </div>

    {{-- NB: voorlopig statische voorbeelddata; later koppelen aan de eindrapporten van de begeleide stages. --}}
    @php
        $rapporten = [
            ['naam' => 'Tom De Wachter', 'afgerond' => '15 juni 2025', 'status' => 'Cijfer ingediend',             'eindcijfer' => '16/20', 'weeklogs' => '14/14', 'competentie' => '4.2/5', 'mentor' => '15/20', 'docent' => '17/20'],
            ['naam' => 'Anna Peeters',   'afgerond' => '10 juni 2025', 'status' => 'Wacht op goedkeuring commissie', 'eindcijfer' => '18/20', 'weeklogs' => '14/14', 'competentie' => '4.8/5', 'mentor' => '17/20', 'docent' => '19/20'],
            ['naam' => 'Lina Janssens',  'afgerond' => '8 juni 2025',  'status' => 'Cijfer ingediend',             'eindcijfer' => '15/20', 'weeklogs' => '14/14', 'competentie' => '3.9/5', 'mentor' => '14/20', 'docent' => '16/20'],
        ];
        $statusBadge = [
            'Cijfer ingediend'              => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
            'Wacht op goedkeuring commissie'=> 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
        ];

        if (trim($zoek) !== '') {
            $rapporten = array_values(array_filter($rapporten, fn ($r) => str_contains(mb_strtolower($r['naam']), mb_strtolower(trim($zoek)))));
        }
    @endphp

    @if (empty($rapporten))
        <div class="rounded-xl border border-neutral-200 bg-white p-10 text-center dark:border-neutral-800 dark:bg-neutral-900">
            <flux:heading size="lg">Geen rapporten gevonden</flux:heading>
            <flux:subheading class="mt-1">Geen student die overeenkomt met "{{ $zoek }}".</flux:subheading>
        </div>
    @endif

    <div class="flex flex-col gap-5">
        @foreach ($rapporten as $r)
            @php $initialen = collect(explode(' ', $r['naam']))->filter()->map(fn ($d) => mb_substr($d, 0, 1))->take(2)->implode(''); @endphp
            <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-800 dark:bg-neutral-900">
                {{-- Kop --}}
                <div class="flex items-start gap-4">
                    <span class="grid size-14 shrink-0 place-items-center rounded-full bg-neutral-100 text-base font-semibold text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300">
                        {{ $initialen }}
                    </span>
                    <div class="flex-1">
                        <h3 class="text-lg font-bold">{{ $r['naam'] }}</h3>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Stage afgerond op {{ $r['afgerond'] }}</p>
                        <span class="mt-2 inline-block rounded-full px-2.5 py-1 text-xs font-medium {{ $statusBadge[$r['status']] }}">{{ $r['status'] }}</span>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold text-[#E2231A]">{{ $r['eindcijfer'] }}</p>
                        <p class="text-sm text-neutral-500 dark:text-neutral-400">Eindcijfer</p>
                    </div>
                </div>

                {{-- Statistiek --}}
                <div class="mt-5 grid gap-3 sm:grid-cols-4">
                    @foreach ([['Weeklogs ingediend', $r['weeklogs']], ['Gem. competentiescore', $r['competentie']], ['Mentor gemiddelde', $r['mentor']], ['Docent gemiddelde', $r['docent']]] as [$label, $waarde])
                        <div class="rounded-lg bg-neutral-50 p-4 dark:bg-neutral-800/50">
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $label }}</p>
                            <p class="mt-1 text-xl font-bold">{{ $waarde }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Acties --}}
                <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a href="{{ route('docent.student.show', ['naam' => $r['naam']]) }}" wire:navigate
                       class="rounded-lg bg-[#E2231A] px-4 py-2.5 text-center text-sm font-medium text-white transition hover:bg-[#c91e16]">
                        Rapport bekijken
                    </a>
                    <button type="button"
                        class="flex items-center justify-center gap-2 rounded-lg border border-neutral-200 px-4 py-2.5 text-sm font-medium transition hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-800">
                        <flux:icon name="arrow-down-tray" class="size-4" /> PDF downloaden
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
