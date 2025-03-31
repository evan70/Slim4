<?php

use App\Admin\Controllers\AdminController;
use App\Admin\Controllers\PostController;
use App\Admin\Controllers\UserController;

$app->group('/dashboard', function ($group) use ($app) {
    // Public routes
    $group->get('/login', [AdminController::class, 'login'])->setName('admin.login');
    $group->post('/login', [AdminController::class, 'login']);
    $group->get('/logout', [AdminController::class, 'logout'])->setName('admin.logout');

    // Protected routes
    $group->group('', function ($group) {
        // Dashboard
        $group->get('', [AdminController::class, 'dashboard'])->setName('admin.dashboard');
        
        // Users routes
        $group->get('/users', [AdminController::class, 'users'])->setName('admin.users');
        $group->get('/users/create', [AdminController::class, 'createUser'])->setName('admin.users.create');
        $group->post('/users', [AdminController::class, 'storeUser'])->setName('admin.users.store');
        $group->get('/users/{id}/edit', [AdminController::class, 'editUser'])->setName('admin.users.edit');
        $group->post('/users/{id}', [AdminController::class, 'updateUser'])->setName('admin.users.update');
        $group->get('/users/{id}/delete', [AdminController::class, 'deleteUser'])->setName('admin.users.delete');
        
        // Posts routes
        $group->get('/posts', [PostController::class, 'index'])->setName('admin.posts');
        $group->get('/posts/create', [PostController::class, 'create'])->setName('admin.posts.create');
        $group->post('/posts', [PostController::class, 'store'])->setName('admin.posts.store');
        $group->get('/posts/{id}', [PostController::class, 'edit'])->setName('admin.posts.edit');
        $group->put('/posts/{id}', [PostController::class, 'update'])->setName('admin.posts.update');
        $group->delete('/posts/{id}', [PostController::class, 'delete'])->setName('admin.posts.delete');
        
        // Settings route
        $group->get('/settings', [AdminController::class, 'settings'])->setName('admin.settings');
        
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

// Documents routes
$app->group('/admin/documents', function ($group) {
    $group->get('', [DocumentController::class, 'index'])
        ->setName('admin.documents');
    $group->get('/create', [DocumentController::class, 'create'])
        ->setName('admin.documents.create');
    $group->post('', [DocumentController::class, 'store'])
        ->setName('admin.documents.store');
    $group->get('/{filename}', [DocumentController::class, 'edit'])
        ->setName('admin.documents.edit');
    $group->post('/preview', [DocumentController::class, 'preview'])
        ->setName('admin.documents.preview');
})->add($app->getContainer()->get('admin_auth_middleware'));
