@extends('layouts.app')

@section('title', 'Backup & Restore Database')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Backup & Restore Database</h1>
            <p class="text-gray-600 mt-1">Kelola backup database untuk keamanan data</p>
        </div>
        <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium" style="cursor: pointer;">
                Buat Backup Baru
            </button>
        </form>
    </div>

    <!-- Database Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="flex-1">
                <p class="text-sm text-blue-800">
                    <strong>Database:</strong> {{ ucfirst(config('database.default')) }} | 
                    <strong>Backup Directory:</strong> <code>storage/app/backups</code>
                </p>
                <p class="text-xs text-blue-600 mt-1">
                    Backup otomatis mencakup semua tabel dan data. Restore akan menggantikan seluruh database dengan data dari backup.
                </p>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Daftar Backup</h2>
            <p class="text-sm text-gray-600 mt-1">Pilih backup untuk restore atau download</p>
        </div>

        @if(empty($backups))
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                </svg>
                <p class="text-gray-500">Belum ada backup yang tersedia</p>
                <p class="text-sm text-gray-400 mt-1">Klik "Buat Backup Baru" untuk membuat backup pertama</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($backups as $backup)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <code class="text-sm text-gray-900">{{ $backup['filename'] }}</code>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $backup['size_human'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $backup['created_at']->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.backup.download', $backup['filename']) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors" style="cursor: pointer;">
                                            Download
                                        </a>
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('admin.backup.restore') }}" method="POST" class="inline" 
                                              onsubmit="return confirm('PERINGATAN: Restore akan menggantikan seluruh database dengan data dari backup ini. Pastikan Anda sudah membuat backup terbaru. Lanjutkan?');">
                                            @csrf
                                            <input type="hidden" name="backup_file" value="{{ $backup['filename'] }}">
                                            <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" style="cursor: pointer;">
                                                Restore
                                            </button>
                                        </form>
                                        <span class="text-gray-300">|</span>
                                        <form action="{{ route('admin.backup.destroy', $backup['filename']) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Yakin ingin menghapus backup ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" style="cursor: pointer;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Warning -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-yellow-800">Penting!</h3>
                <ul class="text-sm text-yellow-700 mt-1 list-disc list-inside space-y-1">
                    <li>Lakukan backup secara rutin untuk melindungi data akademik</li>
                    <li>Sebelum restore, pastikan sudah membuat backup terbaru</li>
                    <li>Restore akan menggantikan seluruh database dengan data dari backup</li>
                    <li>Download backup dan simpan di tempat yang aman sebagai cadangan eksternal</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

