# TinyLink API

A REST API for creating short URLs, managing them per user, and tracking click statistics. Built with Laravel and authenticated via Laravel Sanctum.

## Tech Stack

- **Framework:** Laravel 12
- **Language:** PHP 8.2+
- **Database:** MySQL 8+ / PostgreSQL 14+ (SQLite for testing)
- **Authentication:** Laravel Sanctum (Personal Access Tokens)
- **ORM:** Eloquent

## Project Setup

### 1. Clone the repository

```bash
git clone <repository-url>
cd "Laravel Intern URL Shortener API Technical Assessment"
```

### 2. Install dependencies

```bash
composer install
```

### 3. Environment configuration

```bash
copy .env.example .env       # Windows
# cp .env.example .env       # macOS / Linux
php artisan key:generate
```

Edit `.env` and set your database credentials:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tinylink
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Database setup

Create the database, then run migrations and seeders:

```bash
php artisan migrate --seed
```

This creates the tables and a demo user:
- **Email:** `demo@tinylink.test`
- **Password:** `password`

### 5. Start the server

```bash
php artisan serve
```

The API is available at `http://localhost:8000`.

---

## Authentication

All protected endpoints require a Sanctum Bearer token. Include it in the `Authorization` header:

```
Authorization: Bearer {your_token}
Accept: application/json
```

Tokens are returned when calling `/api/register` or `/api/login`.

---

## API Endpoints

### Public Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/register` | Register a new user |
| `POST` | `/api/login` | Login and receive a token |
| `GET` | `/{short_code}` | Redirect to original URL (increments click count) |

### Protected Endpoints (Require Bearer Token)

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/logout` | Revoke the current token |
| `GET` | `/api/me` | Get the authenticated user's info |
| `POST` | `/api/urls` | Create a short URL |
| `GET` | `/api/urls` | List all URLs for the authenticated user (paginated) |
| `GET` | `/api/urls/{id}` | Get details of a specific URL |
| `DELETE` | `/api/urls/{id}` | Delete a specific URL |
| `GET` | `/api/urls/{id}/stats` | Get click statistics for a URL |

---

## Example Requests and Responses

### Register

**Request:**

```http
POST /api/register
Content-Type: application/json

{
    "name": "Ada Lovelace",
    "email": "ada@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Ada Lovelace",
            "email": "ada@example.com",
            "updated_at": "2026-09-13T10:00:00.000000Z",
            "created_at": "2026-09-13T10:00:00.000000Z"
        },
        "token": "1|abc123..."
    }
}
```

### Login

**Request:**

```http
POST /api/login
Content-Type: application/json

{
    "email": "ada@example.com",
    "password": "password123"
}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Ada Lovelace",
            "email": "ada@example.com"
        },
        "token": "2|xyz789..."
    }
}
```

**Error Response (401):**

```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Logout

**Request:**

```http
POST /api/logout
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "Logged out successfully",
    "data": null
}
```

### Get Authenticated User

**Request:**

```http
GET /api/me
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "User retrieved successfully",
    "data": {
        "id": 1,
        "name": "Ada Lovelace",
        "email": "ada@example.com"
    }
}
```

### Create Short URL

**Request:**

```http
POST /api/urls
Authorization: Bearer {token}
Content-Type: application/json

{
    "url": "https://example.com/this-is-a-very-long-url"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "URL shortened successfully",
    "data": {
        "id": 1,
        "original_url": "https://example.com/this-is-a-very-long-url",
        "short_code": "aB92x",
        "short_url": "http://localhost:8000/aB92x",
        "click_count": 0,
        "created_at": "2026-09-13T10:00:00.000000Z"
    }
}
```

### Create Short URL with Custom Code (Bonus)

**Request:**

```http
POST /api/urls
Authorization: Bearer {token}
Content-Type: application/json

{
    "url": "https://example.com",
    "custom_code": "my-link"
}
```

**Response (201):**

```json
{
    "success": true,
    "message": "URL shortened successfully",
    "data": {
        "id": 2,
        "original_url": "https://example.com",
        "short_code": "my-link",
        "short_url": "http://localhost:8000/my-link",
        "click_count": 0,
        "created_at": "2026-09-13T10:05:00.000000Z"
    }
}
```

### List URLs (Paginated)

**Request:**

```http
GET /api/urls?page=1&per_page=10
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "URLs retrieved successfully",
    "data": [
        {
            "id": 1,
            "original_url": "https://example.com/this-is-a-very-long-url",
            "short_code": "aB92x",
            "short_url": "http://localhost:8000/aB92x",
            "click_count": 5,
            "created_at": "2026-09-13T10:00:00.000000Z"
        }
    ],
    "meta": {
        "current_page": 1,
        "last_page": 1,
        "per_page": 10,
        "total": 1
    }
}
```

### Get URL Details

**Request:**

```http
GET /api/urls/1
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "URL retrieved successfully",
    "data": {
        "id": 1,
        "original_url": "https://example.com/this-is-a-very-long-url",
        "short_code": "aB92x",
        "short_url": "http://localhost:8000/aB92x",
        "click_count": 5,
        "created_at": "2026-09-13T10:00:00.000000Z"
    }
}
```

**Authorization Error (403):**

```json
{
    "message": "This action is unauthorized."
}
```

### Get URL Statistics (Bonus)

**Request:**

