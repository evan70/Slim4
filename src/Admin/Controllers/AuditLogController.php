<?php

namespace App\Admin\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\AuditLog;

class AuditLogController
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function index(Request $request, Response $response): Response
    {
        $page = $request->getQueryParams()['page'] ?? 1;
        $perPage = 50;

        $logs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return $this->view->render($response, 'admin/audit/index.twig', [
            'logs' => $logs
        ]);
    }

    public function show(Request $request, Response $response, array $args): Response
    {
        $log = AuditLog::with('user')->findOrFail($args['id']);

        return $this->view->render($response, 'admin/audit/show.twig', [
            'log' => $log
        ]);
    }
}