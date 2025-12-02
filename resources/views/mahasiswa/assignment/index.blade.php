@extends('layouts.app')

@section('title', 'Tugas')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Tugas</h1>
        <p class="text-gray-600 mt-1">Daftar tugas dari mata kuliah yang Anda ambil</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="space-y-4">
            @forelse($assignments as $assignment)
                <div class="p-5 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $assignment->judul }}</h3>
                                @if($assignment->isExpired())
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-red-100 text-red-800">Expired</span>
                                @elseif($assignment->deadline->diffInDays(now()) <= 1)
                                    <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800">Deadline Soon</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1">{{ $assignment->jadwalKuliah->mataKuliah->nama_mk }}</p>
                            @if($assignment->deskripsi)
                                <p class="text-sm text-gray-600 mt-2">{{ Str::limit($assignment->deskripsi, 150) }}</p>
                            @endif
                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                <span>Deadline: {{ $assignment->deadline->format('d M Y, H:i') }}</span>
                                <span>Bobot: {{ $assignment->bobot }}%</span>
                                @if($assignment->submission)
                                    @if($assignment->submission->nilai !== null)
                                        <span class="font-semibold text-green-600">Nilai: {{ number_format($assignment->submission->nilai, 2) }}</span>
                                    @else
                                        <span class="text-green-600">✓ Sudah Submit</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">Belum Submit</span>
                                @endif
                            </div>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('mahasiswa.assignment.show', $assignment) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                {{ $assignment->submission ? 'Lihat' : 'Kerjakan' }}
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-4 text-gray-500">Belum ada tugas</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

