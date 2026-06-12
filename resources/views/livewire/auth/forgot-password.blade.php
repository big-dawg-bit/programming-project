<x-layouts::auth title="Wachtwoord vergeten">
    @if (session('status'))
        {{-- Bevestiging: "Controleer je mail" --}}
        <div class="flex flex-col gap-6 text-center">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 text-zinc-500">
                <flux:icon.envelope class="size-6" />
            </div>

            <div class="flex flex-col gap-1">
                <flux:heading size="xl">Controleer je mail</flux:heading>
                <flux:subheading>
                    We hebben een reset-link gestuurd naar je EhB-mailadres.
                    Klik op de link om een nieuw wachtwoord in te stellen.
                </flux:subheading>
            </div>

            <flux:button variant="primary" class="w-full" :href="route('login')" wire:navigate>
                Terug naar inloggen
            </flux:button>

            <p class="text-sm text-zinc-500">Geen mail ontvangen? Controleer je spam-folder.</p>
        </div>
    @else
        {{-- Formulier --}}
        <div class="flex flex-col gap-6">
            <x-auth-header
                title="Wachtwoord vergeten"
                description="Vul je EhB-mailadres in. We sturen je een link om je wachtwoord te resetten."
            />

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
                @csrf

                <flux:input
                    name="email"
                    label="EhB-mailadres"
                    type="email"
                    required
                    autofocus
                    placeholder="voornaam.naam@student.ehb.be"
                />

                <flux:button variant="primary" type="submit" class="w-full" data-test="email-password-reset-link-button">
                    Stuur reset-link
                </flux:button>
            </form>

            <div class="text-center text-sm">
                <flux:link :href="route('login')" wire:navigate>← Terug naar inloggen</flux:link>
            </div>
        </div>
    @endif
</x-layouts::auth>
