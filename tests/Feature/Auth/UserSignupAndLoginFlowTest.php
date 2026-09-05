<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('user can view signup page with proper luxury elements', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
    $response->assertSee('Create Your Studio Account');
    $response->assertSee('Full Name');
    $response->assertSee('Email Address');
    $response->assertSee('Password');
    $response->assertSee('Confirm Password');
    $response->assertSee('Already Registered?');
});

test('user can register, get saved in database, and immediately access dashboard', function () {
    $userData = [
        'name' => 'Aditi Sharma',
        'email' => 'aditi@example.com',
        'password' => 'SecurePass123!',
        'password_confirmation' => 'SecurePass123!',
    ];

    $response = $this->post('/register', $userData);

    // Verify user is persisted in the database
    $this->assertDatabaseHas('users', [
        'name' => 'Aditi Sharma',
        'email' => 'aditi@example.com',
        'role' => 'client',
    ]);

    $user = User::where('email', 'aditi@example.com')->first();
    expect($user)->not->toBeNull();
    expect(Hash::check('SecurePass123!', $user->password))->toBeTrue();

    // Verify authenticated and redirected
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registered user can log out and log back in using database credentials', function () {
    // 1. Create registered user in database
    $user = User::create([
        'name' => 'Rohan Varma',
        'email' => 'rohan@example.com',
        'password' => Hash::make('MyPassword99!'),
        'role' => 'client',
    ]);

    // 2. Access login page
    $loginPage = $this->get('/login');
    $loginPage->assertStatus(200);
    $loginPage->assertSee('Welcome Back');
    $loginPage->assertSee('Client Portal Access');
    $loginPage->assertSee('New to the Studio?');

    // 3. Login with wrong password should fail
    $failResponse = $this->post('/login', [
        'email' => 'rohan@example.com',
        'password' => 'WrongPassword',
    ]);
    $this->assertGuest();
    $failResponse->assertSessionHasErrors('email');

    // 4. Login with correct database credentials succeeds
    $successResponse = $this->post('/login', [
        'email' => 'rohan@example.com',
        'password' => 'MyPassword99!',
    ]);
    $this->assertAuthenticatedAs($user);
    $successResponse->assertRedirect(route('dashboard', absolute: false));
});
