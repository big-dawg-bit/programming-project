<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\FrameworkManager;
use App\Livewire\Admin\StudentAssignment;
use App\Livewire\Admin\UserManager;
use App\Livewire\Applications\AgreementReview;
use App\Livewire\Applications\ApplyForm;
use App\Livewire\Applications\EditApplication;
use App\Livewire\Applications\ReviewDetail;
use App\Livewire\Applications\ReviewQueue;
use App\Livewire\Bedrijf\Aanvragen;
use App\Livewire\Bedrijf\LogboekList;
use App\Livewire\Bedrijf\MentorToewijzen;
use App\Livewire\Bedrijf\Overeenkomsten;
use App\Livewire\Docent\Dashboard as DocentDashboard;
use App\Livewire\Docent\Eindbeoordeling;
use App\Livewire\Docent\Evaluaties as DocentEvaluaties;
use App\Livewire\Docent\Rapporten as DocentRapporten;
use App\Livewire\Docent\StudentDetail as DocentStudentDetail;
use App\Livewire\Docent\Studenten as DocentStudenten;
use App\Livewire\Docent\Weeklogs as DocentWeeklogs;
use App\Livewire\Evaluations\EvaluationForm;
use App\Livewire\Mentor\Dashboard as MentorDashboard;
use App\Livewire\Mentor\EvaluationDetail;
use App\Livewire\Mentor\StudentDetail;
use App\Livewire\Mentor\StudentList;
use App\Livewire\Student\AgreementUpload;
use App\Livewire\Student\Dashboard as StudentDashboard;
use App\Livewire\Student\DocumentList;
use App\Livewire\Student\EvaluationList;
use App\Livewire\Student\StageApplicationDetail;
use App\Livewire\Student\StageOverview;
use App\Livewire\Weeklogs\FinalReportUpload;
use App\Livewire\Weeklogs\MentorWeeklogList;
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
            $user->hasRole('bedrijf') => redirect()->route('bedrijf.dashboard'),
            $user->hasRole('admin') => redirect()->route('admin.dashboard'),
            default => view('dashboard'),
        };
    })->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', Dashboard::class)->name('admin.dashboard');
        Route::get('/admin/users', UserManager::class)->name('admin.users');
        Route::get('/admin/framework', FrameworkManager::class)->name('admin.framework');
        Route::get('/admin/toewijzingen', StudentAssignment::class)->name('admin.assignments');
    });

    Route::middleware('role:stagecommissie')->group(function () {
        Route::get('applications/review', ReviewQueue::class)->name('applications.review');
        Route::get('applications/review/{application}', ReviewDetail::class)->name('applications.show');
        Route::get('overeenkomsten', AgreementReview::class)->name('applications.agreements');
    });

    Route::middleware('role:docent')->group(function () {
        Route::get('docent', DocentDashboard::class)->name('docent.dashboard');
        Route::get('docent/studenten', DocentStudenten::class)->name('docent.studenten');
        Route::get('docent/studenten/{naam}', DocentStudentDetail::class)->name('docent.student.show');
        Route::get('docent/weeklogs', DocentWeeklogs::class)->name('docent.weeklogs');
        Route::get('docent/evaluaties', DocentEvaluaties::class)->name('docent.evaluaties');
        Route::get('docent/rapporten', DocentRapporten::class)->name('docent.rapporten');
        Route::get('docent/evaluaties/{stage}/eindbeoordeling', Eindbeoordeling::class)->name('docent.eindbeoordeling');
    });
    Route::middleware('role:mentor')->group(function () {
        Route::get('mentor/studenten', StudentList::class)->name('mentor.studenten');
        Route::get('mentor/weeklogs', MentorWeeklogList::class)->name('mentor.weeklogs');
        Route::get('mentor/documenten', App\Livewire\Mentor\DocumentList::class)->name('mentor.documenten');
        Route::get('mentor/evaluaties', App\Livewire\Mentor\EvaluationList::class)->name('mentor.evaluaties');
        Route::get('mentor/evaluaties/{stage}/invullen/{type}', App\Livewire\Mentor\EvaluationForm::class)->name('mentor.evaluatie.invullen');
        Route::get('mentor/evaluaties/bekijken/{evaluation}', EvaluationDetail::class)->name('mentor.evaluatie.bekijken');
        Route::get('mentor/studenten/{stage}', StudentDetail::class)->name('mentor.student.detail');
    });

    Route::middleware('role:mentor')->group(function () {
        Route::get('mentor', MentorDashboard::class)->name('mentor.dashboard');
    });

    Route::middleware('role:bedrijf')->group(function () {
        Route::get('bedrijf', App\Livewire\Bedrijf\Dashboard::class)->name('bedrijf.dashboard');
        Route::get('bedrijf/logboeken', LogboekList::class)->name('bedrijf.logboeken');
        Route::get('bedrijf/mentor-toewijzen', MentorToewijzen::class)->name('bedrijf.toewijzen');
        Route::get('bedrijf/aanvragen', Aanvragen::class)->name('bedrijf.aanvragen');
        Route::get('bedrijf/overeenkomsten', Overeenkomsten::class)->name('bedrijf.overeenkomsten');
    });

    // Gedeeld: docent én mentor evalueren via hetzelfde formulier (scoping zit in EvaluationForm::mount()).
    Route::middleware('role:docent,mentor')->group(function () {
        Route::get('stages/{stage}/evaluatie', EvaluationForm::class)->name('evaluations.create');
    });

    Route::middleware('role:student')->group(function () {
        Route::get('applications/create', ApplyForm::class)->name('applications.create');
        Route::get('applications/{application}/edit', EditApplication::class)->name('applications.edit');
        Route::get('student', StudentDashboard::class)->name('student.dashboard');
        Route::get('mijn-stage', StageOverview::class)->name('student.stage');
        Route::get('mijn-stage/{application}', StageApplicationDetail::class)->name('student.stage.show');
        Route::get('overeenkomst', AgreementUpload::class)->name('student.agreement');
        Route::get('weeklogs', WeeklogList::class)->name('weeklogs.index');
        Route::get('evaluaties', EvaluationList::class)->name('student.evaluaties');
        Route::get('documenten', DocumentList::class)->name('student.documenten');
        Route::get('eindrapport', FinalReportUpload::class)->name('final-report.edit');
        Route::get('evaluaties/{stage}/invullen/{type}', App\Livewire\Student\EvaluationForm::class)->name('student.evaluatie.invullen');
    });
});

require __DIR__.'/settings.php';
