<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use App\Services\MarkdownService;

class DocumentController
{
    private $view;
    private $markdown;

    public function __construct(Twig $view, MarkdownService $markdown)
    {
        $this->view = $view;
        $this->markdown = $markdown;
    }

    public function index(Request $request, Response $response): Response
    {
        $documents = $this->markdown->getDocumentList();
        
        return $this->view->render($response, 'documents/index.twig', [
            'documents' => $documents
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $document = $this->markdown->getDocument($args['filename']);
        
        if (!$document) {
            return $response->withStatus(404);
        }

        return $this->view->render($response, 'documents/show.twig', [
            'document' => $document
        ]);
    }
}
