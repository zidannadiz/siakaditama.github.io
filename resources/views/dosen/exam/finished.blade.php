@extends('layouts.app')

@section('title', 'Ujian yang Sudah Selesai')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Ujian yang Sudah Selesai</h1>
            <p class="text-gray-600 mt-1">Daftar ujian yang sudah berakhir</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('dosen.exam.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                Kembali ke Daftar Ujian
            </a>
            <a href="{{ route('dosen.exam.ongoing') }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                Ujian Berlangsung
            </a>
        </div>
    </div>

    @if($exams->isEmpty())
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
        <p class="text-blue-800">Tidak ada ujian yang sudah selesai.</p>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Judul Ujian
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mata Kuliah
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Waktu Selesai
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Peserta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Selesai
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($exams as $exam)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $exam->judul }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $exam->jadwalKuliah->mataKuliah->nama_mk ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $exam->jadwalKuliah->semester->nama_semester ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $exam->selesai->format('d M Y, H:i') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $exam->selesai->diffForHumans() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $exam->total_sessions }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $exam->completed_sessions }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('dosen.exam.show', $exam) }}" 
                               class="text-blue-600 hover:text-blue-900 cursor-pointer mr-3">
                                Detail
                            </a>
                            <a href="{{ route('dosen.exam.results', $exam) }}" 
                               class="text-green-600 hover:text-green-900 cursor-pointer">
                                Hasil
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

