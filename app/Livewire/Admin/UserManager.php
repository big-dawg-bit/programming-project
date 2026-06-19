<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
class UserManager extends Component
{
    use WithPagination;

    public string $name = '';

    public string $email = '';

    public string $selectedRole = 'student';

    // Enkel relevant wanneer de rol 'mentor' is.
    public ?int $companyId = null;

    public function createUser(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'selectedRole' => 'required|exists:roles,name',
            'companyId' => 'nullable|exists:companies,id',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt(str()->random(32)),
            'role_id' => Role::where('name', $data['selectedRole'])->value('id'),
        ]);

        // Maak het bijhorende subtype-record aan, zodat de gebruiker echt
        // in zijn portaal terechtkan (anders is auth()->user()->student/... null).
        match ($data['selectedRole']) {
            'student' => $user->student()->create([
                'student_number' => 'EHB'.now()->format('Y').str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                'study_program' => 'Toegepaste Informatica',
                'academic_year' => now()->format('Y').'-'.now()->addYear()->format('Y'),
            ]),
            'docent' => $user->docent()->create([]),
            'mentor' => $user->mentor()->create(['company_id' => $data['companyId'] ?? null]),
            default => null, // stagecommissie / admin: geen subtype nodig
        };

        $this->reset('name', 'email', 'companyId');
        $this->selectedRole = 'student';

        // Spring naar pagina 1 zodat de net aangemaakte gebruiker (nieuwste eerst) zichtbaar is.
        $this->resetPage();

        session()->flash('success', 'Gebruiker aangemaakt.');
    }

    public function changeRole(int $userId, int $roleId): void
    {
        User::findOrFail($userId)->update(['role_id' => $roleId]);
    }

    public function toggleActive(int $userId): void
    {
        $user = User::findOrFail($userId);
        $user->update(['is_active' => ! $user->is_active]);
    }

    public function render()
    {
        return view('livewire.admin.user-manager', [
            'users' => User::with('role')->latest('id')->paginate(15),
            'roles' => Role::orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
        ]);
    }
}
