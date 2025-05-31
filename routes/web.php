<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Livewire\Pages\HomePage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\Auth\VerifyEmailPage;

Route::get('/', HomePage::class)->name('home');

Route::get('/reset-password/{token}', ResetPasswordPage::class)
    ->middleware('guest')
    ->name('password.reset');



Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/'); // or wherever you want
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/verify-email', VerifyEmailPage::class)
    ->middleware('auth')
    ->name('verification.notice');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', HomePage::class)->name('home');
});

Route::get('/reset-password/{token}', ResetPasswordPage::class)
    ->middleware('guest')
    ->name('password.reset');



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
