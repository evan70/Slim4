<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;

try {
    // Získanie poslednej migrácie
    $lastMigration = Capsule::table('migrations')
        ->orderBy('id', 'desc')
        ->first();

    if ($lastMigration) {
        // Rollback poslednej migrácie
        $migrationFile = __DIR__ . '/../database/migrations/' . $lastMigration->migration;
        
        if (file_exists($migrationFile)) {
            // Načítanie obsahu migrácie pre identifikáciu tabuliek
            $content = file_get_contents($migrationFile);
            if (preg_match_all('/create\([\'"](\w+)[\'"]/', $content, $matches)) {
                
                Capsule::connection()->transaction(function() use ($matches) {
                    // Odstránenie tabuliek v opačnom poradí (kvôli foreign keys)
                    $tables = array_reverse($matches[1]);
                    foreach ($tables as $table) {
                        if (Capsule::schema()->hasTable($table)) {
                            Capsule::schema()->dropIfExists($table);
                            echo "Tabuľka '$table' bola odstránená\n";
                        }
                    }
                });
                
                // Odstránenie záznamu o migrácii
                Capsule::table('migrations')
                    ->where('id', $lastMigration->id)
                    ->delete();
                    
                echo "Rollback migrácie {$lastMigration->migration} bol úspešný\n";
            } else {
                echo "V migrácii {$lastMigration->migration} neboli nájdené žiadne create table príkazy\n";
            }
        } else {
            echo "Migračný súbor {$lastMigration->migration} nebol nájdený\n";
        }
    } else {
        echo "Žiadne migrácie na rollback\n";
    }
} catch (Exception $e) {
    echo "CHYBA pri rollbacku: " . $e->getMessage() . "\n";
    exit(1);
}
