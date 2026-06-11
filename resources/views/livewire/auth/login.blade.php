<x-layouts::auth title="Inloggen">
    <div class="flex flex-col gap-6">
        <x-auth-header title="Welkom terug" description="Log in om verder te gaan" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- EhB-mailadres -->
            <flux:input
                name="email"
                label="EhB-mailadres"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="voornaam.naam@student.ehb.be"
            />

            <!-- Wachtwoord -->
            <div class="flex flex-col gap-1.5">
                <flux:input
                    name="password"
                    label="Wachtwoord"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="self-end text-sm" :href="route('password.request')" wire:navigate>
                        Wachtwoord vergeten?
                    </flux:link>
                @endif
            </div>

            <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                Inloggen
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
