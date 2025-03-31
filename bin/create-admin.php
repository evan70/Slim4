<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

use App\Models\User;

// Explicitné heslo pre testovanie
$password = 'test123';

$user = User::create([
    'name' => 'Test Admin',
    'email' => 'test@admin.com',
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'is_admin' => true
]);

echo "Admin user created successfully!\n";
echo "ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Password: " . $password . "\n";
