<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController; // login and logout
use App\Http\Controllers\Auth\ConfirmablePasswordController; // confirm password
use App\Http\Controllers\Auth\EmailVerificationNotificationController; // send email verification notification
use App\Http\Controllers\Auth\EmailVerificationPromptController; // display email verification promt
use App\Http\Controllers\Auth\NewPasswordController; // display password reset view
use App\Http\Controllers\Auth\PasswordController; // update password
use App\Http\Controllers\Auth\PasswordResetLinkController; // display password reset link request view
use App\Http\Controllers\Auth\RegisteredUserController; // display register view
use App\Http\Controllers\Auth\VerifyEmailController; // verify email
use Illuminate\Support\Facades\Route; // define routes

Route::middleware('guest')->group(function () { // middleware for non logged in users
    Route::get('register', [RegisteredUserController::class, 'create'])// display register view
        ->name('register'); // name of the route

    Route::post('register', [RegisteredUserController::class, 'store']); // store new user

    Route::get('login', [AuthenticatedSessionController::class, 'create'])// display login view
        ->name('login'); // name of the route

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create']) // display password reset link request view
        ->name('password.request'); // name of the route

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])// handle password reset link request
        ->name('password.email'); // name of the route

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])// display password reset view
        ->name('password.reset'); // name of the route

    Route::post('reset-password', [NewPasswordController::class, 'store'])// handle password reset request
        ->name('password.store'); // name of the route
});

Route::middleware('auth')->group(function () {// middleware for loged in user
    Route::get('verify-email', EmailVerificationPromptController::class)// display email verification promt view
        ->name('verification.notice'); // name of the route

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)// verify email
        ->middleware(['signed', 'throttle:6,1'])// limit resend request
        ->name('verification.verify'); // name of the route

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])// send email verification notification
        ->middleware('throttle:6,1')// limit reset request
        ->name('verification.send'); // name of the route

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])// display confirm password view
        ->name('password.confirm'); // name of the route

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']); // handle confirm password request

    Route::put('password', [PasswordController::class, 'update'])->name('password.update'); // update passsword

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']) // handle logout request
        ->name('logout'); // name of the command
});
