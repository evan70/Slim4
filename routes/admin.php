<?php

use App\Admin\Controllers\AdminController;
use App\Admin\Middleware\AdminAuthMiddleware;

$app->group('/dashboard', function ($group) {
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
    })->add(new AdminAuthMiddleware());
});
