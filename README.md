# Quotes Application

A Laravel-based quotes management system designed to handle creation, retrieval, and management of inspirational quotes with role-based access control and comprehensive API endpoints.

## 📋 Table of Contents

- [Overview](#overview)
- [Quick Start](#quick-start)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Environment Configuration](#environment-configuration)
- [Development](#development)
- [Project Structure](#project-structure)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [API Documentation](#api-documentation)
- [Database](#database)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)
- [License](#license)

## Overview

The Quotes Application is a full-featured backend system built with Laravel 12 that provides RESTful APIs for managing quotes, categories, and user permissions. It features role-based access control through Spatie's Laravel Permission package and supports PDF export functionality.

### Core Features

- **Quote Management**: Create, read, update, and delete quotes
- **Category Management**: Organize quotes by categories
- **Role-Based Access Control**: Manage permissions and roles for different user types
- **API Authentication**: Secure API endpoints with token-based authentication
- **PDF Export**: Generate PDF exports of quotes using DOMPDF
- **Database**: SQLite for development, easily switchable to MySQL/PostgreSQL
- **Queue Support**: Background job processing via Laravel queues
- **Caching**: Built-in caching layer for improved performance

## Quick Start

```bash
# Clone the repository
git clone https://github.com/vicitjob/quotes.git
cd quotes

# Run setup script (installs dependencies, creates database, runs migrations)
composer run setup

# Start development servers
composer run dev
```

The application will be available at `http://localhost:8000`

## System Requirements

- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 16.0 or higher
- **npm**: 8.0 or higher

### Database Support

- SQLite (default for development)
- MySQL 5.7+
- PostgreSQL 9.6+

## Installation

### Step 1: Clone Repository

```bash
git clone https://github.com/vicitjob/quotes.git
cd quotes
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install Node Dependencies

```bash
npm install
```

### Step 4: Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### Step 5: Database Setup

```bash
# Create SQLite database (automatic on first migration)
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed
```

### Step 6: Build Assets

```bash
npm run build
```

### Step 7: Verify Installation

```bash
# Test API
curl http://localhost:8000/api/health

# View application logs
php artisan pail
```

## Environment Configuration

### Core Settings

```env
APP_NAME=Quotes
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database Configuration
DB_CONNECTION=sqlite
# For MySQL: DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=quotes_db
# DB_USERNAME=root
# DB_PASSWORD=your_password
```

### Authentication

```env
# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# API Token Authentication
API_TOKEN_EXPIRATION=1440  # in minutes
```

### Services

```env
# Mail Configuration
MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com

# Queue Configuration
QUEUE_CONNECTION=database

# Cache Configuration
CACHE_STORE=database

# Filesystem
FILESYSTEM_DISK=local
```

### Development Tools

```env
# Logging
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Debugging
TELESCOPE_ENABLED=false
```

See `.env.example` for all available configuration options.

## Development

### Running Development Servers

```bash
# Start all development services (Laravel, Queue, Pail, Vite)
composer run dev

# Or individually:

# Start Laravel development server
php artisan serve

# Listen to background jobs
php artisan queue:listen --tries=1

# Tail application logs
php artisan pail --timeout=0

# Start Vite development server
npm run dev
```

### Development Commands

```bash
# Run tests
composer test

# Format code
./vendor/bin/pint

# Interactive Tinker REPL
php artisan tinker

# Create migration
php artisan make:migration create_quotes_table

# Create model with migration
php artisan make:model Quote -m

# Create controller
php artisan make:controller Api/QuoteController -r
```

### Code Quality

**Linting & Formatting** (via Laravel Pint):
```bash
./vendor/bin/pint
```

**Testing**:
```bash
# Run all tests
composer test

# Run specific test
php artisan test tests/Feature/QuoteTest.php

# Run tests with coverage
php artisan test --coverage
```

## Project Structure

```
quotes/
├── app/
│   ├── Models/              # Eloquent models
│   │   ├── Quote.php
│   │   ├── Category.php
│   │   └── User.php
│   ├── Http/
│   │   ├── Controllers/     # Route controllers
│   │   │   ├── Api/
│   │   │   │   ├── QuoteController.php
│   │   │   │   └── CategoryController.php
│   │   │   └── AuthController.php
│   │   ├── Middleware/      # Custom middleware
│   │   ├── Requests/        # Form request validation
│   │   └── Resources/       # API resource classes
│   ├── Services/            # Business logic layer
│   ├── Exceptions/          # Custom exceptions
│   ├── Jobs/                # Queued jobs
│   └── Events/              # Application events
├── bootstrap/
│   └── app.php              # Application bootstrap
├── config/
│   ├── app.php              # App configuration
│   ├── database.php         # Database configuration
│   ├── auth.php             # Authentication configuration
│   └── permission.php       # Permission configuration
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/             # Database seeders
│   └── factories/           # Model factories
├── routes/
│   ├── api.php              # API routes
│   ├── web.php              # Web routes
│   └── console.php          # Console commands
├── resources/
│   ├── views/               # Blade templates
│   └── js/                  # Vue/JavaScript components
├── storage/                 # Application storage (logs, uploads)
├── tests/
│   ├── Feature/             # Feature tests
│   ├── Unit/                # Unit tests
│   └── TestCase.php         # Test base class
├── public/                  # Web root
├── .env.example             # Environment template
├── composer.json            # PHP dependencies
├── package.json             # Node dependencies
├── phpunit.xml              # PHPUnit configuration
├── vite.config.js           # Vite configuration
└── artisan                  # Laravel CLI
```

## Key Features

### 1. Quote Management

- Create and manage quotes with multiple categories
- Edit quote content and metadata
- Soft delete support for data retention
- Full-text search capabilities

### 2. User & Authentication

- JWT/Token-based API authentication
- User registration and login
- Password reset functionality
- Email verification

### 3. Permission System

- Role-based access control (RBAC)
- Granular permission management
- Admin, Editor, Viewer roles included
- Custom permission middleware

### 4. Data Export

- Generate PDF exports of quotes
- Customizable export templates
- Batch export operations

### 5. API Features

- RESTful API endpoints
- Pagination support
- Filtering and sorting
- API rate limiting
- CORS support

## Technology Stack

### Backend

- **Framework**: Laravel 12.0
- **Language**: PHP 8.2+
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **ORM**: Eloquent
- **Authentication**: Laravel Sanctum + JWT
- **Authorization**: Spatie Laravel Permission 6.23
- **PDF Generation**: BARRYVDH DOMPDF 3.1

### Frontend/Development

- **Build Tool**: Vite
- **Task Runner**: Concurrently
- **Testing**: PHPUnit 11.5
- **Code Quality**: Laravel Pint 1.24
- **Debugging**: Laravel Sail, Laravel Pail

### DevOps & Deployment

- **Package Manager**: Composer, npm
- **Queue Driver**: Database (development), Redis (production)
- **Caching**: Database/Redis
- **Logging**: Stack driver with single channel

## API Documentation

The complete API documentation is available in [API_DOCUMENTATION.md](./docs/API_DOCUMENTATION.md).

### Quick API Overview

```bash
# Get health status
GET /api/health

# Authentication
POST /api/auth/register
POST /api/auth/login
POST /api/auth/logout

# Quotes
GET    /api/quotes              # List all quotes
POST   /api/quotes              # Create new quote
GET    /api/quotes/{id}         # Get specific quote
PUT    /api/quotes/{id}         # Update quote
DELETE /api/quotes/{id}         # Delete quote

# Categories
GET    /api/categories          # List all categories
POST   /api/categories          # Create category
GET    /api/categories/{id}     # Get specific category

# Users
GET    /api/users               # List all users (admin only)
GET    /api/users/{id}          # Get user profile
PUT    /api/users/{id}          # Update user profile
```

## Database

### Schema Overview

See [ARCHITECTURE.md](./docs/ARCHITECTURE.md) for complete database schema documentation.

### Key Tables

- **users**: User accounts and authentication
- **quotes**: Quote content and metadata
- **categories**: Quote categories
- **roles**: User roles for RBAC
- **permissions**: Granular permission definitions
- **model_has_roles**: User role assignments
- **model_has_permissions**: Direct permission assignments

### Running Migrations

```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Reset database
php artisan migrate:reset

# Refresh (reset + migrate)
php artisan migrate:refresh

# Migrate with seeding
php artisan migrate:seed
```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
php artisan test tests/Feature/QuoteControllerTest.php

# Run with coverage
php artisan test --coverage

# Run unit tests only
php artisan test tests/Unit

# Run feature tests only
php artisan test tests/Feature
```

### Test Structure

```
tests/
├── Feature/
│   ├── QuoteControllerTest.php
│   ├── AuthControllerTest.php
│   └── CategoryControllerTest.php
└── Unit/
    ├── Models/
    ├── Services/
    └── Requests/
```

## Troubleshooting

### Common Issues

#### 1. Database Connection Error

```
SQLSTATE[HY000]: General error: unable to open database file
```

**Solution**:
```bash
touch database/database.sqlite
chmod 666 database/database.sqlite
php artisan migrate
```

#### 2. Permission Denied on Storage

```
The file "storage/logs/laravel.log" could not be opened
```

**Solution**:
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

#### 3. Key Generation Missing

```
No application encryption key has been specified
```

**Solution**:
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Composer Memory Limit

```
The allowed memory size of ... bytes exhausted
```

**Solution**:
```bash
php -d memory_limit=-1 /usr/bin/composer install
```

#### 5. Node Dependencies Issues

```bash
# Clear cache and reinstall
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```

### Getting Help

- Check Laravel documentation: https://laravel.com/docs
- View application logs: `php artisan pail`
- Debug with Tinker: `php artisan tinker`
- Check [TROUBLESHOOTING.md](./docs/TROUBLESHOOTING.md) for more help

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Write/update tests
5. Run linting: `./vendor/bin/pint`
6. Commit (`git commit -m 'Add amazing feature'`)
7. Push (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Development Guidelines

- Follow PSR-12 code standards
- Write tests for new features
- Update documentation
- Keep commits atomic and descriptive
- Use conventional commit messages

## Performance & Optimization

### Caching

```php
// Cache query results
$quotes = Cache::remember('quotes', 60, function () {
    return Quote::all();
});
```

### Database Optimization

```php
// Eager load relationships
$quotes = Quote::with('category', 'author')->get();

// Use pagination
$quotes = Quote::paginate(15);
```

### Code Profiling

```bash
# Enable Telescope for development
TELESCOPE_ENABLED=true

# View at: http://localhost:8000/telescope
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.

---

**Last Updated**: June 13, 2026  
**Maintained by**: Development Team  
**Repository**: https://github.com/vicitjob/quotes
