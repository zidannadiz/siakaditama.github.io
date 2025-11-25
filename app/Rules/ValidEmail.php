<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Log;

class ValidEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Validasi format email dasar
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $fail('Email tidak valid. Pastikan email yang digunakan adalah email asli dan dapat diverifikasi.');
            return;
        }

        // Validasi format email menggunakan regex yang lebih ketat
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if (!preg_match($pattern, $value)) {
            $fail('Email tidak valid. Pastikan email yang digunakan adalah email asli dan dapat diverifikasi.');
            return;
        }

        // Validasi domain email (tidak boleh domain temporary/invalid)
        $disallowedDomains = [
            'tempmail.com',
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            'throwaway.email',
            'temp-mail.org',
            'yopmail.com',
            'getnada.com',
            'mohmal.com',
            'fakeinbox.com',
            'trashmail.com',
            'mintemail.com',
            'sharklasers.com',
            'guerrillamailblock.com',
            'dispostable.com',
            'maildrop.cc',
            'throwawaymail.com',
            'emailondeck.com',
            'fakemail.net',
            'example.com',
            'test.com',
            'invalid.com',
            'localhost.com',
            'fake.com',
        ];

        $domain = substr(strrchr($value, "@"), 1);
        $domain = strtolower(trim($domain));

        // Cek apakah domain dalam daftar yang tidak diizinkan
        foreach ($disallowedDomains as $disallowed) {
            if ($domain === $disallowed || strpos($domain, $disallowed) !== false) {
                $fail('Email tidak valid. Email temporary/tidak valid tidak diizinkan.');
                return;
            }
        }

        // Validasi panjang email
        if (strlen($value) > 255) {
            $fail('Email terlalu panjang. Maksimal 255 karakter.');
            return;
        }

        // Validasi bahwa email memiliki format yang benar (tidak ada spasi, karakter aneh di awal/akhir)
        if (trim($value) !== $value) {
            $fail('Email tidak valid. Email tidak boleh mengandung spasi di awal atau akhir.');
            return;
        }

        // Verifikasi domain email dengan DNS MX record check
        $verificationResult = $this->verifyEmailDomain($domain);
        
        // Log untuk debugging - HAPUS INI SETELAH TESTING
        Log::info("Email validation attempt", [
            'email' => $value,
            'domain' => $domain,
            'is_valid' => $verificationResult
        ]);
        
        if (!$verificationResult) {
            $fail('Email tidak valid. Domain email tidak ditemukan atau tidak memiliki server email (MX record). Pastikan email yang digunakan adalah email asli dan dapat diverifikasi.');
            return;
        }
    }

    /**
     * Verify email domain dengan mengecek MX record dan A record
     */
    private function verifyEmailDomain(string $domain): bool
    {
        // Skip verification untuk localhost atau IP address (untuk development)
        if ($domain === 'localhost' || filter_var($domain, FILTER_VALIDATE_IP)) {
            Log::warning("Email validation: localhost or IP address rejected", ['domain' => $domain]);
            return false;
        }

        // Domain umum yang pasti valid (whitelist) - HANYA domain yang benar-benar umum
        $commonDomains = [
            'gmail.com', 
            'yahoo.com', 
            'hotmail.com', 
            'outlook.com', 
            'icloud.com', 
            'protonmail.com',
            'yahoo.co.id',
            'gmail.co.id',
            'live.com',
            'msn.com',
            'aol.com',
            'zoho.com',
            'mail.com',
        ];
        
        if (in_array($domain, $commonDomains)) {
            return true;
        }

        try {
            // Cek MX record (Mail Exchange record) - ini yang paling penting
            $mxRecords = [];
            $hasMx = @getmxrr($domain, $mxRecords);
            
            // Log untuk debugging - HAPUS INI SETELAH TESTING
            Log::info("Email domain verification", [
                'domain' => $domain,
                'has_mx' => $hasMx,
                'mx_records_count' => is_array($mxRecords) ? count($mxRecords) : 0,
                'mx_records' => $mxRecords ?? []
            ]);
            
            // Jika ada MX record, domain valid
            if ($hasMx && !empty($mxRecords) && is_array($mxRecords) && count($mxRecords) > 0) {
                // Double check: pastikan MX record bukan empty string
                $validMxRecords = array_filter($mxRecords, function($record) {
                    return !empty($record) && is_string($record) && strlen(trim($record)) > 0;
                });
                
                if (count($validMxRecords) > 0) {
                    // Triple check: pastikan MX record bukan localhost atau IP private
                    foreach ($validMxRecords as $record) {
                        if (strpos($record, 'localhost') !== false || 
                            strpos($record, '127.0.0.1') !== false ||
                            strpos($record, '192.168.') !== false ||
                            strpos($record, '10.') !== false) {
                            Log::warning("Email validation: Invalid MX record (localhost/private IP)", [
                                'domain' => $domain,
                                'mx_record' => $record
                            ]);
                            return false;
                        }
                    }
                    return true;
                }
            }
            
            // Untuk domain lain yang tidak dalam whitelist, HARUS ada MX record
            // Jika tidak ada MX record, reject
            Log::warning("Email validation: No valid MX record found", ['domain' => $domain]);
            return false;
            
        } catch (\Exception $e) {
            // Jika terjadi error (misalnya network issue), reject untuk keamanan
            Log::warning("Email domain verification failed for {$domain}: " . $e->getMessage());
            return false;
        }
    }
}
