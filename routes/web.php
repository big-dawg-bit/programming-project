<?php

use App\Livewire\Admin\FrameworkManager;
use App\Livewire\Admin\UserManager;
use App\Livewire\Applications\ApplyForm;
use App\Livewire\Applications\ReviewQueue;
use App\Livewire\Applications\ReviewDetail;
use App\Livewire\Docent\Dashboard as DocentDashboard;
use App\Livewire\Docent\Studenten as DocentStudenten;
use App\Livewire\Evaluations\EvaluationForm;
use App\Livewire\Mentor\Dashboard as MentorDashboard;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\DocumentList;
use App\Livewire\Student\EvaluationList;
use App\Livewire\Student\StageOverview;
use App\Livewire\Weeklogs\FinalReportUpload;
use App\Livewire\Weeklogs\WeeklogList;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        $user = auth()->user();

        return match (true) {
            $user->hasRole('student') => redirect()->route('student.dashboard'),
            $user->hasRole('stagecommissie') => redirect()->route('applications.review'),
            $user->hasRole('docent') => redirect()->route('docent.dashboard'),
            $user->hasRole('mentor') => redirect()->route('mentor.dashboard'),
            $user->hasRole('admin') => redirect()->route('admin.users'),
            default => view('dashboard'),
        };
    })->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', UserManager::class)->name('admin.users');
        Route::get('/admin/framework', FrameworkManager::class)->name('admin.framework');
    });

    Route::middleware('role:stagecommissie')->group(function () {
        Route::get('applications/review', ReviewQueue::class)->name('applications.review');
        Route::get('applications/review/{application}', ReviewDetail::class)->name('applications.show');
    });

    Route::middleware('role:docent')->group(function () {
        Route::get('docent', DocentDashboard::class)->name('docent.dashboard');
        Route::get('docent/studenten', DocentStudenten::class)->name('docent.studenten');
        Route::get('stages/{stage}/evaluatie', EvaluationForm::class)->name('evaluations.create');
    });

    Route::middleware('role:mentor')->group(function () {
        Route::get('mentor', MentorDashboard::class)->name('mentor.dashboard');
    });

    Route::middleware('role:student')->group(function () {
        Route::get('applications/create', ApplyForm::class)->name('applications.create');
        Route::get('applications/{application}/edit', ApplyForm::class)->name('applications.edit');
        Route::get('student', StudentDashboard::class)->name('student.dashboard');
        Route::get('mijn-stage', StageOverview::class)->name('student.stage');
        Route::get('weeklogs', WeeklogList::class)->name('weeklogs.index');
        Route::get('evaluaties', EvaluationList::class)->name('student.evaluaties');
        Route::get('documenten', DocumentList::class)->name('student.documenten');
        Route::get('eindrapport', FinalReportUpload::class)->name('final-report.edit');
    });
});

require __DIR__.'/settings.php';
