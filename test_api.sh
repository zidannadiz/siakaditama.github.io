#!/bin/bash

# Script untuk test API endpoints
# Usage: bash test_api.sh

BASE_URL="http://localhost:8000/api"

echo "=== Testing SIAKAD API ===\n"

# Test Login - Admin
echo "1. Testing Login (Admin)..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"password"}')

echo "$LOGIN_RESPONSE" | jq '.'

# Extract token
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.data.token // empty')

if [ -z "$TOKEN" ]; then
    echo "❌ Login failed! Please check if test users exist."
    exit 1
fi

echo "\n✅ Login successful! Token: ${TOKEN:0:20}...\n"

# Test Get User
echo "2. Testing Get User..."
curl -s -X GET "$BASE_URL/user" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | jq '.'

# Test Dashboard
echo "\n3. Testing Dashboard..."
curl -s -X GET "$BASE_URL/dashboard" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | jq '.'

# Test Notifikasi
echo "\n4. Testing Notifikasi..."
curl -s -X GET "$BASE_URL/notifikasi" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | jq '.'

# Test Profile
echo "\n5. Testing Profile..."
curl -s -X GET "$BASE_URL/profile" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | jq '.'

echo "\n=== Testing Complete ==="

