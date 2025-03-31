# Changelog

Všetky významné zmeny v projekte sú dokumentované v tomto súbore.
Formát je založený na [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Planned] - Budúce vylepšenia

### API & Performance
- [ ] REST API endpoints
- [ ] GraphQL podpora
- [ ] WebSocket real-time notifikácie
- [ ] Cache systém
- [ ] API dokumentácia
- [ ] Rate limiting

### Security
- [x] 2FA autentifikácia
- [ ] OAuth2 integrácia
- [ ] Audit logging
- [x] Session management
- [ ] IP whitelisting
- [ ] Security monitoring

### UI/UX
- [ ] Dark/Light mode
- [ ] Drag & drop rozhranie
- [ ] Live preview
- [ ] Customizovateľný dashboard
- [ ] Dátové vizualizácie
- [ ] Bulk akcie

### Management
- [ ] RBAC systém
- [ ] Media management
- [ ] Backup systém
- [ ] Import/Export funkcionalita
- [ ] Email notifikácie
- [ ] Task management

## [Unreleased]

### Added
- Implementácia Two-Factor Authentication (2FA)
  - Generovanie a overovanie 2FA kódov
  - QR kódy pre jednoduché nastavenie
  - Systém záložných kódov pre prípad straty zariadenia
  - Možnosť vypnutia 2FA
- Vylepšený systém správy sessions
  - Automatické ukončenie neaktívnych sessions
  - Pravidelná regenerácia session ID
  - Sledovanie aktivity užívateľa
  - Konfigurovateľná životnosť session
- Nové závislosti
  - Pridaný balík pragmarx/google2fa pre 2FA funkcionalitu

### Changed
- Kompletne prepracovaný admin interface
  - Zjednotený dizajn všetkých admin stránok
  - Vylepšená navigácia s fixným siderbarom
  - Konzistentné štýlovanie pomocou Tailwind CSS
  - Responzívny layout pre všetky zariadenia
  - Vylepšené zobrazenie štatistík na dashboarde

### Fixed
- Opravené zobrazovanie sidebar linkov v admin rozhraní
- Zjednotené štýlovanie medzi rôznymi sekciami admin panelu

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
- Základná štruktúra admin panelu
- Integrácia Slim 4 frameworku
- Implementácia Twig šablón
- Tailwind CSS pre štýlovanie
- PSR-4 autoloading
- Dependency Injection s PHP-DI

### Security
- Základné bezpečnostné nastavenia
- Konfigurácia .htaccess
- Ochrana citlivých súborov
- Základná admin autentifikácia
