<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/** @var App $app */

$app->get('/', \App\Controllers\HomeController::class . ':index')
    ->setName('home');

$app->get('/api/users', function (Request $request, Response $response) {
    $data = ['users' => ['John', 'Jane']];
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// Document routes
$app->get('/documents', \App\Controllers\DocumentController::class . ':index')
    ->setName('documents.index');

$app->get('/documents/{filename}', \App\Controllers\DocumentController::class . ':show')
    ->setName('documents.show');
