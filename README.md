
# Laravel URL Shortener

A URL Shortener application developed using Laravel.

## Features

- User Authentication (Login and Registration)
- Role-Based Access Control
- Three User Roles:
  - SuperAdmin
  - Admin
  - Member
- SuperAdmin can invite Admin users.
- Admin can invite Admin and Member users.
- Admin and Member can generate short URLs.
- Users can view their own generated short URLs.
- SuperAdmin can view all short URLs.
- Admin can view invited users.
- Short URL redirection.
- Short URL export/download functionality.

## Technology Stack

- PHP
- Laravel
- MySQL
- Bootstrap
- JavaScript
- HTML/CSS

## Installation and Setup

### 1. Clone Repository

```bash
git clone https://github.com/YOUR_USERNAME/laravel-url-shortener.git
```

### 2. Navigate to Project

```bash
cd url-shortner
```

### 3. Install Dependencies

```bash
composer install
```
## 4. Database File

SQL File Location: `database/url_shortener.sql`

### 5. Create Environment File

```bash
cp .env.example .env
```

For Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

### 6. Generate Application Key

```bash
php artisan key:generate
```

### 7. Configure Database

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=url_shorter
DB_USERNAME=root
DB_PASSWORD=
```

Create a MySQL database named `url_shorter`.

### 8. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

### 9. Start Laravel Server

```bash
php artisan serve
```

Open the application:

http://127.0.0.1:8000

## AI Tool Usage Disclosure

ChatGPT was used for:

- Debugging Laravel routes, controllers, and validation.
- Understanding Eloquent relationships and database queries.
- Syntax guidance and troubleshooting.

The implementation, testing, and logical decisions were reviewed and worked on as part of the development process.
