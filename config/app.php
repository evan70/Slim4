<?php

use App\Middleware\SessionManager;

// Registrácia middleware
$app->add(SessionManager::class);