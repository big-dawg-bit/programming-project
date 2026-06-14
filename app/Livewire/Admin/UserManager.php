<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public string $name = '';

    public string $email = '';

    public string $selectedRole = 'student';

    public function createUser(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'selectedRole' => 'required|exists:roles,name',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt(str()->random(32)),
            'role_id' => Role::where('name', $data['selectedRole'])->value('id'),
        ]);

        $this->reset('name', 'email');
        $this->selectedRole = 'student';
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
            'users' => User::with('role')->orderBy('name')->paginate(15),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
