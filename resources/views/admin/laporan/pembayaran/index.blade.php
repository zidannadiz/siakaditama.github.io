@extends('layouts.app')

@section('title', 'Laporan Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Laporan Pembayaran</h1>
            <p class="text-gray-600 mt-1">Laporan dan statistik pembayaran mahasiswa</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.pembayaran.export-excel', request()->all()) }}" 
               class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
               style="background-color: #16A34A !important;">
                Export Excel
            </a>
            <a href="{{ route('admin.laporan.pembayaran.export-pdf', request()->all()) }}" 
               class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-colors font-medium shadow-md" 
               style="background-color: #DC2626 !important;">
                Export PDF
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Pembayaran</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
            <p class="text-sm text-yellow-700">Pending</p>
            <p class="text-2xl font-bold text-yellow-900 mt-1">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-700">Sudah Dibayar</p>
            <p class="text-2xl font-bold text-green-900 mt-1">{{ number_format($stats['paid']) }}</p>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm text-red-700">Expired</p>
            <p class="text-2xl font-bold text-red-900 mt-1">{{ number_format($stats['expired']) }}</p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <p class="text-sm text-gray-700">Dibatalkan</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['cancelled']) }}</p>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-sm text-blue-700">Total Paid</p>
            <p class="text-lg font-bold text-blue-900 mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.laporan.pembayaran.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                
                <select name="payment_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="SPP" {{ request('payment_type') === 'SPP' ? 'selected' : '' }}>SPP</option>
                    <option value="UKT" {{ request('payment_type') === 'UKT' ? 'selected' : '' }}>UKT</option>
                    <option value="BIAYA_PENDAFTARAN" {{ request('payment_type') === 'BIAYA_PENDAFTARAN' ? 'selected' : '' }}>Biaya Pendaftaran</option>
                </select>
                
                <select name="bank_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Bank</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->name }}</option>
                    @endforeach
                </select>
                
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                       placeholder="Dari Tanggal">
                
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                       placeholder="Sampai Tanggal">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <select name="mahasiswa_id" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Mahasiswa</option>
                    @foreach($mahasiswas as $mahasiswa)
                        <option value="{{ $mahasiswa['id'] }}" {{ request('mahasiswa_id') == $mahasiswa['id'] ? 'selected' : '' }}>
                            {{ $mahasiswa['name'] }} ({{ $mahasiswa['mahasiswa']->nim ?? '-' }})
                        </option>
                    @endforeach
                </select>
                
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                       placeholder="Cari invoice, VA, atau nama...">
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.laporan.pembayaran.index') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mahasiswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->invoice_number }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->virtual_account }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->user->name ?? '-' }}</div>
                                <div class="text-xs text-gray-500">{{ $payment->user->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->bank->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp {{ number_format($payment->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $badge = $payment->status_badge;
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $badge['color'] }}-100 text-{{ $badge['color'] }}-800">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->created_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                Tidak ada data pembayaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection

