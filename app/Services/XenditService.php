<?php

namespace App\Services;

use Xendit\Xendit;
use Xendit\VirtualAccounts;
use App\Models\Payment;
use App\Models\Bank;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private $secretKey;

    public function __construct()
    {
        $this->secretKey = config('xendit.secret_key');
        if ($this->secretKey) {
            Xendit::setApiKey($this->secretKey);
        }
    }

    /**
     * Mapping bank code ke Xendit bank code
     */
    private function mapBankCode($bankCode)
    {
        $mapping = [
            'BCA' => 'BCA',
            'BRI' => 'BRI',
            'BNI' => 'BNI',
            'MANDIRI' => 'MANDIRI',
            'CIMB' => 'CIMB',
            'DANAMON' => 'DANAMON',
            'PERMATA' => 'PERMATA',
            'BSI' => 'BSI',
            'OCBC' => 'OCBC',
            'MAYBANK' => 'MAYBANK',
        ];

        return $mapping[$bankCode] ?? 'BCA'; // Default BCA jika tidak ditemukan
    }

    /**
     * Create Virtual Account
     */
    public function createVirtualAccount(Payment $payment)
    {
        try {
            // Jika API key belum diset, return error
            if (!$this->secretKey) {
                return [
                    'success' => false,
                    'error' => 'Xendit API key belum dikonfigurasi',
                ];
            }

            $bank = $payment->bank;
            $xenditBankCode = $this->mapBankCode($bank->code);

            // Format expired_at sesuai Xendit (ISO 8601)
            $expirationDate = $payment->expired_at->format('c');

            $params = [
                'external_id' => $payment->invoice_number,
                'bank_code' => $xenditBankCode,
                'name' => $payment->user->name,
                'expected_amount' => (float) $payment->total_amount,
                'expiration_date' => $expirationDate,
                'is_closed' => true,
                'is_single_use' => true,
            ];

            // Buat VA via Xendit API
            $va = VirtualAccounts::create($params);

            // Update payment dengan data dari Xendit
            $payment->update([
                'xendit_id' => $va['id'],
                'external_id' => $va['external_id'],
                'virtual_account' => $va['account_number'], // Nomor VA yang beneran dari Xendit
                'xendit_response' => json_encode($va),
            ]);

            Log::info('Xendit VA Created', [
                'payment_id' => $payment->id,
                'xendit_id' => $va['id'],
                'va_number' => $va['account_number'],
            ]);

            return [
                'success' => true,
                'virtual_account' => $va['account_number'],
                'data' => $va,
            ];

        } catch (\Xendit\Exceptions\ApiException $e) {
            Log::error('Xendit API Error', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ]);

            return [
                'success' => false,
                'error' => 'Gagal membuat Virtual Account: ' . $e->getMessage(),
            ];
        } catch (\Exception $e) {
            Log::error('Xendit VA Creation Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get Virtual Account Status
     */
    public function getVirtualAccountStatus($xenditId)
    {
        try {
            if (!$this->secretKey) {
                return [
                    'success' => false,
                    'error' => 'Xendit API key belum dikonfigurasi',
                ];
            }

            $va = VirtualAccounts::retrieve($xenditId);

            return [
                'success' => true,
                'data' => $va,
            ];
        } catch (\Exception $e) {
            Log::error('Xendit Get VA Status Error', [
                'xendit_id' => $xenditId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

