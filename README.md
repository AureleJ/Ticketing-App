# Ticketing App

I've created a ticketing system with 4 different implementations :
- [Laravel](#laravel)
- [Laravel with REST API](#laravel-with-rest-api)
- [PHP implementation](#php)
- [Static HTML/CSS/JS](#static)

## Stack Comparison

| Version | Tech Stack |
|---------|------------|
| **Laravel** | PHP 8.2+ / Laravel 12 / Breeze / SQLite |
| **Laravel API** | PHP 8.2+ / Laravel 12 / REST API / Breeze / SQLite |
| **PHP** | PHP / Custom ORM / Raw SQL |
| **Static** | HTML / CSS / Vanilla JavaScript |

## Prerequisites

- **PHP 8.2+**
- **Composer**
- **Node.js 18+**
- **SQLite**

## Versions

### Laravel

Full web version built with Laravel.

**Features**:

- User authentication (Laravel Breeze)
- Ticket creation, tracking, and management
- Multi-client support
- Project organization
- Role-based dashboard

**Installation**:

```bash
cd version-laravel
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed 
```

**Start Development**:

```bash
php artisan serve      # Terminal 1
npm run dev            # Terminal 2
```

(Default test account: adminuser / password: 123)

**Access**: http://localhost:8000

---

### Laravel with REST API

Full web version in Laravel with a REST API.

**Features**:
- API request JSON format (Create for Tickets, Projects, Clients | Update only for Tickets)
- User authentication (Breeze)
- Ticket creation, tracking, and management
- Multi-client support
- Project organization
- Role-based dashboard

**Installation**:

```bash
cd version-laravel-api
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

**Start Development**:

```bash
php artisan serve      # Terminal 1
npm run dev            # Terminal 2
```

**Access**: http://localhost:8000

**API Endpoints**:

```plaintext
POST   /api/tickets          Create ticket
GET    /api/tickets          List all tickets
GET    /api/tickets/{id}     Get ticket details
PUT    /api/tickets/{id}     Update ticket
DELETE /api/tickets/{id}     Delete ticket

POST   /api/projects         Create project
GET    /api/projects         List all projects
GET    /api/projects/{id}    Get project details
PUT    /api/projects/{id}    Update project
DELETE /api/projects/{id}    Delete project

POST   /api/clients          Create client
GET    /api/clients          List all clients
GET    /api/clients/{id}     Get client details
PUT    /api/clients/{id}     Update client
DELETE /api/clients/{id}     Delete client
```

---

### PHP

Pure PHP version without any framework. Features a custom ORM.

**Features**:
- Entity-based data models
- Custom repository pattern
- Service layer implementation

(Use MAMP/XAMPP to serve the PHP files. Initialize the database via Database/db-init.php)

---

### Static

Static version built in HTML, CSS, and JavaScript.

No installation required. Just open `index.html` in a web browser.
