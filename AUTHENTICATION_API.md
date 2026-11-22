# Authentication API Documentation

Comprehensive RESTful Authentication API built with **Laravel 12** and **Laravel Sanctum**.

---

## 📌 Table of Contents
- [Overview](#overview)
- [Base URL & Standard Headers](#base-url--standard-headers)
- [Standard Response Formats](#standard-response-formats)
- [Endpoints Summary](#endpoints-summary)
- [Detailed Endpoint Specifications](#detailed-endpoint-specifications)
  - [1. Register User](#1-register-user)
  - [2. Login](#2-login)
  - [3. Get Current User (Me)](#3-get-current-user-me)
  - [4. Update Profile](#4-update-profile)
  - [5. Change Password](#5-change-password)
  - [6. Logout (Current Device)](#6-logout-current-device)
  - [7. Logout (All Devices)](#7-logout-all-devices)
  - [8. Forgot Password](#8-forgot-password)
  - [9. Reset Password](#9-reset-password)
- [Testing Workflow (cURL Examples)](#testing-workflow-curl-examples)

---

## 🌐 Base URL & Standard Headers

- **Base URL:** `https://favored-lightpink-curlew-28.hostingersite.com/api` (or your configured `APP_URL/api`)

### Common Request Headers
| Header | Value | Description |
|---|---|---|
| `Accept` | `application/json` | Enforces JSON responses from all endpoints |
| `Content-Type` | `application/json` | Required for requests with a JSON body |
| `Authorization` | `Bearer <your_token>` | Required for all protected routes |

---

## 📦 Standard Response Formats

### 1. Success Response (200 / 201)
```json
{
  "success": true,
  "message": "Action completed successfully",
  "data": { ... }
}
```

### 2. Validation Error Response (422)
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "email": [
      "The email field must be a valid email address."
    ]
  }
}
```

### 3. Authentication Error Response (401)
```json
{
  "success": false,
  "message": "Invalid email or password"
}
```

---

## 📋 Endpoints Summary

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/api/auth/register` | No | Register a new user and receive an access token |
| `POST` | `/api/auth/login` | No | Authenticate user and issue an access token |
| `GET` | `/api/auth/me` | Bearer Token | Retrieve currently authenticated user profile |
| `PUT` | `/api/auth/profile` | Bearer Token | Update user profile name and/or email |
| `PUT` | `/api/auth/change-password` | Bearer Token | Change current user password |
| `POST` | `/api/auth/logout` | Bearer Token | Revoke current access token (logout current session) |
| `POST` | `/api/auth/logout-all` | Bearer Token | Revoke all active tokens (logout from all devices) |
| `POST` | `/api/auth/forgot-password` | No | Send password reset email link |
| `POST` | `/api/auth/reset-password` | No | Reset user password using token |

---

## 🛠 Detailed Endpoint Specifications

### 1. Register User

Registers a new account in the system, hashes the password securely, and returns the created user alongside an initial Bearer API token.

- **Method:** `POST`
- **URL:** `/api/auth/register`
- **Authentication:** None

#### Request Body
| Field | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | Full name of the user (max: 255) |
| `email` | string | Yes | Valid unique email address |
| `password` | string | Yes | Minimum 8 characters |
| `password_confirmation` | string | Yes | Must match `password` |
| `device_name` | string | No | Optional client/device name (defaults to `auth_token`) |

#### Example Request
```bash
curl -X POST "http://localhost:8000/api/auth/register" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "Password123!",
    "password_confirmation": "Password123!",
    "device_name": "chrome_desktop"
  }'
```

#### Example Response (`201 Created`)
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "email_verified_at": null,
      "created_at": "2026-11-22T19:00:00.000000Z",
      "updated_at": "2026-11-22T19:00:00.000000Z"
    },
    "token": "1|raC6y5Y9PjN7VvEa1t4gD7nK...",
    "token_type": "Bearer"
  }
}
```

---

### 2. Login

Authenticates credentials and generates a new Sanctum API token for the user session.

- **Method:** `POST`
- **URL:** `/api/auth/login`
- **Authentication:** None

#### Request Body
| Field | Type | Required | Description |
|---|---|---|---|
| `email` | string | Yes | Registered email address |
| `password` | string | Yes | User account password |
| `device_name` | string | No | Optional client/device name (defaults to `auth_token`) |

#### Example Request
```bash
curl -X POST "http://localhost:8000/api/auth/login" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jane@example.com",
    "password": "Password123!",
    "device_name": "mobile_app"
  }'
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "email_verified_at": null,
      "created_at": "2026-11-22T19:00:00.000000Z",
      "updated_at": "2026-11-22T19:00:00.000000Z"
    },
    "token": "2|r9K3f84hF0kLm3qG1t4z...",
    "token_type": "Bearer"
  }
}
```

#### Error Response (`401 Unauthorized`)
```json
{
  "success": false,
  "message": "Invalid email or password"
}
```

---

### 3. Get Current User (Me)

Fetches the profile details of the currently authenticated user based on the provided Bearer token.

- **Method:** `GET`
- **URL:** `/api/auth/me`
- **Authentication:** `Bearer <token>`

#### Example Request
```bash
curl -X GET "http://localhost:8000/api/auth/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 2|r9K3f84hF0kLm3qG1t4z..."
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "User profile retrieved successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Jane Doe",
      "email": "jane@example.com",
      "email_verified_at": null,
      "created_at": "2026-11-22T19:00:00.000000Z",
      "updated_at": "2026-11-22T19:00:00.000000Z"
    }
  }
}
```

---

### 4. Update Profile

Allows an authenticated user to update their name and email address. Ensures email uniqueness while ignoring the user's current record.

- **Method:** `PUT`
- **URL:** `/api/auth/profile`
- **Authentication:** `Bearer <token>`

#### Request Body
| Field | Type | Required | Description |
|---|---|---|---|
| `name` | string | Yes | Updated full name |
| `email` | string | Yes | Updated unique email address |

#### Example Request
```bash
curl -X PUT "http://localhost:8000/api/auth/profile" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 2|r9K3f84hF0kLm3qG1t4z..." \
  -d '{
    "name": "Jane Smith",
    "email": "jane.smith@example.com"
  }'
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "Profile updated successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "Jane Smith",
      "email": "jane.smith@example.com",
      "email_verified_at": null,
      "created_at": "2026-11-22T19:00:00.000000Z",
      "updated_at": "2026-11-22T19:10:00.000000Z"
    }
  }
}
```

---

### 5. Change Password

Allows an authenticated user to change their password by validating their current password and providing a new confirmed password.

- **Method:** `PUT`
- **URL:** `/api/auth/change-password`
- **Authentication:** `Bearer <token>`

#### Request Body
| Field | Type | Required | Description |
|---|---|---|---|
| `current_password` | string | Yes | Must match current account password |
| `password` | string | Yes | New password (min 8 characters, must be different from current) |
| `password_confirmation` | string | Yes | Must match new `password` |

#### Example Request
```bash
curl -X PUT "http://localhost:8000/api/auth/change-password" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer 2|r9K3f84hF0kLm3qG1t4z..." \
  -d '{
    "current_password": "Password123!",
    "password": "NewSecretPass456!",
    "password_confirmation": "NewSecretPass456!"
  }'
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "Password updated successfully"
}
```

---

### 6. Logout (Current Device)

Revokes only the access token currently being used by the client. Other devices or sessions remain active.

- **Method:** `POST`
- **URL:** `/api/auth/logout`
- **Authentication:** `Bearer <token>`

#### Example Request
```bash
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 2|r9K3f84hF0kLm3qG1t4z..."
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

