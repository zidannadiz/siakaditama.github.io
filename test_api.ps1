# PowerShell Script untuk test API endpoints
# Usage: .\test_api.ps1

$BASE_URL = "http://localhost:8000/api"

Write-Host "=== Testing SIAKAD API ===" -ForegroundColor Green
Write-Host ""

# Test Login - Admin
Write-Host "1. Testing Login (Admin)..." -ForegroundColor Yellow
$loginBody = @{
    email = "admin@test.com"
    password = "password"
} | ConvertTo-Json

try {
    $loginResponse = Invoke-RestMethod -Uri "$BASE_URL/login" `
        -Method POST `
        -ContentType "application/json" `
        -Body $loginBody
    
    Write-Host "✅ Login successful!" -ForegroundColor Green
    $TOKEN = $loginResponse.data.token
    Write-Host "Token: $($TOKEN.Substring(0, [Math]::Min(20, $TOKEN.Length)))..." -ForegroundColor Cyan
    Write-Host ""
    
    # Test Get User
    Write-Host "2. Testing Get User..." -ForegroundColor Yellow
    $headers = @{
        "Authorization" = "Bearer $TOKEN"
        "Content-Type" = "application/json"
    }
    $userResponse = Invoke-RestMethod -Uri "$BASE_URL/user" `
        -Method GET `
        -Headers $headers
    Write-Host "✅ Get User successful!" -ForegroundColor Green
    Write-Host "User: $($userResponse.data.name) ($($userResponse.data.role))" -ForegroundColor Cyan
    Write-Host ""
    
    # Test Dashboard
    Write-Host "3. Testing Dashboard..." -ForegroundColor Yellow
    $dashboardResponse = Invoke-RestMethod -Uri "$BASE_URL/dashboard" `
        -Method GET `
        -Headers $headers
    Write-Host "✅ Dashboard successful!" -ForegroundColor Green
    Write-Host "Role: $($dashboardResponse.data.role)" -ForegroundColor Cyan
    Write-Host ""
    
    # Test Notifikasi
    Write-Host "4. Testing Notifikasi..." -ForegroundColor Yellow
    $notifResponse = Invoke-RestMethod -Uri "$BASE_URL/notifikasi" `
        -Method GET `
        -Headers $headers
    Write-Host "✅ Notifikasi successful!" -ForegroundColor Green
    Write-Host "Total: $($notifResponse.data.pagination.total)" -ForegroundColor Cyan
    Write-Host ""
    
    # Test Profile
    Write-Host "5. Testing Profile..." -ForegroundColor Yellow
    $profileResponse = Invoke-RestMethod -Uri "$BASE_URL/profile" `
        -Method GET `
        -Headers $headers
    Write-Host "✅ Profile successful!" -ForegroundColor Green
    Write-Host "Name: $($profileResponse.data.name)" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "=== All Tests Passed! ===" -ForegroundColor Green
    
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Please check:" -ForegroundColor Yellow
    Write-Host "1. Server is running (php artisan serve)" -ForegroundColor Yellow
    Write-Host "2. Test users exist (run: php create_test_users.php)" -ForegroundColor Yellow
}

