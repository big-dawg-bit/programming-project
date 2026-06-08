<?php

namespace App\Livewire\Admin;

use App\Models\Role;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.user-manager', [
            'users' => User::with('role')->orderBy('name')->paginate(15),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
