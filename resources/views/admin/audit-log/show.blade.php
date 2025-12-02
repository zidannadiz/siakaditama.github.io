@extends('layouts.app')

@section('title', 'Detail Audit Log')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Audit Log</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap tentang aktivitas</p>
        </div>
        <a href="{{ route('admin.audit-log.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium" style="cursor: pointer;">
            Kembali
        </a>
    </div>

    <!-- Log Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Informasi Aktivitas</h2>
        </div>

        <div class="p-6 space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu</label>
                    <p class="text-sm text-gray-900">{{ $auditLog->created_at->format('d F Y, H:i:s') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $auditLog->created_at->diffForHumans() }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User</label>
                    @if($auditLog->user)
                        <p class="text-sm text-gray-900">{{ $auditLog->user->name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $auditLog->user->email }}</p>
                    @else
                        <p class="text-sm text-gray-400">System</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aksi</label>
                    <span class="inline-block px-3 py-1 text-sm font-medium rounded-full 
                        @if($auditLog->action === 'create') bg-green-100 text-green-800
                        @elseif($auditLog->action === 'update') bg-blue-100 text-blue-800
                        @elseif($auditLog->action === 'delete') bg-red-100 text-red-800
                        @elseif($auditLog->action === 'login') bg-purple-100 text-purple-800
                        @elseif($auditLog->action === 'logout') bg-gray-100 text-gray-800
                        @elseif($auditLog->action === 'approve') bg-green-100 text-green-800
                        @elseif($auditLog->action === 'reject') bg-red-100 text-red-800
                        @elseif($auditLog->action === 'chat_send') bg-indigo-100 text-indigo-800
                        @elseif($auditLog->action === 'chat_read') bg-teal-100 text-teal-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $auditLog->action_name }}
                    </span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <p class="text-sm text-gray-900">{{ $auditLog->model_name }}</p>
                    @if($auditLog->model_id)
                        <p class="text-xs text-gray-500 mt-1">ID: {{ $auditLog->model_id }}</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">IP Address</label>
                    <p class="text-sm text-gray-900">{{ $auditLog->ip_address ?? '-' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Agent</label>
                    <p class="text-sm text-gray-900 break-all">{{ $auditLog->user_agent ?? '-' }}</p>
                </div>

                @if($auditLog->url)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                    <p class="text-sm text-gray-900 break-all">{{ $auditLog->url }}</p>
                </div>
                @endif

                @if($auditLog->description)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <p class="text-sm text-gray-900">{{ $auditLog->description }}</p>
                </div>
                @endif
            </div>

            <!-- Changes Comparison -->
            @if($auditLog->old_values || $auditLog->new_values)
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Perubahan Data</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($auditLog->old_values)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Lama</label>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <pre class="text-xs text-gray-800 overflow-x-auto">{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif

                        @if($auditLog->new_values)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Baru</label>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <pre class="text-xs text-gray-800 overflow-x-auto">{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

