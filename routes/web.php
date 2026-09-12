<?php

use App\Http\Controllers\OAuthController;
use App\Livewire\Pages\Auth\Login;
use App\Livewire\Pages\Auth\Register;
use App\Livewire\Pages\Main;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::middleware('auth')->group(function () {
    Route::get('chat', Main::class)->name('chat');

    Route::prefix('oauth/{provider}')
        ->name('oauth.')
        ->group(function () {
            Route::get('{service}/redirect', [OAuthController::class, 'redirect'])
                ->name('redirect');
            Route::get('callback', [OAuthController::class, 'callback'])
                ->name('callback');
        });

    Route::get('google_login', function () {
        $url = route('oauth.redirect', [
            'provider' => 'google',
            'service' => 'calendar',
        ]);

        return "<a href='{$url}'>Connect Google Calendar</a>";
    })->name('google');
});
