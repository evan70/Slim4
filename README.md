# Slim Framework Admin Panel

Modern admin panel built with Slim Framework 4, Twig templates, and Tailwind CSS.

## Requirements

- PHP 8.1 or newer
- Composer
- MySQL/MariaDB

## Installation

1. Clone the repository
```bash
git clone [your-repo-url]
cd [your-project-name]
```

2. Install dependencies
```bash
composer install
```

3. Copy `.env.example` to `.env` and configure your environment
```bash
cp .env.example .env
```

4. Set up your database and update `.env` with credentials

5. Run database migrations
```bash
php vendor/bin/phinx migrate
```

## Project Structure

```
├── public/             # Public directory (web root)
├── routes/             # Route definitions
│   ├── web.php
│   └── admin.php
├── src/                # Application source code
│   ├── Admin/         # Admin-related code
│   ├── Controllers/   # Controllers
│   ├── Models/        # Database models
│   └── Middleware/    # Middleware classes
├── templates/          # Twig templates
│   └── admin/         # Admin templates
└── vendor/            # Composer dependencies
```

## Features

- [x] Modern admin interface with Tailwind CSS
- [x] Authentication system
- [x] Dashboard
- [x] Responsive sidebar navigation
- [ ] User management
- [ ] Role-based access control
- [ ] Media management
- [ ] Settings management
- [ ] Activity logs

## Development

To start the development server:
```bash
php -S localhost:8080 -t public
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request