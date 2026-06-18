<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\CompetencyFramework;
use App\Models\Role;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.portal')]
#[Title('Beheer')]
class Dashboard extends Component
{
    public function render()
    {
        // Aantal gebruikers per rol (één query), zodat het overzicht meegroeit met de data.
        $perRol = User::query()
            ->selectRaw('role_id, COUNT(*) as aantal')
            ->groupBy('role_id')
            ->pluck('aantal', 'role_id');

        $rollen = Role::orderBy('id')->get()->map(fn ($rol) => [
            'naam' => ucfirst($rol->name),
            'aantal' => (int) ($perRol[$rol->id] ?? 0),
        ]);

        return view('livewire.admin.dashboard', [
            'rollen' => $rollen,
            'aantalGebruikers' => User::count(),
            'aantalBedrijven' => Company::count(),
            'aantalKaders' => CompetencyFramework::count(),
            'aantalLogs' => AuditLog::count(),
            'recenteLogs' => AuditLog::with('user')->latest()->take(5)->get(),
        ]);
    }
}
