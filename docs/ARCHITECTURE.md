# Architecture Documentation

Detailed technical documentation of the Quotes Application workspace architecture, data models, and system design.

## 📋 Table of Contents

- [Architecture Overview](#architecture-overview)
- [System Architecture](#system-architecture)
- [Data Models](#data-models)
- [Database Schema](#database-schema)
- [Directory Structure](#directory-structure)
- [Design Patterns](#design-patterns)
- [Request Flow](#request-flow)
- [Security Architecture](#security-architecture)
- [Scalability Considerations](#scalability-considerations)

## Architecture Overview

The Quotes Application follows a **layered architecture** pattern with clear separation of concerns:

```
┌─────────────────────────────────────────────────────┐
│            API Clients / Frontend                   │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│              HTTP / REST Layer                      │
│         (Routes, Controllers, Middleware)           │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│           Business Logic Layer                      │
│      (Services, Jobs, Events, Repositories)         │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│            Data Access Layer                        │
│    (Models, Queries, Database Interactions)         │
└─────────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────────┐
│         Database / External Services                │
│    (SQLite/MySQL, Redis, Mail, File Storage)        │
└─────────────────────────────────────────────────────┘
```

## System Architecture

### Components

#### 1. API Gateway & Routing
- **File**: `routes/api.php`
- **Purpose**: Central routing definition for all API endpoints
- **Key Features**:
  - RESTful endpoint definitions
  - Middleware pipeline (auth, throttle, CORS)
  - Route grouping by resource
  - Version prefixing support

#### 2. Controllers
- **Location**: `app/Http/Controllers/Api/`
- **Responsibility**: Handle HTTP requests and responses
- **Pattern**: Resource-based controllers (REST)
- **Key Controllers**:
  - `QuoteController`: Quote CRUD operations
  - `CategoryController`: Category management
  - `AuthController`: Authentication flow
  - `UserController`: User management

#### 3. Request Validation
- **Location**: `app/Http/Requests/`
- **Purpose**: Form request validation with custom rules
- **Benefits**:
  - Centralized validation logic
  - Reusable across controllers
  - Clear request contracts
  - Authorization checks

#### 4. Models & ORM
- **Location**: `app/Models/`
- **Framework**: Eloquent ORM
- **Models**:
  - `User`: User authentication and profile
  - `Quote`: Quote content and metadata
  - `Category`: Quote categorization
  - `Role`, `Permission`: RBAC implementation

#### 5. Services & Business Logic
- **Location**: `app/Services/`
- **Pattern**: Service layer for complex operations
- **Examples**:
  - `QuoteService`: Quote operations
  - `ExportService`: PDF generation
  - `PermissionService`: Permission checking

#### 6. Middleware
- **Location**: `app/Http/Middleware/`
- **Purpose**: Request preprocessing and response postprocessing
- **Key Middleware**:
  - `Authenticate`: JWT/Sanctum authentication
  - `CheckPermission`: Role-based access control
  - `ThrottleRequests`: Rate limiting
  - `CorsMiddleware`: CORS handling

#### 7. Jobs & Queue
- **Location**: `app/Jobs/`
- **Purpose**: Asynchronous task processing
- **Examples**:
  - `ExportQuotesPdf`: Background PDF generation
  - `SendNotificationEmail`: Email notifications
  - `IndexQuotesForSearch`: Search indexing

#### 8. Events & Listeners
- **Location**: `app/Events/` and `app/Listeners/`
- **Pattern**: Event-driven architecture
- **Examples**:
  - `QuoteCreated`: Triggered when quote is created
  - `QuoteDeleted`: Triggered when quote is deleted

## Data Models

### User Model

```php
class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationships
    public function quotes()
    {
        return $this->hasMany(Quote::class, 'created_by');
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Methods
    public function hasPermission($permission)
    {
        return $this->roles()
            ->whereHas('permissions', function ($q) use ($permission) {
                $q->where('name', $permission);
            })->exists();
    }
}
```

### Quote Model

```php
class Quote extends Model
{
    protected $fillable = [
        'title',
        'content',
        'author',
        'category_id',
        'created_by',
        'is_published',
        'views',
        'likes',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'views' => 'integer',
        'likes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'like', "%{$term}%")
                     ->orWhere('content', 'like', "%{$term}%");
    }
}
```

### Category Model

```php
class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    // Relationships
    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    // Accessors
    public function getQuoteCountAttribute()
    {
        return $this->quotes()->count();
    }
}
```

## Database Schema

### Entity Relationship Diagram

```
┌─────────────┐
│   users     │
├─────────────┤
│ id (PK)     │
│ name        │
│ email       │
│ password    │
│ created_at  │
│ updated_at  │
└─────────────┘
       │
       ├──→ (created_by) ┌──────────────┐
       │                  │   quotes     │
       └──────────────────┤──────────────┤
                          │ id (PK)      │
                          │ title        │
                          │ content      │
                          │ author       │
                          │ category_id  │ ──→ ┌────────────────┐
                          │ created_by   │     │  categories    │
                          │ is_published │     ├────────────────┤
                          │ views        │     │ id (PK)        │
                          │ likes        │     │ name           │
                          │ created_at   │     │ description    │
                          │ updated_at   │     │ slug           │
                          └──────────────┘     └────────────────┘
                                │
                                └──→ ┌──────────────┐
                                     │ quote_tags   │
                                     ├──────────────┤
                                     │ quote_id (FK)│
                                     │ tag_id (FK)  │
                                     └──────────────┘
                                             │
                                             └──→ ┌──────────────┐
                                                  │ tags         │
                                                  ├──────────────┤
                                                  │ id (PK)      │
                                                  │ name         │
                                                  └──────────────┘

┌─────────────────────┐
│      roles          │
├─────────────────────┤
│ id (PK)             │
│ name                │
│ display_name        │
│ description         │
└─────────────────────┘
       │
       ├──→ ┌──────────────────────┐
       │    │ role_has_permissions │
       │    ├──────────────────────┤
       │    │ role_id (FK)         │
       │    │ permission_id (FK)   │
       │    └──────────────────────┘
       │            │
       │            └──→ ┌──────────────┐
       │                 │ permissions  │
       │                 ├──────────────┤
       │                 │ id (PK)      │
       │                 │ name         │
       │                 │ display_name │
       │                 └──────────────┘
       │
       └──→ ┌──────────────────────┐
            │ model_has_roles      │
            ├──────────────────────┤
            │ role_id (FK)         │
            │ model_id (FK)        │
            │ model_type           │
            └──────────────────────┘
                     │
                     └──→ users
```

### Table Definitions

#### users
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);
```

#### quotes
```sql
CREATE TABLE quotes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    author VARCHAR(255) NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    is_published BOOLEAN DEFAULT FALSE,
    views INT UNSIGNED DEFAULT 0,
    likes INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_category_id (category_id),
    INDEX idx_created_by (created_by),
    INDEX idx_is_published (is_published),
    FULLTEXT idx_fulltext (title, content)
);
```

#### categories
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
);
```

#### tags
```sql
CREATE TABLE tags (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### quote_tags (pivot table)
```sql
CREATE TABLE quote_tags (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    quote_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_quote_tag (quote_id, tag_id),
    INDEX idx_tag_id (tag_id)
);
```

#### roles
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### permissions
```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### role_has_permissions
```sql
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);
```

#### model_has_roles
```sql
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    INDEX idx_model (model_id, model_type)
);
```

## Directory Structure

```
quotes/
├── app/
│   ├── Console/
│   │   └── Commands/          # Artisan commands
│   │       └── GenerateReports.php
│   │
│   ├── Exceptions/            # Custom exceptions
│   │   ├── QuoteNotFoundException.php
│   │   ├── UnauthorizedException.php
│   │   └── ValidationException.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── QuoteController.php
│   │   │   │   ├── CategoryController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── RoleController.php
│   │   │   └── BaseController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── Authenticate.php
│   │   │   ├── CheckPermission.php
│   │   │   ├── ThrottleRequests.php
│   │   │   └── CorsMiddleware.php
│   │   │
│   │   ├── Requests/
│   │   │   ├── LoginRequest.php
│   │   │   ├── RegisterRequest.php
│   │   │   ├── StoreQuoteRequest.php
│   │   │   ├── UpdateQuoteRequest.php
│   │   │   └── StoreCategoryRequest.php
│   │   │
│   │   └── Resources/
│   │       ├── QuoteResource.php
│   │       ├── CategoryResource.php
│   │       └── UserResource.php
│   │
│   ├── Models/                # Eloquent models
│   │   ├── User.php
│   │   ├── Quote.php
│   │   ├── Category.php
│   │   ├── Tag.php
│   │   ├── Role.php
│   │   └── Permission.php
│   │
│   ├── Services/              # Business logic layer
│   │   ├── QuoteService.php
│   │   ├── CategoryService.php
│   │   ├── ExportService.php
│   │   ├── PermissionService.php
│   │   └── AuthService.php
│   │
│   ├── Repositories/          # Data access patterns
│   │   ├── QuoteRepository.php
│   │   └── CategoryRepository.php
│   │
│   ├── Jobs/                  # Queued jobs
│   │   ├── ExportQuotesPdf.php
│   │   ├── SendNotificationEmail.php
│   │   └── IndexQuotesForSearch.php
│   │
│   ├── Events/                # Event classes
│   │   ├── QuoteCreated.php
│   │   ├── QuoteUpdated.php
│   │   └── QuoteDeleted.php
│   │
│   ├── Listeners/             # Event listeners
│   │   ├── NotifyOnQuoteCreated.php
│   │   └── IndexOnQuoteCreated.php
│   │
│   ├── Traits/                # Reusable trait classes
│   │   ├── HasApiResponses.php
│   │   └── HasTimestamps.php
│   │
│   └── Providers/             # Service providers
│       ├── AppServiceProvider.php
│       ├── RouteServiceProvider.php
│       └── EventServiceProvider.php
│
├── bootstrap/
│   ├── app.php                # Bootstrap application
│   └── cache/
│
├── config/                    # Configuration files
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── cache.php
│   ├── queue.php
│   └── permission.php
│
├── database/
│   ├── migrations/            # Database migrations
│   │   ├── 2026_01_01_000000_create_users_table.php
│   │   ├── 2026_01_01_000001_create_quotes_table.php
│   │   ├── 2026_01_01_000002_create_categories_table.php
│   │   └── 2026_01_01_000003_create_roles_and_permissions_tables.php
│   │
│   ├── seeders/               # Database seeders
│   │   ├── DatabaseSeeder.php
│   │   ├── RoleSeeder.php
│   │   ├── PermissionSeeder.php
│   │   ├── UserSeeder.php
│   │   └── QuoteSeeder.php
│   │
│   └── factories/             # Model factories for testing
│       ├── UserFactory.php
│       ├── QuoteFactory.php
│       └── CategoryFactory.php
│
├── routes/
│   ├── api.php                # API routes
│   ├── web.php                # Web routes
│   └── console.php            # Console routes
│
├── storage/
│   ├── app/
│   │   ├── public/            # Public files
│   │   └── private/           # Private files
│   ├── logs/                  # Application logs
│   └── uploads/               # User uploads
│
├── tests/
│   ├── Feature/
│   │   ├── QuoteControllerTest.php
│   │   ├── CategoryControllerTest.php
│   │   ├── AuthControllerTest.php
│   │   └── PermissionTest.php
│   │
│   ├── Unit/
│   │   ├── Models/
│   │   │   ├── QuoteTest.php
│   │   │   └── UserTest.php
│   │   ├── Services/
│   │   │   ├── QuoteServiceTest.php
│   │   │   └── ExportServiceTest.php
│   │   └── Requests/
│   │       └── StoreQuoteRequestTest.php
│   │
│   ├── TestCase.php
│   ├── CreatesApplication.php
│   └── Traits/
│       └── WithTestDatabase.php
│
├── public/
│   ├── index.php              # Entry point
│   ├── .htaccess
│   └── web.config
│
├── resources/
│   ├── views/                 # Blade templates
│   ├── js/                    # JavaScript/Vue components
│   └── css/                   # Stylesheets
│
├── .env.example               # Environment template
├── .gitignore
├── composer.json              # PHP dependencies
├── package.json               # Node dependencies
├── phpunit.xml                # PHPUnit configuration
├── vite.config.js             # Vite build config
└── artisan                    # Laravel CLI
```

## Design Patterns

### 1. Repository Pattern

Used for data access abstraction:

```php
interface QuoteRepositoryInterface
{
    public function all($limit = 15);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}

class QuoteRepository implements QuoteRepositoryInterface
{
    public function all($limit = 15)
    {
        return Quote::paginate($limit);
    }
}
```

### 2. Service Layer Pattern

Encapsulates business logic:

```php
class QuoteService
{
    public function __construct(QuoteRepository $repository)
    {
        $this->repository = $repository;
    }

    public function publishQuote($id)
    {
        $quote = $this->repository->find($id);
        $quote->update(['is_published' => true]);
        event(new QuotePublished($quote));
        return $quote;
    }
}
```

### 3. Resource Pattern

API response transformation:

```php
class QuoteResource extends Resource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'author' => $this->author,
            'category' => new CategoryResource($this->category),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
```

### 4. Event-Driven Architecture

Decouples components:

```php
// Trigger event
event(new QuoteCreated($quote));

// Listen for event
class NotifyOnQuoteCreated implements ShouldQueue
{
    public function handle(QuoteCreated $event)
    {
        Notification::send($users, new QuoteCreatedNotification($event->quote));
    }
}
```

## Request Flow

### Typical Request Lifecycle

```
1. HTTP Request
   ↓
2. Route Matching (routes/api.php)
   ↓
3. Middleware Pipeline
   ├─ Authenticate (verify token)
   ├─ CheckPermission (verify role)
   └─ ThrottleRequests (rate limit)
   ↓
4. Controller Action
   ├─ Validate Request (Form Request)
   ├─ Call Service Layer
   └─ Return Response
   ↓
5. Service Layer
   ├─ Business Logic
   ├─ Model Queries
   ├─ Dispatch Events
   └─ Queue Jobs
   ↓
6. Model Interaction (Eloquent ORM)
   ├─ Query Database
   ├─ Apply Scopes
   └─ Eager Load Relations
   ↓
7. Database Query
   ├─ Execute SQL
   └─ Return Results
   ↓
8. Response Transformation
   ├─ Apply Resources
   └─ Format JSON
   ↓
9. HTTP Response
```

## Security Architecture

### 1. Authentication Flow

```
User Credentials
    ↓
Login Endpoint
    ↓
Verify Credentials
    ↓
Generate JWT Token
    ↓
Return Token
    ↓
Client Stores Token
    ↓
Include in Authorization Header
    ↓
Middleware Validates Token
    ↓
Authenticated Request
```

### 2. Authorization Flow

```
Authenticated User
    ↓
Check Route/Action
    ↓
Retrieve User Roles
    ↓
Check Role Permissions
    ↓
Grant/Deny Access
```

### 3. Security Features

- **Password Hashing**: BCrypt (12 rounds by default)
- **CORS**: Configurable cross-origin requests
- **Rate Limiting**: Throttle requests per user
- **HTTPS**: Required in production
- **CSRF Protection**: Token-based validation
- **Input Validation**: Form requests + database constraints
- **SQL Injection Prevention**: Parameterized queries via ORM
- **XSS Prevention**: Output escaping in responses

## Scalability Considerations

### 1. Caching Strategy

```
Data Request
    ↓
Check Cache (Redis)
├─ Hit: Return Cached Data
└─ Miss: Query Database
         ↓
         Cache Result (TTL: 60 min)
         ↓
         Return Data
```

### 2. Database Optimization

- **Indexing**: Strategic indexes on frequently queried columns
- **Query Optimization**: Eager loading with `with()` method
- **Pagination**: Prevent large dataset transfers
- **Full-Text Search**: MySQL FULLTEXT indexes

### 3. Queue System

Background job processing for expensive operations:

```
Long-Running Task
    ↓
Dispatch to Queue
    ↓
Return Immediate Response
    ↓
Worker Processes Job
    ↓
Job Completion
```

### 4. Load Balancing

Horizontal scaling setup:

```
Load Balancer
    ├─ Server 1 (Laravel App)
    ├─ Server 2 (Laravel App)
    └─ Server 3 (Laravel App)
         ↓
    Shared Database (MySQL)
    Shared Cache (Redis)
    Shared Queue (Redis)
    Shared Storage (S3/NFS)
```

### 5. Performance Monitoring

Key metrics to monitor:

- Response time (target < 200ms)
- Database query count (N+1 prevention)
- Cache hit ratio (target > 80%)
- Queue processing time
- Error rate
- CPU/Memory usage

---

**Last Updated**: June 13, 2026  
**Architecture Version**: 1.0  
**Status**: Production Ready
