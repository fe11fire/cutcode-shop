<?php

namespace App\Routing;

use App\Contracts\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ThumbnailController;
use Illuminate\Contracts\Routing\Registrar;

class AppRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {
            // dd('1');
            Route::get('/storage/public/images/{dir}/{method}/{size}/{file}/get', ThumbnailController::class)
                ->where('method', 'resize|crop|fit')
                ->where('size', '\d+x\d+')
                // ->where('file', '.+\.(png|jpg|gif|bmp|jpeg)$')
                ->name('thumbnail');
            Route::get('/', HomeController::class)->name('home');
            // dd('111');

        });
    }
}
