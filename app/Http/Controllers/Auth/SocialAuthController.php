<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Domain\Auth\Models\User;
use DomainException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\RedirectResponse;

class SocialAuthController extends Controller
{
    public function redirect(string $driver)
    {
        try {
            return Socialite::driver($driver)->redirect();
        } catch (\Throwable $e) {
            throw new DomainException('Произошла ошибка или драйвер не поддерживается');
        }
    }

    public function callback(string $driver): RedirectResponse
    {

        if ($driver !== 'github') {
            throw new DomainException('Драйвер не поддерживается');
        }
        $githubUser = Socialite::driver($driver)->user();

        $user = User::query()->updateOrCreate([
            $driver . '_id' => $githubUser->getId(),
        ], [
            'name' => $githubUser->getName(),
            'email' => $githubUser->getEmail(),
            'password' => bcrypt('12345678'),
        ]);



        auth()->login($user);

        return redirect()->intended(route('home'));
    }
}
