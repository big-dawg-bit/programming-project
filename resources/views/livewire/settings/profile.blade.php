<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified.') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </flux:link>
                        </flux:text>

                    </div>
                @endif
            </div>

            @if ($isMentor)
                <flux:separator variant="subtle" />

                <flux:heading size="sm">{{ __('Bedrijfsgegevens') }}</flux:heading>

                <flux:input wire:model="companyName" :label="__('Bedrijfsnaam')" type="text" readonly disabled />
                <flux:input wire:model="vatNumber" :label="__('BTW-nummer')" type="text" readonly disabled />
                <flux:text class="-mt-3 text-xs">{{ __('Bedrijfsnaam en BTW-nummer worden door de administratie beheerd.') }}</flux:text>

                <flux:input wire:model="phone" :label="__('Telefoonnummer')" type="text" />
                <flux:input wire:model="address" :label="__('Adres')" type="text" />
            @endif

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit">{{ __('Save') }}</flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:settings.delete-user-form />
        @endif
    </x-settings.layout>
</section>
