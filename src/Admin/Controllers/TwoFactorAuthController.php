<?php

namespace App\Admin\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\TwoFactorAuthService;
use App\Models\User;

class TwoFactorAuthController
{
    private $view;
    private $twoFactorAuth;

    public function __construct(Twig $view, TwoFactorAuthService $twoFactorAuth)
    {
        $this->view = $view;
        $this->twoFactorAuth = $twoFactorAuth;
    }

    public function setup(Request $request, Response $response): Response
    {
        $user = User::find($_SESSION['user_id']);
        $secretKey = $this->twoFactorAuth->generateSecretKey();
        $qrCode = $this->twoFactorAuth->generateQRCode($user, $secretKey);
        $recoveryCodes = $this->twoFactorAuth->generateRecoveryCodes();

        $_SESSION['2fa_temp'] = [
            'secret_key' => $secretKey,
            'recovery_codes' => $recoveryCodes
        ];

        return $this->view->render($response, 'admin/2fa/setup.twig', [
            'qrCode' => $qrCode,
            'secretKey' => $secretKey,
            'recoveryCodes' => $recoveryCodes
        ]);
    }

    public function enable(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $temp = $_SESSION['2fa_temp'] ?? null;

        if (!$temp || !isset($data['code'])) {
            return $response->withStatus(400);
        }

        if (!$this->twoFactorAuth->verify($temp['secret_key'], $data['code'])) {
            return $response->withJson(['error' => 'Neplatný verifikačný kód']);
        }

        $user = User::find($_SESSION['user_id']);
        $user->twoFactorAuth()->create([
            'secret_key' => $temp['secret_key'],
            'recovery_codes' => json_encode($temp['recovery_codes']),
            'is_enabled' => true
        ]);

        unset($_SESSION['2fa_temp']);

        return $response->withJson(['success' => true]);
    }

    public function verify(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $user = User::find($_SESSION['user_id']);
        $twoFactor = $user->twoFactorAuth;

        if (!isset($data['code'])) {
            return $response->withStatus(400);
        }

        if ($this->twoFactorAuth->verify($twoFactor->secret_key, $data['code'])) {
            $_SESSION['2fa_verified'] = true;
            return $response->withJson(['success' => true]);
        }

        return $response->withJson(['error' => 'Neplatný verifikačný kód']);
    }

    public function disable(Request $request, Response $response): Response
    {
        $user = User::find($_SESSION['user_id']);
        $user->twoFactorAuth()->delete();

        return $response->withRedirect('/admin/security');
    }
}