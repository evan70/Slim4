<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

$password = 'test123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Test password verification:\n";
echo "Password: $password\n";
echo "Hash: $hash\n";
echo "Verification result: " . (password_verify($password, $hash) ? "SUCCESS" : "FAILED") . "\n\n";

// Test s existujúcim užívateľom
use App\Models\User;
$user = User::where('email', 'test@admin.com')->first();

echo "Database user test:\n";
echo "User ID: " . $user->id . "\n";
echo "Stored hash: " . $user->password . "\n";
echo "Verification with 'test123': " . (password_verify('test123', $user->password) ? "SUCCESS" : "FAILED") . "\n";

// Vytvoríme nového užívateľa s rovnakým heslom
$newUser = User::create([
    'name' => 'Password Test User',
    'email' => 'password.test@example.com',
    'password' => 'test123',
    'is_admin' => true
]);

echo "\nNew test user created:\n";
echo "User ID: " . $newUser->id . "\n";
echo "Email: " . $newUser->email . "\n";
echo "Password hash: " . $newUser->password . "\n";
echo "Verification with 'test123': " . (password_verify('test123', $newUser->password) ? "SUCCESS" : "FAILED") . "\n";