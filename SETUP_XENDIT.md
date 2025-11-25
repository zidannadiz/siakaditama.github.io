# Setup Virtual Account dengan Xendit

## ✅ Yang Sudah Diinstall

1. ✅ Package `xendit/xendit-php` sudah terinstall
2. ✅ Config file `config/xendit.php` sudah dibuat
3. ✅ Migration untuk kolom Xendit sudah dibuat dan dijalankan
4. ✅ Service class `XenditService` sudah dibuat
5. ✅ Webhook controller sudah dibuat
6. ✅ Routes sudah ditambahkan
7. ✅ CSRF exception untuk webhook sudah dikonfigurasi

---

## 📋 Langkah Setup Xendit

### **Step 1: Daftar/Login ke Xendit**

1. Kunjungi: https://dashboard.xendit.co
2. Daftar atau login ke akun Xendit Anda
3. Pilih environment: **Development** (untuk testing) atau **Production** (untuk live)

### **Step 2: Ambil API Keys**

1. Login ke dashboard Xendit
2. Pergi ke: **Settings** → **API Keys**
3. Copy **Secret Key** dan **Public Key**
   - Secret Key: `xnd_development_...` atau `xnd_production_...`
   - Public Key: `xnd_public_development_...` atau `xnd_public_production_...`

### **Step 3: Setup Environment Variables**

Tambahkan di file `.env`:

```env
# Xendit Configuration
XENDIT_SECRET_KEY=xnd_development_YOUR_SECRET_KEY_HERE
XENDIT_PUBLIC_KEY=xnd_public_development_YOUR_PUBLIC_KEY_HERE
XENDIT_WEBHOOK_TOKEN=your_webhook_token_here
XENDIT_ENVIRONMENT=development
```

**Penting:**
- Ganti `YOUR_SECRET_KEY_HERE` dengan Secret Key dari Xendit
- Ganti `YOUR_PUBLIC_KEY_HERE` dengan Public Key dari Xendit
- Untuk webhook token, bisa generate random string atau ambil dari Xendit dashboard

### **Step 4: Setup Webhook di Xendit Dashboard**

1. Login ke dashboard Xendit
2. Pergi ke: **Settings** → **Webhooks**
3. Klik **Add Webhook**
4. Isi:
   - **Webhook Name**: `SIAKAD Payment Webhook`
   - **URL**: `https://yourdomain.com/payment/xendit/webhook`
     - Untuk local testing: `https://your-ngrok-url.ngrok.io/payment/xendit/webhook`
     - Untuk production: `https://siakad.yourdomain.com/payment/xendit/webhook`
   - **Events**: Pilih `Virtual Account Paid`
5. Save webhook

### **Step 5: Test Virtual Account**

1. Buat pembayaran baru di aplikasi
2. Pilih bank (misal: BCA)
3. Virtual Account akan dibuat via Xendit API
4. Nomor VA yang muncul adalah **Virtual Account beneran** dari Xendit
5. Lakukan pembayaran via mobile banking/app bank tersebut
6. Status akan otomatis update via webhook

---

## 🔄 Cara Kerja

### **Flow Pembayaran:**

1. **User membuat pembayaran:**
   - User memilih bank dan jumlah
   - System create payment record di database
   - System panggil Xendit API untuk create Virtual Account
   - Xendit return nomor VA yang beneran
   - Nomor VA disimpan di database

2. **User melakukan pembayaran:**
   - User transfer ke nomor VA via mobile banking
   - Bank process pembayaran
   - Xendit detect pembayaran

3. **Xendit mengirim webhook:**
   - Xendit POST ke `/payment/xendit/webhook`
   - WebhookController update status payment menjadi `paid`
   - System bisa kirim notifikasi ke user

---

## 🧪 Testing (Development Mode)

### **Menggunakan Ngrok untuk Local Testing:**

1. Install ngrok: https://ngrok.com/download
2. Jalankan ngrok:
   ```bash
   ngrok http 8000
   ```
3. Copy URL dari ngrok (contoh: `https://abc123.ngrok.io`)
4. Setup webhook URL di Xendit: `https://abc123.ngrok.io/payment/xendit/webhook`
5. Test pembayaran dari aplikasi

### **Test Payment dengan Xendit Test Account:**

Xendit menyediakan nomor Virtual Account untuk testing:
- BCA: Gunakan nomor VA yang diberikan Xendit
- Simulasi pembayaran bisa dilakukan via Xendit dashboard

---

## 📝 Environment Variables Reference

```env
# Xendit Configuration
XENDIT_SECRET_KEY=                    # Secret Key dari Xendit dashboard
XENDIT_PUBLIC_KEY=                    # Public Key dari Xendit dashboard  
XENDIT_WEBHOOK_TOKEN=                 # Token untuk verify webhook (opsional)
XENDIT_ENVIRONMENT=development        # development atau production
```

---

## 🔍 Troubleshooting

### **Error: "Xendit API key belum dikonfigurasi"**
**Solusi:** Pastikan `XENDIT_SECRET_KEY` sudah di-set di `.env` file

### **Error: "Invalid bank code"**
**Solusi:** Cek mapping bank code di `XenditService::mapBankCode()`. Pastikan bank yang dipilih support di Xendit.

### **Webhook tidak menerima callback**
**Solusi:** 
1. Pastikan webhook URL accessible dari internet (gunakan ngrok untuk local)
2. Cek logs di `storage/logs/laravel.log`
3. Verify webhook token di webhook controller

### **Status payment tidak auto-update**
**Solusi:**
1. Cek apakah webhook sudah di-setup di Xendit dashboard
2. Cek apakah webhook URL benar dan accessible
3. Cek logs untuk melihat apakah webhook diterima

---

## 📚 Dokumentasi Xendit

- Official Docs: https://docs.xendit.co/
- Virtual Account: https://docs.xendit.co/payments/virtual-accounts/
- Webhooks: https://docs.xendit.co/payments/webhooks/

---

## ✅ Checklist Setup

- [ ] Install package Xendit ✅
- [ ] Setup config file ✅
- [ ] Tambah environment variables di `.env`
- [ ] Setup webhook di Xendit dashboard
- [ ] Test create Virtual Account
- [ ] Test webhook callback
- [ ] Test pembayaran end-to-end

---

**Setelah setup selesai, Virtual Account akan menggunakan nomor VA yang beneran dari Xendit! 🎉**

