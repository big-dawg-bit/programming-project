<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
class UserManager extends Component
{
    use WithPagination;

    // Actieve rolfilter voor de gebruikerslijst ('' = alle rollen).
    public string $roleFilter = '';

    public string $name = '';

    public string $email = '';

    public string $selectedRole = 'student';

    // Enkel relevant wanneer de rol 'mentor' is.
    public ?int $companyId = null;

    // Bedrijfsgegevens die de admin invult bij het aanmaken van een mentor.
    public string $companyName = '';

    // Belgisch BTW-nummer: vaste 'BE' + 10 cijfers, geformatteerd als BE0821.385.112.
    public string $vatNumber = '';

    public string $phone = '';

    // Adres opgesplitst in losse onderdelen.
    public string $street = '';

    public string $houseNumber = '';

    public string $postalCode = '';

    public string $municipality = '';

    // Wachtwoord voor de nieuwe gebruiker; leeg = automatisch genereren.
    public string $password = '';

    public function createUser(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'selectedRole' => 'required|exists:roles,name',
            'companyId' => 'nullable|exists:companies,id',
            'password' => 'nullable|string|min:8',
            // Bedrijfsgegevens verplicht zodra de rol 'mentor' is.
            'companyName' => 'required_if:selectedRole,mentor|nullable|string|max:255',
            // BTW: exact 'BE' + 10 cijfers, geformatteerd als BE0821.385.112.
            'vatNumber' => ['required_if:selectedRole,mentor', 'nullable', 'regex:/^BE\d{4}\.\d{3}\.\d{3}$/'],
            'phone' => 'required_if:selectedRole,mentor|nullable|string|max:50',
            'street' => 'required_if:selectedRole,mentor|nullable|string|max:255',
            'houseNumber' => 'required_if:selectedRole,mentor|nullable|string|max:20',
            'postalCode' => 'required_if:selectedRole,mentor|nullable|digits:4',
            'municipality' => 'required_if:selectedRole,mentor|nullable|string|max:255',
        ], [
            'vatNumber.regex' => 'Het BTW-nummer moet de vorm BE0821.385.112 hebben (10 cijfers).',
            'postalCode.digits' => 'De postcode bestaat uit 4 cijfers.',
        ]);

        // Echt bruikbaar wachtwoord: opgegeven door de admin of automatisch gegenereerd.
        $plainPassword = $data['password'] !== '' ? $data['password'] : Str::password(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($plainPassword),
            'email_verified_at' => now(),
            'is_active' => true,
            'role_id' => Role::where('name', $data['selectedRole'])->value('id'),
        ]);

        // Bijhorend subtype, zodat de gebruiker echt in zijn eigen portaal terechtkan.
        match ($data['selectedRole']) {
            'student' => $user->student()->create([
                'student_number' => 'EHB'.now()->format('Y').str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                'study_program' => 'Toegepaste Informatica',
                'academic_year' => now()->format('Y').'-'.now()->addYear()->format('Y'),
            ]),
            'docent' => $user->docent()->create([]),
            'mentor' => $this->createMentor($user, $data),
            default => null,
        };

        $email = $user->email;
        $this->reset('name', 'email', 'companyId', 'companyName', 'vatNumber', 'phone', 'street', 'houseNumber', 'postalCode', 'municipality', 'password');
        $this->selectedRole = 'student';
        $this->resetPage();

        session()->flash('success', "Gebruiker aangemaakt. Inloggegevens — e-mail: {$email} · wachtwoord: {$plainPassword}");
    }

    /**
     * Maak het mentor-subtype aan met bijhorend bedrijf.
     * De admin typt nieuwe bedrijfsgegevens in; bestaat er al een companyId, dan
     * wordt die als fallback gebruikt.
     */
    private function createMentor(User $user, array $data): void
    {
        $companyId = $data['companyId'] ?? null;

        if (! empty($data['companyName'])) {
            // Bouw één adresregel uit de losse onderdelen: "Straat 12, 1000 Brussel".
            $address = trim(sprintf(
                '%s %s, %s %s',
                $data['street'] ?? '',
                $data['houseNumber'] ?? '',
                $data['postalCode'] ?? '',
                $data['municipality'] ?? '',
            ), ' ,');

            $companyId = Company::create([
                'name' => $data['companyName'],
                'vat_number' => $data['vatNumber'] ?? null,
                'address' => $address !== '' ? $address : null,
                'contact_phone' => $data['phone'] ?? null,
            ])->id;
        }

        $user->mentor()->create([
            'company_id' => $companyId,
            'phone' => $data['phone'] ?? null,
        ]);
    }

    /** Reset naar de eerste pagina wanneer de rolfilter verandert. */
    public function updatedRoleFilter(): void
    {
        $this->resetPage();
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
            'users' => User::with('role')
                ->when($this->roleFilter !== '', fn ($q) => $q->whereHas(
                    'role',
                    fn ($r) => $r->where('name', $this->roleFilter)
                ))
                ->latest('id')
                ->paginate(15),
            'roles' => Role::orderBy('name')->get(),
            'companies' => Company::orderBy('name')->get(),
        ]);
    }
}
