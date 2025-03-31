<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/bootstrap.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Načítanie všetkých migračných súborov
$migrationsPath = __DIR__ . '/../database/migrations';
$migrations = glob($migrationsPath . '/*.php');

// Kontrola formátu názvov migračných súborov
$validMigrations = [];
$invalidMigrations = [];
$duplicateChecks = [];

foreach ($migrations as $migration) {
    $filename = basename($migration);
    if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_.*\.php$/', $filename)) {
        if (in_array($filename, $duplicateChecks)) {
            $invalidMigrations[] = "Duplicitná migrácia: $filename";
        } else {
            $validMigrations[] = $migration;
            $duplicateChecks[] = $filename;
        }
    } else {
        $invalidMigrations[] = $filename;
    }
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
if (!Capsule::schema()->hasTable('migrations')) {
    Capsule::schema()->create('migrations', function (Blueprint $table) {
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
            // Načítanie migračnej triedy
            $migrationClass = require $migration;
            
            // Spustenie migrácie v transakcii
            Capsule::connection()->transaction(function() use ($migrationClass, $migrationName) {
                // Spustenie up() metódy
                $migrationClass->up();
                
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
        echo "CHYBA pri spustení migrácie $migrationName: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "Všetky migrácie boli dokončené\n";
