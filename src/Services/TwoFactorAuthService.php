<?php

namespace App\Services;

use PragmaRX\Google2FA\Google2FA;
use App\Models\User;

class TwoFactorAuthService
{
    private $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function generateQRCode(User $user, string $secretKey): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secretKey
        );
    }

    public function verify(string $secretKey, string $code): bool
    {
        return $this->google2fa->verifyKey($secretKey, $code);
    }

    public function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = bin2hex(random_bytes(10));
        }
        return $codes;
    }

    public function verifyRecoveryCode(User $user, string $code): bool
    {
        if (!$user->twoFactorAuth) {
            return false;
        }

        $recoveryCodes = json_decode($user->twoFactorAuth->recovery_codes, true);
        
        if (!is_array($recoveryCodes)) {
            return false;
        }

        $index = array_search($code, $recoveryCodes);
        
        if ($index === false) {
            return false;
        }

        // Remove used recovery code
        unset($recoveryCodes[$index]);
        $user->twoFactorAuth->update([
            'recovery_codes' => json_encode(array_values($recoveryCodes))
        ]);

        return true;
    }

    public function isEnabled(User $user): bool
    {
        return $user->twoFactorAuth && $user->twoFactorAuth->is_enabled;
    }

    public function disable(User $user): void
    {
        if ($user->twoFactorAuth) {
            $user->twoFactorAuth->delete();
        }
    }

    public function getRecoveryCodes(User $user): array
    {
        if (!$user->twoFactorAuth) {
            return [];
        }

        return json_decode($user->twoFactorAuth->recovery_codes, true) ?? [];
    }
}
