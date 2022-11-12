<?php

namespace Tests\Feature\Auth\Actions;

use Domain\Auth\Contracts\RegisterNewUserContract;
use Domain\Auth\DTOs\NewUserDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterNewUserActionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_success_user_created(): void
    {
        $email = 'mail@mail.ru';
        $this->assertDatabaseMissing('users', [
            'email' => $email
        ]);
        $action = app(RegisterNewUserContract::class);

        $action(NewUserDTO::make('Test', $email, '12345678'));

        $this->assertDatabaseHas('users', [
            'email' => $email
        ]);
    }
}
