<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

use App\Models\User;

// Získanie vstupov od používateľa
echo "Creating admin user\n";
echo "Enter name: ";
$name = trim(fgets(STDIN));

echo "Enter email: ";
$email = trim(fgets(STDIN));

echo "Enter password: ";
$password = trim(fgets(STDIN));

try {
    // Kontrola či email už existuje
    if (User::where('email', $email)->exists()) {
        throw new Exception("User with email $email already exists");
    }

    // Vytvorenie admin používateľa
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'is_admin' => true,
        'email_verified_at' => date('Y-m-d H:i:s') // Použijeme PHP date() namiesto now()
    ]);

    echo "\nAdmin user created successfully!\n";
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";

} catch (Exception $e) {
    echo "\nError: " . $e->getMessage() . "\n";
    exit(1);
}
