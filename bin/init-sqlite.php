<?php

// Vytvorenie SQLite databázy
$databasePath = __DIR__ . '/../database/database.sqlite';
$databaseDir = dirname($databasePath);

// Vytvorenie adresára ak neexistuje
if (!is_dir($databaseDir)) {
    mkdir($databaseDir, 0777, true);
}

// Vytvorenie prázdneho SQLite súboru
if (!file_exists($databasePath)) {
    touch($databasePath);
    chmod($databasePath, 0777);
    echo "SQLite databáza bola vytvorená v: $databasePath\n";
} else {
    echo "SQLite databáza už existuje v: $databasePath\n";
}