<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicAssignmentFileController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\SetupController;
use App\Livewire\Dashboard\ContactQueries;
use App\Livewire\Dashboard\EditPage;
use App\Livewire\Dashboard\Home;
use App\Livewire\Dashboard\Login;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/privacy-policy', fn () => app(PublicPageController::class)->show('privacy-policy'))->name('pages.privacy');
Route::get('/terms', fn () => app(PublicPageController::class)->show('terms'))->name('pages.terms');
Route::get('/about-us', fn () => app(PublicPageController::class)->show('about-us'))->name('pages.about');
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/setup', SetupController::class)->name('setup');
Route::redirect('/deploy/setup', '/setup');

Route::get('/assignments/download/{id}', [PublicAssignmentFileController::class, 'showById'])
    ->whereNumber('id')
    ->name('assignments.download');

Route::get('/assignments/files/{filename}', [PublicAssignmentFileController::class, 'show'])
    ->where('filename', '[^/]+')
    ->name('assignments.file');

Route::get('/storage/assignments/{filename}', [PublicAssignmentFileController::class, 'show'])
    ->where('filename', '[^/]+')
    ->name('assignments.storage');

Route::middleware('dashboard.guest')->group(function () {
    Route::livewire('/admin', Login::class)->name('dashboard.login');
});

Route::middleware('dashboard.auth')->prefix('dashboard')->group(function () {
    Route::livewire('/', Home::class)->name('dashboard.home');
    Route::livewire('/pages/{slug}', EditPage::class)->name('dashboard.pages.edit');
    Route::livewire('/contact-queries', ContactQueries::class)->name('dashboard.contact-queries');

    Route::post('/logout', function () {
        session()->forget([
            'dashboard_authenticated',
            'dashboard_admin_id',
            'dashboard_admin_username',
        ]);

        return redirect()->route('dashboard.login');
    })->name('dashboard.logout');
});
