<?php

use App\Livewire\Admin\UserManager;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Weeklogs\FinalReportUpload;
use App\Livewire\Weeklogs\WeeklogList;
use Illuminate\Support\Facades\Route;

Route::redirect('/', 'dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', UserManager::class)->name('admin.users');
    });

    Route::livewire('student', StudentDashboard::class)->name('student.dashboard');
    Route::livewire('weeklogs', WeeklogList::class)->name('weeklogs.index');
    Route::livewire('eindrapport', FinalReportUpload::class)->name('final-report.edit');
});

require __DIR__.'/settings.php';
