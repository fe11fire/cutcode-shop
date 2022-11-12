<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use Tests\TestCase;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use Database\Factories\UserFactory;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $token;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = UserFactory::new()->create();
        $this->token = Password::createToken($this->user);
    }


    /**
     * @test
     * @return void
     */
    public function it_page_success(): void
    {
        $this->get(action([ResetPasswordController::class, 'page'], $this->token))
            ->assertOk()
            ->assertViewIs('auth.reset-password');
    }

    /**
     * @test
     * @return void
     */
    public function it_handle(): void
    {
        $password = '12345678';
        $password_confirmation = '12345678';

        Password::shouldReceive('reset')->once()->withSomeOfArgs([
            'email' => $this->user->email,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
            'token' => $this->token
        ])->andReturn(Password::PASSWORD_RESET);

        $response = $this->post(action([ResetPasswordController::class, 'handle']), [
            'email' => $this->user->email,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
            'token' => $this->token
        ]);

        $response->assertRedirect(action([SignInController::class, 'page']));
    }

    /**
     * @test
     * @return void
     */
    public function it_handle_success(): void
    {
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
