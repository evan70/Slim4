<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

// Načítanie všetkých migračných súborov
$migrationsPath = __DIR__ . '/../database/migrations';
$migrations = glob($migrationsPath . '/*.php');

// Kontrola formátu názvov migračných súborov
$validMigrations = [];
$invalidMigrations = [];
$duplicateChecks = [];

foreach ($migrations as $migration) {
    $filename = basename($migration);
    
    // Kontrola správneho formátu názvu (YYYY_MM_DD_HHMMSS_name.php)
    if (!preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_.*\.php$/', $filename)) {
        $invalidMigrations[] = $filename;
        continue;
    }
    
    // Kontrola duplicitných názvov (bez timestampu)
    $name = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $filename);
    if (isset($duplicateChecks[$name])) {
        throw new Exception("Našla sa duplicitná migrácia: $filename a " . $duplicateChecks[$name]);
    }
    $duplicateChecks[$name] = $filename;
    $validMigrations[] = $migration;
}

// Výpis chýb ak existujú nesprávne pomenované migrácie
if (!empty($invalidMigrations)) {
    echo "CHYBA: Nasledujúce migrácie nemajú správny formát názvu (YYYY_MM_DD_HHMMSS_name.php):\n";
    foreach ($invalidMigrations as $invalid) {
        echo "- $invalid\n";
    }
    exit(1);
}

// Vytvorenie tabuľky pre sledovanie migrácií ak neexistuje
use Illuminate\Database\Capsule\Manager as Capsule;

if (!Capsule::schema()->hasTable('migrations')) {
    Capsule::schema()->create('migrations', function ($table) {
        $table->id();
        $table->string('migration');
        $table->timestamp('executed_at');
    });
    echo "Vytvorená tabuľka migrations\n";
}

// Spustenie migrácií
foreach ($validMigrations as $migration) {
    $migrationName = basename($migration);
    
    try {
        // Kontrola či migrácia už bola spustená
        $executed = Capsule::table('migrations')
            ->where('migration', $migrationName)
            ->exists();
        
        if (!$executed) {
            // Kontrola či tabuľky už neexistujú
            $content = file_get_contents($migration);
            if (preg_match_all('/create\([\'"](\w+)[\'"]/', $content, $matches)) {
                foreach ($matches[1] as $table) {
                    if (Capsule::schema()->hasTable($table)) {
                        throw new Exception("Tabuľka '$table' už existuje");
                    }
                }
            }
            
            // Spustenie migrácie v transakcii
            Capsule::connection()->transaction(function() use ($migration, $migrationName) {
                require $migration;
                
                // Zaznamenanie úspešnej migrácie
                Capsule::table('migrations')->insert([
                    'migration' => $migrationName,
                    'executed_at' => date('Y-m-d H:i:s')
                ]);
            });
            
            echo "Migrácia $migrationName bola úspešne spustená\n";
        } else {
            echo "Migrácia $migrationName už bola spustená predtým\n";
        }
    } catch (Exception $e) {
        echo "CHYBA pri migrácii $migrationName: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Všetky migrácie boli dokončené\n";
