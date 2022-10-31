<?php

namespace Domain\Auth\Routing;

use App\Contracts\RouteRegistrar;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Routing\Registrar;

class AuthRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {
            Route::controller(SignInController::class)->group(function () {
                Route::get('/login', 'page')->name('login');
                Route::post('/login', 'handle')->middleware('throttle:auth')->name('login.handle');

                Route::delete('/logout', 'logOut')->name('logOut');
            });

            Route::controller(SignUpController::class)->group(function () {
                Route::get('/sign-up', 'page')->name('register');
                Route::post('/sign-up', 'handle')->middleware('throttle:auth')->name('register.handle');
            });

            Route::controller(ForgotPasswordController::class)->group(function () {
                Route::get('/forgot-password', 'page')->middleware('guest')->name('forgot');
                Route::post('/forgot-password', 'handle')->middleware('guest')->name('forgot.handle');
            });

            Route::controller(ResetPasswordController::class)->group(function () {
                Route::get('/reset-password/{token}', 'page')->middleware('guest')->name('password.reset');
                Route::post('/reset-password', 'handle')->middleware('guest')->name('password-reset.handle');
            });

            Route::controller(SocialAuthController::class)->group(function () {
                Route::get('/auth/socialite/{driver}', 'redirect')->name('socialite.redirect');
                Route::post('/auth/socialite/{driver}/callback', 'callback')->name('socialite.callback');
            });

            // Route::controller(AuthController::class)->group(function () {




            //     Route::get('/forgot-password', 'forgot')->middleware('guest')->name('password.request');
            //     Route::post('/forgot-password', 'forgotPassword')->middleware('guest')->name('password.email');

            //     Route::get('/reset-password/{token}', 'reset')->middleware('guest')->name('password.reset');
            //     Route::post('/reset-password', 'resetPassword')->middleware('guest')->name('password.update');

            //     Route::get('/auth/socialite/github', 'github')->name('socialite.github');

            //     Route::get('/auth/socialite/github/callback', 'githubCallback')->name('socialite.github.callback');
            // });
        });
    }
}
