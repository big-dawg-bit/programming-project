<?php

use App\Livewire\Student\DocumentList;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// Een student moet via de upload-modal een eigen document kunnen toevoegen.
// Dat krijgt categorie 'Eigen'; de gekozen soort wordt als beschrijving bewaard.
it('uploadt een eigen document met een soort', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(DocumentList::class)
        ->call('openUpload')
        ->set('upload', UploadedFile::fake()->create('verslag.pdf', 120, 'application/pdf'))
        ->set('soort', 'CV')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showUpload', false);

    $file = File::first();
    expect($file)->not->toBeNull()
        ->and($file->original_name)->toBe('verslag.pdf')
        ->and($file->category)->toBe('Eigen')
        ->and($file->description)->toBe('CV')
        ->and($file->uploaded_by)->toBe($user->id);

    Storage::disk('local')->assertExists($file->storage_path);
});

// Alleen PDF en DOCX zijn toegestaan (max 10 MB), zoals in het ontwerp.
it('weigert een bestandstype dat niet is toegestaan', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(DocumentList::class)
        ->set('upload', UploadedFile::fake()->create('virus.exe', 10, 'application/octet-stream'))
        ->call('save')
        ->assertHasErrors(['upload']);

    expect(File::count())->toBe(0);
});
