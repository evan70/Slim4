<?php

use App\Admin\Controllers\AdminController;
use App\Admin\Controllers\TwoFactorAuthController;
use App\Admin\Controllers\AuditLogController;
use App\Admin\Controllers\IpWhitelistController;

$app->group('/dashboard', function ($group) use ($app) {
    // Public routes
    $group->get('/login', [AdminController::class, 'login'])->setName('admin.login');
    $group->post('/login', [AdminController::class, 'login']);
    $group->get('/logout', [AdminController::class, 'logout'])->setName('admin.logout');

    // Protected routes
    $group->group('', function ($group) {
        $group->get('', [AdminController::class, 'dashboard'])->setName('admin.dashboard');
        
        // Users routes
        $group->get('/users', [AdminController::class, 'users'])
            ->setName('admin.users');
        $group->get('/users/create', [AdminController::class, 'createUser'])
            ->setName('admin.users.create');
        $group->post('/users', [AdminController::class, 'storeUser'])
            ->setName('admin.users.store');
        $group->get('/users/{id}/edit', [AdminController::class, 'editUser'])
            ->setName('admin.users.edit');
        $group->post('/users/{id}', [AdminController::class, 'updateUser'])
            ->setName('admin.users.update');
        $group->get('/users/{id}/delete', [AdminController::class, 'deleteUser'])
            ->setName('admin.users.delete');
        
        // Settings routes
        $group->get('/settings', [AdminController::class, 'settings'])
            ->setName('admin.settings');
        $group->post('/settings', [AdminController::class, 'settings'])
            ->setName('admin.settings.update');
    })->add($app->getContainer()->get('admin_auth_middleware'));
});

// 2FA Routes
$app->get('/admin/2fa/setup', [TwoFactorAuthController::class, 'setup']);
$app->post('/admin/2fa/enable', [TwoFactorAuthController::class, 'enable']);
$app->post('/admin/2fa/verify', [TwoFactorAuthController::class, 'verify']);
$app->post('/admin/2fa/disable', [TwoFactorAuthController::class, 'disable']);

// Audit Log Routes
$app->get('/admin/audit', [AuditLogController::class, 'index']);
$app->get('/admin/audit/{id}', [AuditLogController::class, 'show']);

// IP Whitelist Routes
$app->get('/admin/security/ip-whitelist', [IpWhitelistController::class, 'index']);
$app->post('/admin/security/ip-whitelist', [IpWhitelistController::class, 'store']);
$app->delete('/admin/security/ip-whitelist/{id}', [IpWhitelistController::class, 'delete']);
