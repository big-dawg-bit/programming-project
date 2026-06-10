<?php

use App\Livewire\Weeklogs\WeeklogList;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('weeklogs', WeeklogList::class)->name('weeklogs.index');
});

require __DIR__.'/settings.php';
