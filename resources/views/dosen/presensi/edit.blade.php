@extends('layouts.app')

@section('title', 'Edit Presensi')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Presensi</h1>
        <p class="text-gray-600 mt-1">{{ $jadwal->mataKuliah->nama_mk }} - Pertemuan {{ $pertemuan }}</p>
    </div>

    <form action="{{ route('dosen.presensi.update', [$jadwal->id, $pertemuan]) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="pertemuan" class="block text-sm font-medium text-gray-700 mb-2">Pertemuan Ke-</label>
                <input type="number" name="pertemuan" id="pertemuan" value="{{ $pertemuan }}" min="1" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                <p class="mt-1 text-xs text-gray-500">Pertemuan tidak dapat diubah</p>
            </div>

            <div>
                <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" value="{{ old('tanggal', $tanggal->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('tanggal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($krs_list as $index => $krs)
                        @php
                            $presensi = $presensis->get($krs->mahasiswa_id);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nim }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nama }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="hidden" name="presensi[{{ $index }}][mahasiswa_id]" value="{{ $krs->mahasiswa_id }}">
                                <select name="presensi[{{ $index }}][status]" required class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="hadir" {{ old("presensi.$index.status", $presensi->status ?? 'hadir') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="izin" {{ old("presensi.$index.status", $presensi->status ?? '') === 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ old("presensi.$index.status", $presensi->status ?? '') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="alpa" {{ old("presensi.$index.status", $presensi->status ?? '') === 'alpa' ? 'selected' : '' }}>Alpa</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" name="presensi[{{ $index }}][catatan]" value="{{ old("presensi.$index.catatan", $presensi->catatan ?? '') }}" placeholder="Opsional" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('dosen.presensi.index', ['jadwal_id' => $jadwal->id, 'pertemuan' => $pertemuan]) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Update Presensi
            </button>
        </div>
    </form>
</div>
@endsection

