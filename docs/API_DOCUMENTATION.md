# API Documentation

Complete reference for all REST API endpoints in the Quotes Application.

## 📋 Table of Contents

- [Base URL](#base-url)
- [Authentication](#authentication)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [Rate Limiting](#rate-limiting)
- [Endpoints](#endpoints)
  - [Authentication](#authentication-endpoints)
  - [Quotes](#quotes-endpoints)
  - [Categories](#categories-endpoints)
  - [Users](#users-endpoints)
  - [Roles & Permissions](#roles--permissions-endpoints)
- [Examples](#examples)

## Base URL

```
http://localhost:8000/api
```

For production, replace `localhost:8000` with your domain.

## Authentication

### Token-Based Authentication

The API uses Bearer token authentication. Include the token in the `Authorization` header:

```
Authorization: Bearer YOUR_API_TOKEN
```

### Getting a Token

1. Register a new account: `POST /auth/register`
2. Login: `POST /auth/login`
3. Use the returned `token` in subsequent requests

### Token Expiration

Tokens expire after 24 hours by default (configurable in `.env` as `API_TOKEN_EXPIRATION`).

### Refreshing Tokens

```bash
POST /auth/refresh
Authorization: Bearer YOUR_API_TOKEN
```

## Response Format

### Success Response

All successful responses follow this format:

```json
{
  "success": true,
  "message": "Operation completed successfully",
  "data": {
    // Response data
  },
  "meta": {
    "timestamp": "2026-06-13T10:30:00Z",
    "version": "1.0"
  }
}
```

### Paginated Response

```json
{
  "success": true,
  "data": [
    // Array of items
  ],
  "meta": {
    "pagination": {
      "total": 100,
      "count": 15,
      "per_page": 15,
      "current_page": 1,
      "total_pages": 7,
      "links": {
        "next": "http://localhost:8000/api/quotes?page=2",
        "prev": null
      }
    }
  }
}
```

## Error Handling

### Error Response

All error responses include a status code and error details:

```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field_name": ["Error message"]
  },
  "status_code": 400
}
```

### HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Request successful |
| 201 | Created | Resource created successfully |
| 204 | No Content | Successful deletion or update |
| 400 | Bad Request | Invalid parameters or validation error |
| 401 | Unauthorized | Missing or invalid authentication |
| 403 | Forbidden | Authenticated but lacking permissions |
| 404 | Not Found | Resource not found |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

## Rate Limiting

API endpoints are rate-limited to prevent abuse:

- **Authenticated Users**: 60 requests per minute
- **Public Endpoints**: 30 requests per minute

Rate limit information is included in response headers:

```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1623595800
```

## Endpoints

---

## Authentication Endpoints

### Register User

Create a new user account.

```http
POST /auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "secure_password_123",
  "password_confirmation": "secure_password_123"
}
```

**Response** (201):
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
  }
}
```

**Validation Rules**:
- `name`: Required, string, max 255 characters
- `email`: Required, email, unique
- `password`: Required, min 8 characters, confirmed

---

### Login

Authenticate and receive an API token.

```http
POST /auth/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "secure_password_123"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
  }
}
```

---

### Logout

Invalidate the current token.

```http
POST /auth/logout
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

### Refresh Token

Get a new token using the current one.

```http
POST /auth/refresh
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "Bearer"
  }
}
```

---

### Password Reset Request

Request a password reset email.

```http
POST /auth/forgot-password
Content-Type: application/json

{
  "email": "john@example.com"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Password reset email sent"
}
```

---

### Reset Password

Reset password using token from email.

```http
POST /auth/reset-password
Content-Type: application/json

{
  "email": "john@example.com",
  "token": "reset_token_from_email",
  "password": "new_password_123",
  "password_confirmation": "new_password_123"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Password reset successfully"
}
```

---

## Quotes Endpoints

### List Quotes

Retrieve a paginated list of quotes.

```http
GET /quotes?page=1&per_page=15&sort=created_at&order=desc&search=inspiration&category_id=1
Authorization: Bearer YOUR_API_TOKEN
```

**Query Parameters**:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 15, max: 100)
- `sort`: Sort field (`created_at`, `updated_at`, `title`)
- `order`: Sort order (`asc`, `desc`)
- `search`: Search in title and content
- `category_id`: Filter by category
- `author_id`: Filter by author

**Response** (200):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Keep going",
      "content": "The only way to do great work is to love what you do.",
      "author": "Steve Jobs",
      "category": {
        "id": 1,
        "name": "Motivation"
      },
      "views": 1523,
      "likes": 342,
      "created_at": "2026-06-13T10:00:00Z",
      "updated_at": "2026-06-13T10:00:00Z"
    }
  ],
  "meta": {
    "pagination": {
      "total": 150,
      "count": 15,
      "per_page": 15,
      "current_page": 1,
      "total_pages": 10
    }
  }
}
```

---

### Get Single Quote

Retrieve a specific quote by ID.

```http
GET /quotes/{id}
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Keep going",
    "content": "The only way to do great work is to love what you do.",
    "author": "Steve Jobs",
    "category": {
      "id": 1,
      "name": "Motivation"
    },
    "tags": ["motivation", "work", "success"],
    "views": 1523,
    "likes": 342,
    "created_by": {
      "id": 5,
      "name": "Admin User"
    },
    "created_at": "2026-06-13T10:00:00Z",
    "updated_at": "2026-06-13T10:00:00Z"
  }
}
```

---

### Create Quote

Create a new quote (requires `create_quotes` permission).

```http
POST /quotes
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "title": "Dream Big",
  "content": "Don't let anyone tell you that you can't do it.",
  "author": "Walt Disney",
  "category_id": 1,
  "tags": ["dreams", "motivation"],
  "is_published": true
}
```

**Request Fields**:
- `title`: Required, string, max 255
- `content`: Required, string
- `author`: Required, string, max 255
- `category_id`: Required, integer, exists in categories
- `tags`: Optional, array of strings
- `is_published`: Optional, boolean (default: false)

**Response** (201):
```json
{
  "success": true,
  "message": "Quote created successfully",
  "data": {
    "id": 2,
    "title": "Dream Big",
    "content": "Don't let anyone tell you that you can't do it.",
    "author": "Walt Disney",
    "category_id": 1,
    "is_published": true,
    "created_at": "2026-06-13T11:00:00Z"
  }
}
```

---

### Update Quote

Update an existing quote (requires `edit_quotes` permission).

```http
PUT /quotes/{id}
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "title": "Dream Big - Updated",
  "content": "Don't let anyone tell you that you can't achieve your dreams.",
  "author": "Walt Disney",
  "category_id": 1,
  "is_published": true
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Quote updated successfully",
  "data": {
    "id": 2,
    "title": "Dream Big - Updated",
    "content": "Don't let anyone tell you that you can't achieve your dreams.",
    "updated_at": "2026-06-13T11:30:00Z"
  }
}
```

---

### Delete Quote

Delete a quote (requires `delete_quotes` permission).

```http
DELETE /quotes/{id}
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (204):
No content returned

