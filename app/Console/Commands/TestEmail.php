<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use App\Mail\GeneralNotificationMail;
use App\Models\User;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?} {--check : Hanya cek konfigurasi tanpa mengirim email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration dan kirim test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Test Konfigurasi Email ===');
        $this->newLine();

        // Check configuration
        $this->checkConfiguration();

        if ($this->option('check')) {
            return 0;
        }

        // Ask for email address if not provided
        $email = $this->argument('email');
        if (!$email) {
            $email = $this->ask('Masukkan email tujuan untuk test email');
        }

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email tidak valid!');
            return 1;
        }

        $this->newLine();
        $this->info('Mengirim test email ke: ' . $email);
        $this->newLine();

        try {
            // Create a dummy user for the email
            $dummyUser = new User();
            $dummyUser->name = 'Test User';
            $dummyUser->email = $email;

            // Send test email
            Mail::to($email)->send(new GeneralNotificationMail(
                $dummyUser,
                'Test Email dari SIAKAD',
                'Halo!',
                'Ini adalah email test dari sistem SIAKAD. Jika Anda menerima email ini, berarti konfigurasi email sudah benar.',
                'Buka Dashboard',
                route('admin.dashboard')
            ));

            $mailer = Config::get('mail.default');
            $this->info('✓ Email berhasil dikirim!');
            $this->newLine();

            if ($mailer === 'log') {
                $this->warn('⚠ Email dikirim ke log (mode development)');
                $this->info('  Cek file: storage/logs/laravel.log');
            } elseif ($mailer === 'smtp') {
                $this->info('✓ Email dikirim melalui SMTP');
                $this->warn('⚠ Pastikan queue worker berjalan jika menggunakan queue');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('✗ Gagal mengirim email!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Tips:');
            $this->line('  1. Cek konfigurasi email di .env file');
            $this->line('  2. Pastikan MAIL_MAILER, MAIL_HOST, MAIL_PORT sudah benar');
            $this->line('  3. Untuk Gmail, pastikan menggunakan App Password');
            $this->line('  4. Cek log di: storage/logs/laravel.log');
            return 1;
        }
    }

    /**
     * Check email configuration
     */
    protected function checkConfiguration()
    {
        $this->info('📋 Konfigurasi Email Saat Ini:');
        $this->newLine();

        $mailer = Config::get('mail.default');
        $fromAddress = Config::get('mail.from.address');
        $fromName = Config::get('mail.from.name');

        $this->line("  Mailer: <fg=cyan>{$mailer}</>");
        $this->line("  From Address: <fg=cyan>{$fromAddress}</>");
        $this->line("  From Name: <fg=cyan>{$fromName}</>");

        if ($mailer === 'smtp') {
            $host = env('MAIL_HOST');
            $port = env('MAIL_PORT');
            $username = env('MAIL_USERNAME');
            $encryption = env('MAIL_ENCRYPTION');

            $this->newLine();
            $this->info('📡 Konfigurasi SMTP:');
            $this->line("  Host: <fg=cyan>" . ($host ?: 'Tidak di-set') . "</>");
            $this->line("  Port: <fg=cyan>" . ($port ?: 'Tidak di-set') . "</>");
            $this->line("  Username: <fg=cyan>" . ($username ?: 'Tidak di-set') . "</>");
            $this->line("  Password: <fg=cyan>" . (env('MAIL_PASSWORD') ? '*** (tersembunyi)' : 'Tidak di-set') . "</>");
            $this->line("  Encryption: <fg=cyan>" . ($encryption ?: 'Tidak di-set') . "</>");

            // Validation
            $this->newLine();
            $errors = [];
            $warnings = [];

            if (!$host) {
                $errors[] = 'MAIL_HOST tidak di-set';
            }
            if (!$port) {
                $errors[] = 'MAIL_PORT tidak di-set';
            }
            if (!$username) {
                $errors[] = 'MAIL_USERNAME tidak di-set';
            }
            if (!env('MAIL_PASSWORD')) {
                $errors[] = 'MAIL_PASSWORD tidak di-set';
            }
            if (!$encryption) {
                $warnings[] = 'MAIL_ENCRYPTION tidak di-set (disarankan: tls atau ssl)';
            }
            if ($port == 587 && $encryption != 'tls') {
                $warnings[] = 'Port 587 biasanya menggunakan encryption TLS';
            }
            if ($port == 465 && $encryption != 'ssl') {
                $warnings[] = 'Port 465 biasanya menggunakan encryption SSL';
            }

            if (count($errors) > 0) {
                $this->newLine();
                $this->error('✗ Error Konfigurasi:');
                foreach ($errors as $error) {
                    $this->line("  - {$error}");
                }
            }

            if (count($warnings) > 0) {
                $this->newLine();
                $this->warn('⚠ Peringatan:');
                foreach ($warnings as $warning) {
                    $this->line("  - {$warning}");
                }
            }

            if (count($errors) == 0 && count($warnings) == 0) {
                $this->newLine();
                $this->info('✓ Konfigurasi SMTP terlihat baik!');
            }
        } elseif ($mailer === 'log') {
            $this->newLine();
            $this->warn('⚠ Mode Development: Email akan disimpan di log');
            $this->info('  File log: storage/logs/laravel.log');
        }

        // Check queue configuration
        $this->newLine();
        $queueConnection = Config::get('queue.default');
        $this->info('📦 Konfigurasi Queue:');
        $this->line("  Connection: <fg=cyan>{$queueConnection}</>");

        if ($queueConnection === 'database') {
            $this->info('  ✓ Queue menggunakan database (disarankan untuk email)');
            $this->warn('  ⚠ Pastikan queue worker berjalan: php artisan queue:work');
        } elseif ($queueConnection === 'sync') {
            $this->warn('  ⚠ Queue menggunakan sync (email akan dikirim langsung, tidak async)');
        }
    }
}
