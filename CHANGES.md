# Changelog

Všetky významné zmeny v projekte sú dokumentované v tomto súbore.
Formát je založený na [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

### Added
- Základná štruktúra admin panelu
- Integrácia Slim 4 frameworku
- Implementácia Twig šablón
- Tailwind CSS pre štýlovanie
- PSR-4 autoloading
- Dependency Injection s PHP-DI
- Základná autentifikácia admin používateľov
- Migračný systém pre databázu
- Responzívny dizajn admin rozhrania

### Changed
- Aktualizácia na PHP 8.1+
- Prechod na Eloquent ORM
- Vylepšená štruktúra routes s použitím skupín
- Optimalizácia middleware systému

### Security
- Implementácia admin autentifikácie
- Zabezpečenie admin routes pomocou middleware
- Validácia formulárových vstupov
- Ochrana proti CSRF útokom
- Bezpečné spracovanie hesiel

## [0.1.0] - 2024-01-15

### Added
- Inicializácia projektu
- Základná adresárová štruktúra
- Composer konfigurácia
- Základné závislosti
- .env konfigurácia
- Dokumentácia v README.md
- Migračný systém
- Docker konfigurácia
- CI/CD pipeline

### Security
- Základné bezpečnostné nastavenia
- Konfigurácia .htaccess
- Ochrana citlivých súborov