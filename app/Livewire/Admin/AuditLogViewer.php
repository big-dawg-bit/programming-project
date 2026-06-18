<?php

namespace App\Livewire\Admin;

use App\Models\AuditLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.portal')]
#[Title('Logboek')]
class AuditLogViewer extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.admin.audit-log', [
            'logs' => AuditLog::with('user')->latest()->paginate(20),
        ]);
    }
}
