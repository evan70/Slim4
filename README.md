# Slim4 Admin Panel

Moderný administračný panel vytvorený pomocou Slim Framework 4, Twig šablón a Tailwind CSS.

## Použité technológie

- PHP 8.1+
- Slim Framework 4
- Twig šablóny
- Tailwind CSS
- MySQL/MariaDB
- PHP-DI (Dependency Injection)

## Funkcie

- [x] Moderné admin rozhranie s Tailwind CSS
- [x] PSR-4 autoloading
- [x] Dependency Injection s PHP-DI
- [x] Twig šablónovací systém
- [x] Admin autentifikácia
- [x] Responzívny dizajn
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

5. Spustenie vývojového servera
```bash
php -S localhost:8080 -t public
```

## Štruktúra projektu

```
├── public/             # Verejný adresár
│   ├── index.php      # Vstupný bod aplikácie
│   └── .htaccess      # Apache konfigurácia
├── routes/             # Definície ciest
│   ├── web.php        # Verejné cesty
│   └── admin.php      # Admin cesty
├── src/                # Zdrojový kód
│   ├── Admin/         # Admin funkcionalita
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Controllers/   # Kontroléry
│   ├── Models/        # Databázové modely
│   └── Middleware/    # Middleware
├── templates/          # Twig šablóny
│   └── admin/         # Admin šablóny
└── vendor/            # Composer závislosti
```

## Vývoj

Projekt používa:
- PSR-4 štandard pre autoloading
- PSR-7 pre HTTP správy
- PSR-11 pre kontajner
- PSR-15 pre HTTP middleware

## Prispievanie

1. Vytvorte fork repozitára
2. Vytvorte feature branch (`git checkout -b feature/nova-funkcia`)
3. Commitnite zmeny (`git commit -m 'Pridaná nová funkcia'`)
4. Pushnite do branchu (`git push origin feature/nova-funkcia`)
5. Vytvorte Pull Request

## Licencia

Tento projekt je vydaný pod MIT licenciou - pozrite súbor LICENSE pre detaily