```http
GET /api/urls/1/stats
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "URL statistics retrieved successfully",
    "data": {
        "url": "https://example.com/this-is-a-very-long-url",
        "short_code": "aB92x",
        "click_count": 25
    }
}
```

### Delete URL

**Request:**

```http
DELETE /api/urls/1
Authorization: Bearer {token}
```

**Response (200):**

```json
{
    "success": true,
    "message": "URL deleted successfully",
    "data": null
}
```

### Short URL Redirect

**Request:**

```http
GET /aB92x
```

**Response:** HTTP 302 redirect to `https://example.com/this-is-a-very-long-url`

**Not Found (404):**

```json
{
    "message": "No query results for model [App\\Models\\Url]."
}
```

---

## Validation Rules

| Field | Rules |
|-------|-------|
| `name` | Required, string, max 255 characters |
| `email` | Required, valid email, max 255 characters, unique in `users` table |
| `password` | Required, confirmed, follows Laravel Password defaults (min 8, mixed case, numbers) |
| `url` | Required, must be a valid HTTP/HTTPS URL, max 2048 characters |
| `custom_code` | Optional, alphanumeric with dashes/underscores (`alpha_dash`), 3-32 characters, unique in `urls` table |

Validation errors return HTTP 422:

```json
{
    "message": "The url field is required. (and 1 more error)",
    "errors": {
        "url": ["The url field is required."]
    }
}
```

---

## Database Schema

### `users` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | Primary key |
| `name` | string | |
| `email` | string | Unique |
| `email_verified_at` | timestamp | Nullable |
| `password` | string | Hashed |
| `remember_token` | string | Nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `urls` table

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | Primary key |
| `user_id` | bigint | Foreign key to `users.id`, cascade delete |
| `original_url` | text | The target URL |
| `short_code` | string(32) | Unique, indexed |
| `click_count` | unsigned big int | Default: 0 |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

**Indexes:**
- Unique index on `short_code`
- Composite index on `(user_id, created_at)` for efficient user-scoped queries

### `personal_access_tokens` table

Standard Laravel Sanctum table for API token management.

---

## Relationships

```
User ──hasMany──> Url
Url  ──belongsTo──> User
```

---

## Authorization

A `UrlPolicy` enforces that users can only access their own URLs:

- **view:** User must own the URL (`$user->id === $url->user_id`)
- **delete:** User must own the URL (`$user->id === $url->user_id`)

Attempting to view, delete, or get statistics for another user's URL returns HTTP 403 Forbidden.

---

## Rate Limiting

API requests are limited to **60 requests per minute** per authenticated user or IP address.

---

## Testing

Run the test suite:

```bash
php artisan test
```

The tests use an in-memory SQLite database and cover:

- **AuthApiTest:** Login success/failure, register validation, token access, logout
- **UrlApiTest:** Registration flow, cross-user authorization denial, redirect click tracking

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php        # Register, Login, Logout, Me
│   │   ├── UrlController.php         # CRUD + Stats for URLs
│   │   └── RedirectController.php    # Public short URL redirect
│   ├── Requests/
│   │   ├── RegisterRequest.php       # Registration validation
│   │   ├── LoginRequest.php          # Login validation
│   │   └── StoreUrlRequest.php       # URL creation validation
│   └── Resources/
│       └── UrlResource.php           # JSON transformation for URLs
├── Models/
│   ├── User.php                      # User model with HasApiTokens
│   └── Url.php                       # URL model with relationships
└── Policies/
    └── UrlPolicy.php                 # Authorization for URL access

database/
├── migrations/
│   ├── create_users_table.php
│   ├── create_personal_access_tokens_table.php
│   └── create_urls_table.php
└── seeders/
    └── DatabaseSeeder.php            # Demo user + sample URL

routes/
├── api.php                           # API routes (auth: sanctum)
└── web.php                           # Public redirect route

tests/
└── Feature/
    ├── AuthApiTest.php               # 7 authentication tests
    └── UrlApiTest.php                # 3 URL management tests

docs/
└── TinyLink.postman_collection.json  # Postman collection
```

---

## Postman Collection

Import `docs/TinyLink.postman_collection.json` into Postman.

Set the `base_url` collection variable to `http://localhost:8000`. The Register and Login requests include test scripts that automatically save the returned token to the `token` variable for subsequent requests.

---

## Design Decisions

- **Short code generation:** 6-character random strings using `Str::random()`, checked for uniqueness in a loop before insert. The database unique index serves as the final concurrency guard.
- **Click tracking:** Uses Eloquent's atomic `increment()` to avoid race conditions.
- **URL validation:** The `url:http,https` rule prevents non-web schemes (e.g., `ftp://`, `javascript:`) from being stored.
- **Pagination:** The `per_page` parameter is clamped between 1 and 100 to prevent abuse.
- **Authorization:** Uses Laravel Policy classes for clean, testable ownership checks.
- **Form Requests:** Dedicated FormRequest classes keep validation logic out of controllers.

---

## Assumptions

1. The API is consumed by a frontend client that sends `Accept: application/json` headers.
2. Short codes are case-sensitive and can contain letters, numbers, underscores, and hyphens.
3. No password reset flow is implemented (not required by the assessment).
4. No URL editing endpoint exists after creation (not required by the assessment).
5. The redirect route returns a standard 404 HTML page if the short code is not found.
