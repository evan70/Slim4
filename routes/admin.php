<?php

use App\Admin\Controllers\AdminController;
use App\Admin\Middleware\AdminAuthMiddleware;

$app->group('/admin', function ($group) {
    $group->get('', [AdminController::class, 'dashboard'])->setName('admin.dashboard');
    $group->get('/login', [AdminController::class, 'login'])->setName('admin.login');
    
    // Přidáme další admin routes zde
    $group->group('/users', function ($group) {
        $group->get('', [AdminController::class, 'users'])->setName('admin.users');
        $group->get('/create', [AdminController::class, 'createUser'])->setName('admin.users.create');
        $group->post('/store', [AdminController::class, 'storeUser'])->setName('admin.users.store');
    });
})->add(new AdminAuthMiddleware());