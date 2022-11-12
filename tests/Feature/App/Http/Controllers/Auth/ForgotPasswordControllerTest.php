<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertViewIs('auth.forgot-password');
    }

    /**
     * @test
     * @return void
     */
    public function it_handle_success(): void
    {
        $email = 'mail@mail.ru';

        $user = UserFactory::new()->create([
            'email' => $email,
        ]);

        $request = [
            'email' => $email,
        ];

        $response = $this->post(action([ForgotPasswordController::class, 'handle']), $request);
        $response->assertRedirect();
        Notification::assertSentTo($user, ResetPassword::class);

        $response = $this->post(action([ForgotPasswordController::class, 'handle']), $request);
        $response->assertSessionHasErrors(['email']);
        $response->assertStatus(302);
    }

    /**
     * @test
     * @return void
     */
    public function it_handle_fail(): void
    {
        $email = 'mail@mail.ru';

        $this->assertDatabaseMissing('users', [
            'email' => $email,
        ]);

        $this->post(action([ForgotPasswordController::class, 'handle']), [
            'email' => $email,
        ])->assertInvalid(['email']);

        Notification::assertNothingSent();
    }
}
