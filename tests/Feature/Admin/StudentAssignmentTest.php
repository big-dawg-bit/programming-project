
<?php

use App\Livewire\Admin\StudentAssignment;
use App\Models\Docent;
use App\Models\Mentor;
use App\Models\Role;
use App\Models\Stage;
use App\Models\User;
use Livewire\Livewire;

beforeEach(fn () => $this->seed());

function makeAssignmentUser(string $roleName): User
{
    return User::factory()->create([
        'role_id' => Role::where('name', $roleName)->value('id'),
    ]);
}

it('laat een admin een docent aan een stage toewijzen', function () {
    $stage = Stage::first();
    $docent = Docent::first();

    Livewire::actingAs(makeAssignmentUser('admin'))
        ->test(StudentAssignment::class)
        ->call('assignDocent', $stage->id, $docent->id);

    expect($stage->fresh()->docent_id)->toBe($docent->id);
});

it('laat een admin de docent-toewijzing weghalen', function () {
    $stage = Stage::first();
    $stage->update(['docent_id' => Docent::first()->id]);

    Livewire::actingAs(makeAssignmentUser('admin'))
        ->test(StudentAssignment::class)
        ->call('assignDocent', $stage->id, null);

    expect($stage->fresh()->docent_id)->toBeNull();
});

it('laat een admin een mentor aan een stage toewijzen', function () {
    $stage = Stage::first();
    $mentor = Mentor::first();

    Livewire::actingAs(makeAssignmentUser('admin'))
        ->test(StudentAssignment::class)
        ->call('assignMentor', $stage->id, $mentor->id);

    expect($stage->fresh()->mentor_id)->toBe($mentor->id);
});

it('laat een admin de mentor-toewijzing weghalen', function () {
    $stage = Stage::first();
    $stage->update(['mentor_id' => Mentor::first()->id]);

    Livewire::actingAs(makeAssignmentUser('admin'))
        ->test(StudentAssignment::class)
        ->call('assignMentor', $stage->id, null);

    expect($stage->fresh()->mentor_id)->toBeNull();
});

it('blokkeert een niet-admin op de toewijzingen-pagina', function () {
    $this->actingAs(makeAssignmentUser('student'))
        ->get('/admin/toewijzingen')
        ->assertForbidden();
});
