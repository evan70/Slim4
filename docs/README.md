# Slim4 Admin Panel

Moderný administračný panel vytvorený pomocou Slim Framework 4, Twig šablón a Tailwind CSS.

## Použité technológie

- PHP 8.1+
- Slim Framework 4
- Twig šablóny
- Tailwind CSS
- MySQL/MariaDB/SQLite
- PHP-DI (Dependency Injection)
- Eloquent ORM

## Funkcie

- [x] Moderné admin rozhranie s Tailwind CSS
- [x] PSR-4 autoloading
- [x] Dependency Injection s PHP-DI
- [x] Twig šablónovací systém
- [x] Admin autentifikácia
- [x] Responzívny dizajn
- [x] Migračný systém
- [ ] Správa používateľov
- [ ] Správa rolí a oprávnení
- [ ] Správa médií
- [ ] Správa nastavení
- [ ] Protokol aktivít

## Inštalácia

1. Naklonovanie repozitára
```bash
git clone https://github.com/evan70/Slim4.git
cd Slim4
```

2. Inštalácia závislostí
```bash
composer install
```

3. Vytvorenie konfiguračného súboru
```bash
cp .env.example .env
```

4. Nastavenie databázy
   
   Pre SQLite:
   ```bash
   # Vytvorenie SQLite databázy
   php bin/init-sqlite.php
   
   # Upravte .env na použitie SQLite
   DB_CONNECTION=sqlite
   DB_DATABASE=database/database.sqlite
   ```
   
   Pre MySQL:
   ```bash
   # Upravte .env podľa vašej MySQL konfigurácie
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=slim4_admin
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Spustenie migrácií
```bash
# Spustenie všetkých nových migrácií
php bin/migrate.php

# Rollback poslednej migrácie
php bin/migrate-rollback.php
```

6. Spustenie vývojového servera
```bash
php -S localhost:8080 -t public
```

## Štruktúra projektu

```
├── bin/                # Príkazové skripty
│   ├── migrate.php    # Spustenie migrácií
│   └── init-sqlite.php # Inicializácia SQLite
├── database/          # Databázové súbory
│   └── migrations/    # Migračné súbory
├── public/            # Verejný adresár
│   ├── index.php     # Vstupný bod aplikácie
│   └── .htaccess     # Apache konfigurácia
├── routes/            # Definície ciest
│   ├── web.php       # Verejné cesty
│   └── admin.php     # Admin cesty
├── src/               # Zdrojový kód
│   ├── Admin/        # Admin funkcionalita
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Controllers/  # Kontroléry
│   ├── Models/       # Databázové modely
│   └── Middleware/   # Middleware
├── templates/         # Twig šablóny
│   └── admin/        # Admin šablóny
└── vendor/           # Composer závislosti
```

## Migračný systém

### Prehľad
Projekt používa robustný migračný systém založený na Eloquent ORM s dodatočnými bezpečnostnými kontrolami:

- Automatická validácia formátu názvov migračných súborov
- Detekcia a prevencia duplicitných migrácií
- Transakčné spracovanie migrácií
- Inteligentný rollback systém
- Automatické sledovanie vykonaných migrácií

### Štruktúra migračných súborov

Migračné súbory musia dodržiavať nasledujúci formát názvu:
```
YYYY_MM_DD_HHMMSS_nazov_migracie.php
```

Príklad:
```
2024_01_09_000000_create_users_table.php
```

### Vytvorenie novej migrácie

1. Vytvorte nový súbor v `database/migrations/` s korektným názvom
2. Použite nasledujúcu základnú štruktúru:

```php
<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('nazov_tabulky', function ($table) {
    $table->id();
    $table->string('stlpec');
    $table->timestamps();
});
```

### Príkazy pre migrácie

```bash
# Spustenie všetkých nových migrácií
php bin/migrate.php

# Rollback poslednej migrácie
php bin/migrate-rollback.php
```

### Bezpečnostné kontroly

Systém automaticky kontroluje:
- Správny formát názvu migračného súboru
- Duplicitné názvy migrácií
- Existenciu tabuliek pred vytvorením
- Úspešnosť vykonania migrácie

### Chybové hlášky

Systém poskytuje jasné chybové hlášky pre:
- Nesprávny formát názvu súboru
- Duplicitné migrácie
- Existujúce tabuľky
- Chyby pri vykonávaní migrácií
- Problémy pri rollbacku

### Príklady použitia

1. Vytvorenie novej migrácie:
```php
<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('products', function ($table) {
    $table->id();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 10, 2);
    $table->foreignId('category_id')->constrained();
    $table->timestamps();
});
```

2. Migrácia s reláciami:
```php
<?php
use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('orders', function ($table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('product_id')->constrained();
    $table->integer('quantity');
    $table->timestamps();
});
```

### Najlepšie praktiky

1. Vždy používajte timestamp v názve migrácie
2. Jedna migrácia = jedna logická zmena
3. Používajte zmysluplné názvy pre migrácie
4. Testujte migrácie aj rollback
5. Pridávajte komentáre pre komplexné zmeny
6. Používajte foreign key constraints
7. Definujte indexy pre často používané stĺpce

## Vývoj

Projekt používa:
- PSR-4 štandard pre autoloading
- PSR-7 pre HTTP správy
- PSR-11 pre kontajner
- PSR-15 pre HTTP middleware
- Eloquent ORM pre prácu s databázou

## Prispievanie

1. Vytvorte fork repozitára
2. Vytvorte feature branch (`git checkout -b feature/nova-funkcia`)
3. Commitnite zmeny (`git commit -m 'Pridaná nová funkcia'`)
4. Pushnite do branchu (`git push origin feature/nova-funkcia`)
5. Vytvorte Pull Request

## Licencia

Tento projekt je vydaný pod MIT licenciou - pozrite súbor LICENSE pre detaily
