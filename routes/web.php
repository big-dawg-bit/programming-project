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
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', UserManager::class)->name('admin.users');
    });

    Route::middleware('role:stagecommissie')->group(function () {
        Route::get('applications/review', ReviewQueue::class)
            ->name('applications.review');
    });

    Route::middleware('role:student')->group(function () {
        Route::get('applications/create', ApplyForm::class)->name('applications.create');
        Route::get('student', StudentDashboard::class)->name('student.dashboard');
        Route::get('mijn-stage', StageOverview::class)->name('student.stage');
        Route::get('weeklogs', WeeklogList::class)->name('weeklogs.index');
        Route::get('evaluaties', EvaluationList::class)->name('student.evaluaties');
        Route::get('documenten', DocumentList::class)->name('student.documenten');
        Route::get('eindrapport', FinalReportUpload::class)->name('final-report.edit');
    });
});

require __DIR__.'/settings.php';
