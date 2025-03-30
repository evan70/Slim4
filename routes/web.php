<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

/** @var App $app */

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('Hello World!');
    return $response;
});

$app->get('/api/users', function (Request $request, Response $response) {
    $data = ['users' => ['John', 'Jane']];
    $payload = json_encode($data);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});