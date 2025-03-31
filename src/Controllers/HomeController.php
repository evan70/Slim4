<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Services\MarkdownService;

class HomeController
{
    private $view;
    private $markdown;

    public function __construct(Twig $view, MarkdownService $markdown)
    {
        $this->view = $view;
        $this->markdown = $markdown;
    }

    public function index(Request $request, Response $response)
    {
        $documents = $this->markdown->getDocumentList();
        
        return $this->view->render($response, 'home.twig', [
            'documents' => $documents
        ]);
    }
}
