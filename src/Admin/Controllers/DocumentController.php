<?php

namespace App\Admin\Controllers;

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
        
        return $this->view->render($response, 'admin/documents/index.twig', [
            'documents' => $documents
        ]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/documents/edit.twig');
    }

    public function store(Request $request, Response $response): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');
        
        if (strstr($contentType, 'application/json')) {
            $data = json_decode($request->getBody()->getContents(), true);
        } else {
            $data = $request->getParsedBody();
        }
        
        $filename = $data['filename'] ?? '';
        $content = $data['content'] ?? '';

        if (empty($filename) || empty($content)) {
            $response->getBody()->write(json_encode([
                'error' => 'Filename and content are required'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        try {
            $saved = $this->markdown->saveDocument($filename, $content);
            
            if (!$saved) {
                throw new \RuntimeException('Failed to save document');
            }

            if ($request->getHeaderLine('Accept') === 'application/json') {
                $response->getBody()->write(json_encode([
                    'success' => true,
                    'redirect' => '/admin/documents'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(200);
            }
            
            return $response
                ->withHeader('Location', '/admin/documents')
                ->withStatus(302);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => 'Failed to save document: ' . $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $document = $this->markdown->getDocument($args['filename']);
        
        if (!$document) {
            return $response->withStatus(404);
        }

        return $this->view->render($response, 'admin/documents/edit.twig', [
            'document' => $document
        ]);
    }

    public function preview(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $content = $data['content'] ?? '';
        
        $html = $this->markdown->convertToHtml($content);
        
        $payload = json_encode(['html' => $html]);
        $response->getBody()->write($payload);
        
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
