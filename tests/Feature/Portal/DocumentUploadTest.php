<?php

use App\Livewire\Student\DocumentList;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

// Een student moet via de upload-modal een document kunnen toevoegen,
// dat daarna in de gekozen categorie verschijnt.
it('uploadt een document naar de gekozen categorie', function () {
    Storage::fake('local');
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(DocumentList::class)
        ->call('openUpload')
        ->set('upload', UploadedFile::fake()->create('verslag.pdf', 120, 'application/pdf'))
        ->set('uploadCategorie', 'Overeenkomst')
        ->set('beschrijving', 'Ondertekende overeenkomst')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('showUpload', false);

    $file = File::first();
    expect($file)->not->toBeNull()
        ->and($file->original_name)->toBe('verslag.pdf')
        ->and($file->category)->toBe('Overeenkomst')
        ->and($file->description)->toBe('Ondertekende overeenkomst')
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