---

### Like Quote

Add a like to a quote.

```http
POST /quotes/{id}/like
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "message": "Quote liked",
  "data": {
    "id": 1,
    "likes": 343
  }
}
```

---

### Unlike Quote

Remove a like from a quote.

```http
POST /quotes/{id}/unlike
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "message": "Quote unliked",
  "data": {
    "id": 1,
    "likes": 342
  }
}
```

---

### Export Quote to PDF

Export a quote as PDF.

```http
GET /quotes/{id}/export/pdf
Authorization: Bearer YOUR_API_TOKEN
```

**Response**: PDF file download

---

## Categories Endpoints

### List Categories

Retrieve all quote categories.

```http
GET /categories?page=1&per_page=50&search=motivation
Authorization: Bearer YOUR_API_TOKEN
```

**Query Parameters**:
- `page`: Page number (default: 1)
- `per_page`: Items per page (default: 50)
- `search`: Search in category name
- `sort`: Sort field (default: `name`)

**Response** (200):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Motivation",
      "description": "Inspirational and motivational quotes",
      "quote_count": 45,
      "created_at": "2026-06-13T08:00:00Z"
    },
    {
      "id": 2,
      "name": "Success",
      "description": "Quotes about success and achievement",
      "quote_count": 32,
      "created_at": "2026-06-13T08:00:00Z"
    }
  ],
  "meta": {
    "pagination": {
      "total": 15,
      "count": 15,
      "per_page": 50,
      "current_page": 1,
      "total_pages": 1
    }
  }
}
```

---

### Get Single Category

Retrieve a specific category with its quotes.

```http
GET /categories/{id}
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Motivation",
    "description": "Inspirational and motivational quotes",
    "quote_count": 45,
    "quotes": [
      {
        "id": 1,
        "title": "Keep going",
        "author": "Steve Jobs",
        "content": "The only way to do great work..."
      }
    ],
    "created_at": "2026-06-13T08:00:00Z"
  }
}
```

---

### Create Category

Create a new category (requires `create_categories` permission).

```http
POST /categories
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "name": "Leadership",
  "description": "Quotes about effective leadership"
}
```

**Response** (201):
```json
{
  "success": true,
  "message": "Category created successfully",
  "data": {
    "id": 16,
    "name": "Leadership",
    "description": "Quotes about effective leadership",
    "created_at": "2026-06-13T12:00:00Z"
  }
}
```

---

### Update Category

Update a category (requires `edit_categories` permission).

```http
PUT /categories/{id}
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "name": "Leadership & Management",
  "description": "Updated description"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Category updated successfully",
  "data": {
    "id": 16,
    "name": "Leadership & Management",
    "description": "Updated description"
  }
}
```

---

### Delete Category

Delete a category (requires `delete_categories` permission).

```http
DELETE /categories/{id}
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (204):
No content returned

---

## Users Endpoints

### Get Current User

Get authenticated user profile.

```http
GET /users/me
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "roles": ["editor"],
    "permissions": ["create_quotes", "edit_quotes", "delete_quotes"],
    "created_at": "2026-06-13T08:00:00Z"
  }
}
```

---

### Update Profile

