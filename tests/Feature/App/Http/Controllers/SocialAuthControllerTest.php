<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class SocialAuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @return void
     */
    public function it_redirect_success(): void
    {
        $response = $this->get(action([SocialAuthController::class, 'redirect'], 'github'));
        $response->assertStatus(302);
        $response->assertRedirectContains('https://github.com/login');
    }

    /**
     * @test
     * @return void
     */
    public function it_callback_success(): void
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');

        // $abstractUser
        //     ->shouldReceive('getId')
        //     ->andReturn(rand());
        // ->shouldReceive('getName')
        // ->andReturn(str_random(10))
        // ->shouldReceive('getEmail')
        // ->andReturn(str_random(10) . '@gmail.com')
        // ->shouldReceive('getAvatar')
        // ->andReturn('https://en.gravatar.com/userimage');

        // $x = Socialite::shouldReceive('driver->github')->andReturn($abstractUser);
        // dd($x);
        // $response = $this->post(action([SocialAuthController::class, 'callback'], 'github'));
        // dd($response['status']);
        // $response = $this->get(action([SocialAuthController::class, 'redirect'], 'github'));


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
