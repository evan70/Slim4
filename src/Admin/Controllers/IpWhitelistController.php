<?php

namespace App\Admin\Controllers;

use Slim\Views\Twig;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\IpWhitelist;
use App\Services\AuditService;

class IpWhitelistController
{
    private $view;
    private $auditService;

    public function __construct(Twig $view, AuditService $auditService)
    {
        $this->view = $view;
        $this->auditService = $auditService;
    }

    public function index(Request $request, Response $response): Response
    {
        $ips = IpWhitelist::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return $this->view->render($response, 'admin/security/ip-whitelist.twig', [
            'ips' => $ips
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        $whitelist = IpWhitelist::create([
            'ip_address' => $data['ip_address'],
            'description' => $data['description'],
            'expires_at' => $data['expires_at'] ?? null,
            'created_by' => $_SESSION['user_id']
        ]);

        $this->auditService->log(
            'ip_whitelist_added',
            'ip_whitelist',
            $whitelist->id,
            null,
            $whitelist->toArray()
        );

        return $response->withHeader('Location', '/admin/security/ip-whitelist')
            ->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $whitelist = IpWhitelist::findOrFail($args['id']);
        
        $this->auditService->log(
            'ip_whitelist_removed',
            'ip_whitelist',
            $whitelist->id,
            $whitelist->toArray(),
            null
        );

        $whitelist->delete();

        return $response->withHeader('Location', '/admin/security/ip-whitelist')
            ->withStatus(302);
    }
}