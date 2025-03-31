<?php

namespace App\Services;

use App\Models\User;
use App\Validation\Validator;

class UserService
{
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function validateUserData(array $data, ?int $userId = null): Validator
    {
        $rules = [
            'name' => ['required', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email' . ($userId ? ",$userId" : '')],
            'password' => $userId ? ['min:8'] : ['required', 'min:8'],
            'is_admin' => ['boolean'],
            'is_active' => ['boolean']
        ];

        return $this->validator->validate($data, $rules);
    }

    public function createUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_admin' => $data['is_admin'] ?? false,
            'is_active' => $data['is_active'] ?? true
        ];

        return User::create($userData);
    }

    public function updateUser(User $user, array $data): bool
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_admin' => $data['is_admin'] ?? $user->is_admin,
            'is_active' => $data['is_active'] ?? $user->is_active
        ];

        if (!empty($data['password'])) {
            $userData['password'] = $data['password'];
        }

        return $user->update($userData);
    }

    public function toggleTwoFactor(User $user, string $secret): bool
    {
        return $user->update([
            'two_factor_secret' => $secret,
            'two_factor_recovery_codes' => $this->generateRecoveryCodes()
        ]);
    }

    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = bin2hex(random_bytes(10));
        }
        return $codes;
    }
}