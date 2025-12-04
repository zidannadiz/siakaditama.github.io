@extends('layouts.app')

@section('title', 'Input Nilai')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Input Nilai</h1>
        <p class="text-gray-600 mt-1">{{ $jadwal->mataKuliah->nama_mk }} - {{ $jadwal->semester->nama_semester }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('dosen.nilai.store', $jadwal->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIM</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tugas (30%)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UTS (30%)</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UAS (40%)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($krs_list as $index => $krs)
                            @php
                                $nilai = $krs->nilai ?? null;
                            @endphp
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nim }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $krs->mahasiswa->nama }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="hidden" name="krs_id[]" value="{{ $krs->id }}">
                                    <input type="number" 
                                           name="nilai_tugas[]" 
                                           value="{{ old('nilai_tugas.'.$index, $nilai?->nilai_tugas ?? '') }}"
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           name="nilai_uts[]" 
                                           value="{{ old('nilai_uts.'.$index, $nilai?->nilai_uts ?? '') }}"
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="number" 
                                           name="nilai_uas[]" 
                                           value="{{ old('nilai_uas.'.$index, $nilai?->nilai_uas ?? '') }}"
                                           min="0" 
                                           max="100" 
                                           step="0.01"
                                           class="w-20 px-2 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('dosen.nilai.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    Batal
                </a>
                <button type="submit" style="cursor: pointer;" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium cursor-pointer">
                    Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

