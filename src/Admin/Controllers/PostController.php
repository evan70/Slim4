<?php

namespace App\Admin\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class PostController
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/posts/index.twig', [
            'title' => 'Posts Management'
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/posts/create.twig', [
            'title' => 'Create Post'
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        // TODO: Implementovať ukladanie príspevku
        return $response->withHeader('Location', '/dashboard/posts')->withStatus(302);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        return $this->view->render($response, 'admin/posts/edit.twig', [
            'title' => 'Edit Post',
            'post_id' => $args['id']
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementovať aktualizáciu príspevku
        return $response->withHeader('Location', '/dashboard/posts')->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementovať mazanie príspevku
        return $response->withHeader('Location', '/dashboard/posts')->withStatus(302);
    }
}