---

### 7. Logout (All Devices)

Revokes all active API tokens for the authenticated user, terminating sessions across all devices and applications.

- **Method:** `POST`
- **URL:** `/api/auth/logout-all`
- **Authentication:** `Bearer <token>`

#### Example Request
```bash
curl -X POST "http://localhost:8000/api/auth/logout-all" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 2|r9K3f84hF0kLm3qG1t4z..."
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "Successfully logged out from all devices"
}
```

---

### 8. Forgot Password

Initiates the password reset process by generating a password reset token and dispatching the reset email link.

- **Method:** `POST`
- **URL:** `/api/auth/forgot-password`
- **Authentication:** None

#### Request Body
| Field | Type | Required | Description |
|---|---|---|---|
| `email` | string | Yes | Email address of the account to reset |

#### Example Request
```bash
curl -X POST "http://localhost:8000/api/auth/forgot-password" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "jane.smith@example.com"
  }'
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "We have emailed your password reset link."
}
```

---

### 9. Reset Password

Completes the password reset process by validating the token and email, then setting the user's new password.

- **Method:** `POST`
- **URL:** `/api/auth/reset-password`
- **Authentication:** None

#### Request Body
| Field | Type | Required | Description |
|---|---|---|---|
| `token` | string | Yes | Reset token received in the email/URL |
| `email` | string | Yes | Email address matching the reset token |
| `password` | string | Yes | New password (min 8 chars) |
| `password_confirmation` | string | Yes | Must match `password` |

#### Example Request
```bash
curl -X POST "http://localhost:8000/api/auth/reset-password" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "token": "4a7f052565651c6e1e8db45214690bc4123...",
    "email": "jane.smith@example.com",
    "password": "BrandNewPassword789!",
    "password_confirmation": "BrandNewPassword789!"
  }'
```

#### Example Response (`200 OK`)
```json
{
  "success": true,
  "message": "Your password has been reset."
}
```

---

## 🚀 Testing Workflow (cURL Examples)

Execute this step-by-step sequence in any terminal:

```bash
# 1. Register a new user
TOKEN=$(curl -s -X POST "http://localhost:8000/api/auth/register" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name":"Tester","email":"test@example.com","password":"Password123!","password_confirmation":"Password123!"}' \
  | grep -o '"token":"[^"]*' | cut -d'"' -f4)

# 2. Get current profile using the token
curl -X GET "http://localhost:8000/api/auth/me" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"

# 3. Update profile
curl -X PUT "http://localhost:8000/api/auth/profile" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"name":"Tester Updated","email":"test@example.com"}'

# 4. Change password
curl -X PUT "http://localhost:8000/api/auth/change-password" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{"current_password":"Password123!","password":"NewPassword123!","password_confirmation":"NewPassword123!"}'

# 5. Logout
curl -X POST "http://localhost:8000/api/auth/logout" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN"
```
