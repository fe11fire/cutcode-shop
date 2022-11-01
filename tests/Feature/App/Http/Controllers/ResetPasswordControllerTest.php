<?php

namespace Tests\Feature\App\Http\Controllers;

use Tests\TestCase;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Auth\ResetPasswordController;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_page_success(): void
    {
        $token = '1111';
        $this->get(action([ResetPasswordController::class, 'page'], $token))
            ->assertOk()
            ->assertViewIs('auth.reset-password');
    }

    /**
     * @test
     * @return void
     */
    public function it_handle_success(): void
    {
        Notification::fake();
        Event::fake();

        $email = 'mail@mail.ru';

        $user = User::newFactory()->create([
            'email' => $email,
        ]);

        Password::sendResetLink(['email' => $email]);

        $token = '';

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, &$token) {
            $mailData = $notification->toMail($user);
            $token = parse_url($mailData->actionUrl, PHP_URL_PATH);
            $token = substr($token, 16);
            return true;
        });

        $password = bcrypt('111123456');

        $request = [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $response = $this->post(action([ResetPasswordController::class, 'handle']), $request);
        $response->assertValid();
        $response->assertRedirect(route('login'));
    }

   
}
