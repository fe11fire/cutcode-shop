<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\SignInController;
use Mockery;
use Tests\TestCase;
use Domain\Auth\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\AuthController;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use App\Notifications\NewUserNotification;
use App\Listeners\SendEmailNewUserListener;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Auth\SignUpController;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_login_page_success(): void
    {
        $this->get(action([SignInController::class, 'page']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_in_success(): void
    {
        $password = '12345678';
        $email = 'mail@mail.ru';

        $user = UserFactory::new()->create([
            'email' => $email,
            'password' => bcrypt($password),
        ]);
        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $password,
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.sign-up');
    }

    /**
     * @test
     * @return void
     */
    public function it_sign_up_success(): void
    {
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequest::factory()->create([
            'email' => 'mail@mail.ru',
            'password' => '11123456',
            'password_confirmation' => '11123456',
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $request['email'],
        ]);

        $response = $this->post(action([SignUpController::class, 'handle']), $request);

        $response->assertValid();

        $this->assertDatabaseHas('users', [
            'email' => $request['email'],
        ]);

        $user = User::query()->where('email', $request['email'])->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);
        Notification::assertSentTo($user, NewUserNotification::class);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }

    /**
     * @test
     * @return void
     */
    public function it_logout_success(): void
    {
        $email = 'mail@mail.ru';

        $user = UserFactory::new()->create([
            'email' => $email,
        ]);
        $this->actingAs($user)->delete(action([SignInController::class, 'logOut']));

        $this->assertGuest();
    }


    /**
     * @test
     * @return void
     */
    public function it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertViewIs('auth.forgot-password');
    }

    /**
     * @test
     * @return void
     */
    public function it_forgot_Password_page_success(): void
    {
        // Notification::fake();

        // $email = 'mail@mail.ru';

        // $user = UserFactory::new()->create([
        //     'email' => $email,
        // ]);

        // $request = [
        //     'email' => $email,
        // ];

        // $response = $this->post(action([AuthController::class, 'forgotPassword']), $request);
        // $response->assertValid();
        // $response->assertStatus(302);
        // Notification::assertSentTo($user, ResetPassword::class);

        // $response = $this->post(action([AuthController::class, 'forgotPassword']), $request);
        // $response->assertSessionHasErrors(['email']);
        // $response->assertStatus(302);
    }

    /**
     * @test
     * @return void
     */
    public function it_reset_page_success(): void
    {
        // $token = '1111';
        // $this->get(action([AuthController::class, 'reset'], $token))
        //     ->assertOk()
        //     ->assertViewIs('auth.reset-password');
    }

    /**
     * @test
     * @return void
     */
    public function it_reset_Password_page_success(): void
    {
        // Notification::fake();
        // Event::fake();

        // $email = 'mail@mail.ru';

        // $user = User::factory()->create([
        //     'email' => $email,
        // ]);

        // Password::sendResetLink(['email' => $email]);

        // $token = '';

        // Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, &$token) {
        //     $mailData = $notification->toMail($user);
        //     $token = parse_url($mailData->actionUrl, PHP_URL_PATH);
        //     $token = substr($token, 16);
        //     return true;
        // });

        // $password = bcrypt('111123456');

        // $request = [
        //     'email' => $email,
        //     'token' => $token,
        //     'password' => $password,
        //     'password_confirmation' => $password,
        // ];

        // $response = $this->post(action([AuthController::class, 'resetPassword']), $request);
        // $response->assertValid();
        // $response->assertRedirect(route('login'));
    }

    /**
     * @test
     * @return void
     */
    public function it_github_success(): void
    {
        // $response = $this->get(action([AuthController::class, 'github']));
        // $response->assertStatus(302);
        // $response->assertRedirectContains('https://github.com/login');
    }

    /**
     * @test
     * @return void
     */
    public function it_githubCallback_success(): void
    {
        // $this->visit();
        // $response = $this->get(action([AuthController::class, 'github']));
        // $response->assertStatus(302);
        // $response->assertRedirectContains('https://github.com/login');
        // $url = $response->headers->get('location');
        // $parts = parse_url($url);
        // parse_str($parts['query'], $query);

        // $request = [
        //     'code' => '1111',
        //     'state' => $query['state'],
        // ];

        // $response = $this->get(action([AuthController::class, 'githubCallback?code=11111&state=' . $query['state']]));

        // Socialite::shouldReceive('driver->github')->andReturn(true);
        // $response = $this->get('/auth/login/github');
        // $url = $response->headers->get('url');
        // dd($response);
        // $parts = parse_url($url);
        // parse_str($parts['query'], $query);

        // $request = [
        //     'code' => '1111',
        //     'state' => $query['state'],
        // ];

        // dd($request);
    }
}
