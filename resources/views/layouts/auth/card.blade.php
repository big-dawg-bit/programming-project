<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- De auth-kaart is ontworpen voor lichte modus. Op basis van het systeemthema zou
         Flux 'dark' op <html> zetten, waardoor de accentkleur wit wordt en tekst wit-op-wit
         valt. Door de voorkeur op 'light' te zetten vóór Flux initialiseert, kiest Flux
         zelf lichte modus en komt 'dark' er nooit op. --}}
    <script>try { localStorage.setItem('flux.appearance', 'light'); } catch (e) {}</script>
    @include('partials.head')

    {{-- De auth-schermen (login, wachtwoord vergeten/resetten) zijn bewust
         light-branded: de rode EhB-kaart op een lichte achtergrond. We negeren
         hier dus een eventuele dark-voorkeur, zodat de kaart altijd leesbaar blijft. --}}
    <script>
        document.documentElement.classList.remove('dark');
        document.addEventListener('livewire:navigated', () => document.documentElement.classList.remove('dark'));
    </script>
</head>
<body class="min-h-screen bg-[#F7F7F8] antialiased">
<div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
    <div class="flex w-full max-w-md flex-col gap-6">
        <div class="rounded-2xl border border-zinc-200 bg-white text-zinc-800 shadow-sm">
            <div class="flex flex-col gap-6 px-8 py-10 sm:px-10">
                <a href="{{ route('home') }}" class="flex justify-center" wire:navigate>
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#E2231A] text-lg font-bold text-white">
                                EhB
                            </span>
                    <span class="sr-only">{{ config('app.name', 'Stage Monitoring Tool') }}</span>
                </a>

                {{ $slot }}
            </div>
        </div>
    </div>
</div>

@persist('toast')
<flux:toast.group>
    <flux:toast />
</flux:toast.group>
@endpersist

@fluxScripts
</body>
</html>
