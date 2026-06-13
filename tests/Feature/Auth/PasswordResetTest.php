<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Mail::fake();

    $user = User::factory()->create();

    $response = $this->post('/forgot-password', ['email' => $user->email]);

    $response->assertRedirect(route('password.reset'));

    $this->assertDatabaseHas('password_reset_tokens', [
        'email' => strtolower($user->email),
    ]);
});

test('reset password screen can be rendered', function () {
    $response = $this->get('/reset-password/otp');

    $response->assertStatus(200);
});

test('password can be reset with valid otp', function () {
    $user = User::factory()->create();
    $otp = '123456';

    DB::table('password_reset_tokens')->insert([
        'email' => strtolower($user->email),
        'token' => Hash::make($otp),
        'created_at' => now(),
    ]);

    $this->post('/reset-password/otp', [
        'email' => $user->email,
        'otp' => $otp,
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('password.new'));

    $response = $this
        ->withSession(['password_reset_verified_email' => strtolower($user->email)])
        ->post('/reset-password/new', [
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('login'));

    $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
});
