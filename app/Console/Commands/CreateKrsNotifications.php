<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KRS;
use App\Models\Notifikasi;
use App\Models\User;
use App\Services\NotifikasiService;

class CreateKrsNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'krs:create-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create notifications for pending KRS that don\'t have notifications yet';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for pending KRS without notifications...');

        $pendingKrs = KRS::where('status', 'pending')
            ->with(['mahasiswa', 'jadwalKuliah.mataKuliah'])
            ->get();

        if ($pendingKrs->isEmpty()) {
            $this->info('No pending KRS found.');
            return 0;
        }

        $adminUsers = User::where('role', 'admin')->get();
        
        if ($adminUsers->isEmpty()) {
            $this->error('No admin users found.');
            return 1;
        }

        $created = 0;
        foreach ($pendingKrs as $krs) {
            // Cek apakah sudah ada notifikasi untuk KRS ini
            $existingNotification = Notifikasi::where('user_id', $adminUsers->first()->id)
                ->where('judul', 'Pengajuan KRS Baru')
                ->where('pesan', 'like', "%{$krs->mahasiswa->nim}%")
                ->where('link', route('admin.krs.index'))
                ->first();

            if (!$existingNotification) {
                // Buat notifikasi untuk semua admin
                $mataKuliah = $krs->jadwalKuliah->mataKuliah;
                NotifikasiService::createForRole(
                    'admin',
                    'Pengajuan KRS Baru',
                    "Mahasiswa {$krs->mahasiswa->nama} ({$krs->mahasiswa->nim}) mengajukan KRS untuk mata kuliah {$mataKuliah->nama_mk}.",
                    'info',
                    route('admin.krs.index')
                );
                $created++;
            }
        }

        $this->info("Created {$created} notification(s) for pending KRS.");
        return 0;
    }
}
