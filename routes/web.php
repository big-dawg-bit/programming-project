<?php

use App\Livewire\Admin\UserManager;
use App\Livewire\Applications\ApplyForm;
use App\Livewire\Applications\ReviewQueue;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\DocumentList;
use App\Livewire\Student\EvaluationList;
use App\Livewire\Student\StageOverview;
use App\Livewire\Weeklogs\FinalReportUpload;
use App\Livewire\Weeklogs\WeeklogList;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Na het inloggen komt iedereen op /dashboard uit (Fortify 'home').
    // Stuur elke rol vandaaruit door naar zijn eigen portaal. Rollen zonder
    // eigen portaal (docent, mentor) krijgen voorlopig het standaarddashboard.
    Route::get('dashboard', function () {
        $user = auth()->user();

        return match (true) {
            $user->hasRole('student') => redirect()->route('student.dashboard'),
            $user->hasRole('stagecommissie') => redirect()->route('applications.review'),
            $user->hasRole('admin') => redirect()->route('admin.users'),
            default => view('dashboard'),
        };
    })->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', UserManager::class)->name('admin.users');
    });

    Route::middleware('role:student')->group(function () {
        Route::livewire('applications/create', ApplyForm::class)->name('applications.create');
    });

    Route::middleware('role:stagecommissie')->group(function () {
        Route::livewire('applications/review', ReviewQueue::class)
            ->name('applications.review');
    });

    Route::livewire('student', StudentDashboard::class)->name('student.dashboard');
    Route::livewire('mijn-stage', StageOverview::class)->name('student.stage');
    Route::livewire('weeklogs', WeeklogList::class)->name('weeklogs.index');
    Route::livewire('evaluaties', EvaluationList::class)->name('student.evaluaties');
    Route::livewire('documenten', DocumentList::class)->name('student.documenten');
    Route::livewire('eindrapport', FinalReportUpload::class)->name('final-report.edit');
});

require __DIR__.'/settings.php';
