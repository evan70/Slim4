<?php

namespace App\Services;

use App\Models\AuditLog;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuditService
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function log(
        string $action,
        ?string $entityType = null,
        ?int $entityId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $userId = $_SESSION['user_id'] ?? null;
        
        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $this->getClientIp(),
            'user_agent' => $this->request->getHeaderLine('User-Agent')
        ]);
    }

    private function getClientIp(): string
    {
        $serverParams = $this->request->getServerParams();
        
        return $serverParams['HTTP_X_FORWARDED_FOR'] 
            ?? $serverParams['REMOTE_ADDR'] 
            ?? '0.0.0.0';
    }
}
