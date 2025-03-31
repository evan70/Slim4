<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

use App\Models\User;

if ($argc !== 3) {
    echo "Usage: php bin/reset-password.php <email> <new_password>\n";
    exit(1);
}

$email = $argv[1];
$newPassword = $argv[2];

$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found with email: $email\n";
    exit(1);
}

$user->password = $newPassword;
$user->save();

echo "Password reset successfully for user: $email\n";
echo "New password hash: " . $user->password . "\n";
echo "Verification test: " . (password_verify($newPassword, $user->password) ? "SUCCESS" : "FAILED") . "\n";