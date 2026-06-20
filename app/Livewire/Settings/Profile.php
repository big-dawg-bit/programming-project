<?php

namespace App\Livewire\Settings;

use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Profile settings')]
class Profile extends Component
{
    use ProfileValidationRules;

    public string $name = '';

    public string $email = '';

    // Mentor-specifiek: telefoon en adres zijn bewerkbaar; bedrijfsnaam en BTW niet.
    public bool $isMentor = false;

    public string $companyName = '';

    public string $vatNumber = '';

    public string $phone = '';

    public string $address = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;

        if ($user->mentor) {
            $this->isMentor = true;
            $this->phone = $user->mentor->phone ?? '';
            $this->companyName = $user->mentor->company?->name ?? '';
            $this->vatNumber = $user->mentor->company?->vat_number ?? '';
            $this->address = $user->mentor->company?->address ?? '';
        }
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Mentor mag enkel telefoon en adres aanpassen — bedrijfsnaam en BTW blijven vast.
        if ($this->isMentor && $user->mentor) {
            $this->validate([
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string|max:255',
            ]);

            $user->mentor->update(['phone' => $this->phone]);
            $user->mentor->company?->update(['address' => $this->address]);
        }

        Flux::toast(variant: 'success', text: __('Profile updated.'));
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(text: __('A new verification link has been sent to your email address.'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}
