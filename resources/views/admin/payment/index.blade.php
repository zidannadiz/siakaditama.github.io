@extends('layouts.app')

@section('title', 'Kelola Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Kelola Pembayaran</h1>
            <p class="text-gray-600 mt-1">Lihat dan kelola semua transaksi pembayaran</p>
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
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
            <p class="text-sm text-blue-700">Total Paid</p>
            <p class="text-lg font-bold text-blue-900 mt-1">Rp {{ number_format($stats['total_amount'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <p class="text-sm text-gray-700">Pending Amount</p>
            <p class="text-lg font-bold text-gray-900 mt-1">Rp {{ number_format($stats['pending_amount'], 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.payment.index') }}" id="filter-form" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <select name="payment_type" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
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
                <input type="text" name="search" placeholder="Cari invoice/VA/nama..." 
                       value="{{ request('search') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <input type="date" name="date_from" placeholder="Dari Tanggal" 
                       value="{{ request('date_from') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       onchange="this.form.submit()">
                <input type="date" name="date_to" placeholder="Sampai Tanggal" 
                       value="{{ request('date_to') }}"
                       class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                       onchange="this.form.submit()">
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Virtual Account</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $payment->invoice_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $payment->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $payment->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($payment->bank->logo && file_exists(public_path('storage/' . $payment->bank->logo)))
                                        <img src="{{ asset('storage/' . $payment->bank->logo) }}" 
                                             alt="{{ $payment->bank->name }}" 
                                             class="w-8 h-8 object-contain mr-2">
                                    @endif
                                    <span class="text-sm text-gray-900">{{ $payment->bank->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $payment->virtual_account }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($payment->total_amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($payment->status === 'paid') bg-green-100 text-green-800
                                    @elseif($payment->status === 'expired') bg-red-100 text-red-800
                                    @elseif($payment->status === 'cancelled') bg-gray-100 text-gray-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ $payment->status_badge['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $payment->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.payment.show', $payment) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    Detail
                                </a>
                                @if($payment->status === 'pending')
                                    <form action="{{ route('admin.payment.verify', $payment) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Verifikasi pembayaran ini?');">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900">
                                            Verifikasi
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                Tidak ada data pembayaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $payments->links() }}
    </div>
</div>
@endsection