Update current user profile.

```http
PUT /users/me
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "name": "John Updated",
  "email": "john.updated@example.com"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "id": 1,
    "name": "John Updated",
    "email": "john.updated@example.com"
  }
}
```

---

### Change Password

Change current user password.

```http
POST /users/me/change-password
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "current_password": "old_password_123",
  "password": "new_password_123",
  "password_confirmation": "new_password_123"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Password changed successfully"
}
```

---

### List Users

Get all users (admin only).

```http
GET /users?page=1&per_page=20&search=john&role=editor
Authorization: Bearer YOUR_API_TOKEN
```

**Query Parameters**:
- `page`: Page number
- `per_page`: Items per page
- `search`: Search in name and email
- `role`: Filter by role
- `sort`: Sort field

**Response** (200):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "roles": ["editor"],
      "created_at": "2026-06-13T08:00:00Z"
    }
  ],
  "meta": {
    "pagination": {
      "total": 50,
      "count": 20,
      "per_page": 20,
      "current_page": 1,
      "total_pages": 3
    }
  }
}
```

---

### Get User

Get specific user details (admin only).

```http
GET /users/{id}
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "roles": ["editor"],
    "permissions": ["create_quotes", "edit_quotes"],
    "created_at": "2026-06-13T08:00:00Z"
  }
}
```

---

### Assign Role

Assign a role to user (admin only).

```http
POST /users/{id}/roles
Authorization: Bearer YOUR_API_TOKEN
Content-Type: application/json

{
  "role": "admin"
}
```

**Response** (200):
```json
{
  "success": true,
  "message": "Role assigned successfully",
  "data": {
    "id": 1,
    "roles": ["admin"]
  }
}
```

---

### Revoke Role

Remove a role from user (admin only).

```http
DELETE /users/{id}/roles/{role}
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "message": "Role revoked successfully"
}
```

---

## Roles & Permissions Endpoints

### List Roles

Get all available roles.

```http
GET /roles
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "admin",
      "display_name": "Administrator",
      "description": "Full system access",
      "permissions_count": 12
    },
    {
      "id": 2,
      "name": "editor",
      "display_name": "Editor",
      "description": "Can manage quotes and categories",
      "permissions_count": 6
    },
    {
      "id": 3,
      "name": "viewer",
      "display_name": "Viewer",
      "description": "Can only view quotes",
      "permissions_count": 1
    }
  ]
}
```

---

### List Permissions

Get all available permissions.

```http
GET /permissions
Authorization: Bearer YOUR_API_TOKEN
```

**Response** (200):
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "view_quotes",
      "display_name": "View Quotes"
    },
    {
      "id": 2,
      "name": "create_quotes",
      "display_name": "Create Quotes"
    },
    {
      "id": 3,
      "name": "edit_quotes",
      "display_name": "Edit Quotes"
    },
    {
      "id": 4,
      "name": "delete_quotes",
      "display_name": "Delete Quotes"
    }
  ]
}
```

---

## Examples

### Example 1: Complete Workflow

```bash
# 1. Register a new user
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "SecurePass123",
    "password_confirmation": "SecurePass123"
  }'

# Response includes token: eyJ0eXAiOiJKV1QiLCJhbGc...

# 2. Use token to create a quote
curl -X POST http://localhost:8000/api/quotes \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "New Quote",
    "content": "This is an inspiring quote",
    "author": "Jane Doe",
    "category_id": 1,
    "is_published": true
  }'

# 3. List all quotes
curl -X GET "http://localhost:8000/api/quotes?page=1&per_page=15" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."

# 4. Get specific quote
curl -X GET http://localhost:8000/api/quotes/1 \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."

# 5. Update quote
curl -X PUT http://localhost:8000/api/quotes/1 \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..." \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Updated Quote",
    "content": "Updated content"
  }'

# 6. Like the quote
curl -X POST http://localhost:8000/api/quotes/1/like \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."

# 7. Logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc..."
```

### Example 2: Error Handling

```bash
# Missing required field
curl -X POST http://localhost:8000/api/quotes \
  -H "Authorization: Bearer token" \
  -H "Content-Type: application/json" \
  -d '{"title": "Quote without content"}'

# Response (422):
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "content": ["The content field is required."],
    "author": ["The author field is required."],
    "category_id": ["The category id field is required."]
  },
  "status_code": 422
}
```

### Example 3: Pagination

```bash
# Request page 2 with 10 items per page
curl -X GET "http://localhost:8000/api/quotes?page=2&per_page=10" \
  -H "Authorization: Bearer token"

# Response includes:
{
  "meta": {
    "pagination": {
      "total": 150,
      "count": 10,
      "per_page": 10,
      "current_page": 2,
      "total_pages": 15,
      "links": {
        "first": "http://localhost:8000/api/quotes?page=1",
        "last": "http://localhost:8000/api/quotes?page=15",
        "prev": "http://localhost:8000/api/quotes?page=1",
        "next": "http://localhost:8000/api/quotes?page=3"
      }
    }
  }
}
```

---

**API Version**: 1.0  
**Last Updated**: June 13, 2026  
**Status**: Production Ready
