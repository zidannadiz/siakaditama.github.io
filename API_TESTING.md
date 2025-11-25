# Panduan Testing API SIAKAD

## Quick Start

### 1. Pastikan Server Running
```bash
php artisan serve
```

### 2. Test Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

Response:
```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin",
            "email": "admin@example.com",
            "role": "admin"
        },
        "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    }
}
```

### 3. Simpan Token
Copy token dari response, gunakan untuk request selanjutnya.

### 4. Test Dashboard
```bash
curl -X GET http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json"
```

## Test Cases

### Authentication

#### ✅ Test Login Success
```bash
POST /api/login
Body: {"email": "user@example.com", "password": "password"}
Expected: 200, success: true, token returned
```

#### ❌ Test Login Failed
```bash
POST /api/login
Body: {"email": "wrong@example.com", "password": "wrong"}
Expected: 422, validation error
```

#### ✅ Test Logout
```bash
POST /api/logout
Headers: Authorization: Bearer {token}
Expected: 200, success: true
```

### Mahasiswa Endpoints

#### ✅ Get KRS List
```bash
GET /api/mahasiswa/krs
Headers: Authorization: Bearer {token}
Expected: 200, list of KRS
```

#### ✅ Add KRS
```bash
POST /api/mahasiswa/krs
Headers: Authorization: Bearer {token}
Body: {"jadwal_kuliah_id": 1}
Expected: 201, KRS created
```

#### ✅ Get KHS
```bash
GET /api/mahasiswa/khs
Headers: Authorization: Bearer {token}
Expected: 200, list of semesters
```

### Dosen Endpoints

#### ✅ Get Nilai List
```bash
GET /api/dosen/nilai?jadwal_id=1
Headers: Authorization: Bearer {token}
Expected: 200, list of nilai
```

#### ✅ Input Nilai
```bash
POST /api/dosen/nilai/1
Headers: Authorization: Bearer {token}
Body: {
  "krs_id": [1, 2],
  "nilai_tugas": [85, 90],
  "nilai_uts": [80, 85],
  "nilai_uas": [90, 95]
}
Expected: 201, nilai created
```

### Notifikasi

#### ✅ Get Notifikasi
```bash
GET /api/notifikasi
Headers: Authorization: Bearer {token}
Expected: 200, list of notifications
```

#### ✅ Get Unread Count
```bash
GET /api/notifikasi/unread-count
Headers: Authorization: Bearer {token}
Expected: 200, count: number
```

## Postman Collection

Import collection berikut ke Postman:

```json
{
  "info": {
    "name": "SIAKAD API",
    "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
  },
  "item": [
    {
      "name": "Auth",
      "item": [
        {
          "name": "Login",
          "request": {
            "method": "POST",
            "header": [{"key": "Content-Type", "value": "application/json"}],
            "body": {
              "mode": "raw",
              "raw": "{\n  \"email\": \"admin@example.com\",\n  \"password\": \"password\"\n}"
            },
            "url": {
              "raw": "{{base_url}}/login",
              "host": ["{{base_url}}"],
              "path": ["login"]
            }
          }
        },
        {
          "name": "Get User",
          "request": {
            "method": "GET",
            "header": [
              {"key": "Authorization", "value": "Bearer {{token}}"}
            ],
            "url": {
              "raw": "{{base_url}}/user",
              "host": ["{{base_url}}"],
              "path": ["user"]
            }
          }
        }
      ]
    },
    {
      "name": "Dashboard",
      "request": {
        "method": "GET",
        "header": [
          {"key": "Authorization", "value": "Bearer {{token}}"}
        ],
        "url": {
          "raw": "{{base_url}}/dashboard",
          "host": ["{{base_url}}"],
          "path": ["dashboard"]
        }
      }
    }
  ],
  "variable": [
    {
      "key": "base_url",
      "value": "http://localhost:8000/api"
    },
    {
      "key": "token",
      "value": ""
    }
  ]
}
```

## Automated Testing

### PHPUnit Test Example

```php
<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_success()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'data' => [
                    'user',
                    'token',
                ],
            ]);
    }

    public function test_login_failed()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong',
        ]);

        $response->assertStatus(422);
    }

    public function test_dashboard_requires_auth()
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(401);
    }

    public function test_dashboard_with_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }
}
```

## Common Issues

### Issue: 401 Unauthorized
**Solution:** 
- Pastikan token valid
- Cek format header: `Authorization: Bearer {token}`
- Token mungkin expired, login ulang

### Issue: CORS Error
**Solution:**
- Pastikan CORS middleware aktif
- Untuk development, tambahkan domain di `.env`

### Issue: 500 Internal Server Error
**Solution:**
- Cek `storage/logs/laravel.log`
- Pastikan database sudah migrate
- Cek konfigurasi `.env`

## Performance Testing

### Load Test dengan Apache Bench
```bash
# Test login endpoint
ab -n 100 -c 10 -p login.json -T application/json \
  http://localhost:8000/api/login
```

### Monitor Response Time
Gunakan tools seperti:
- Postman (built-in)
- Apache Bench
- JMeter
- K6

## Security Testing

### Test Cases:
1. ✅ Token validation
2. ✅ Role-based access
3. ✅ SQL injection prevention
4. ✅ XSS prevention
5. ✅ CSRF protection (untuk web)

## Next Steps

1. Setup automated testing
2. Setup CI/CD untuk API testing
3. Setup API monitoring
4. Setup rate limiting
5. Setup API versioning (jika diperlukan)

