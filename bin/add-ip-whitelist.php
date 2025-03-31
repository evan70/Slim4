<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

use App\Models\IpWhitelist;

$ip = $argv[1] ?? '127.0.0.1';  // Použite skutočnú IP adresu ako parameter alebo localhost ako default

try {
    IpWhitelist::create([
        'ip_address' => $ip,
        'description' => 'Admin access',
        'created_at' => date('Y-m-d H:i:s')
    ]);

    echo "IP address $ip has been whitelisted successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}