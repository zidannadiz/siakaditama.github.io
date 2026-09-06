@extends('layouts.app')

@section('title', 'Detail Kurikulum')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Detail Kurikulum</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap kurikulum dan daftar mata kuliah</p>
        </div>
        <div class="flex space-x-3">
            @if(in_array(auth()->user()->role, ['admin', 'admin_pt', 'admin_prodi']))
            <a href="{{ route('admin.kurikulum.edit', $kurikulum) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">
                Edit Kurikulum
            </a>
            @endif
            <a href="{{ route('admin.kurikulum.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium border border-gray-200">
                Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Info Card -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-3 mb-4">Informasi Umum</h3>
                
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama Kurikulum</dt>
                        <dd class="mt-1 text-base text-gray-900 font-medium">{{ $kurikulum->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Program Studi</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $kurikulum->prodi->nama_prodi }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Tahun</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $kurikulum->tahun }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $kurikulum->status === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($kurikulum->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($kurikulum->keterangan)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $kurikulum->keterangan }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            @if(in_array(auth()->user()->role, ['admin', 'admin_pt', 'admin_prodi']))
            <!-- Form Tambah Detail -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 border-b pb-3 mb-4">Tambah Mata Kuliah</h3>
                <form action="{{ route('admin.kurikulum.detail.add', $kurikulum) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="mata_kuliah_id" class="block text-sm font-medium text-gray-700">Mata Kuliah</label>
                            <select name="mata_kuliah_id" id="mata_kuliah_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="">Pilih Mata Kuliah</option>
                                @foreach($mataKuliahs as $mk)
                                    <option value="{{ $mk->id }}">{{ $mk->kode_mk }} - {{ $mk->nama_mk }} ({{ $mk->sks }} SKS)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="semester_ke" class="block text-sm font-medium text-gray-700">Semester Ke-</label>
                            <input type="number" name="semester_ke" id="semester_ke" min="1" max="14" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label for="jenis" class="block text-sm font-medium text-gray-700">Jenis</label>
                            <select name="jenis" id="jenis" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="wajib">Wajib</option>
                                <option value="pilihan">Pilihan</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                            Tambahkan
                        </button>
                    </div>
                </form>
            </div>
            @endif
        </div>
        
        <!-- Mata Kuliah Terkait -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Daftar Mata Kuliah dalam Kurikulum</h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester Ke</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kode</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKS</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($kurikulum->details->sortBy('semester_ke') as $detail)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                    {{ $detail->semester_ke }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $detail->mataKuliah->kode_mk }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->mataKuliah->nama_mk }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $detail->mataKuliah->sks }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $detail->jenis === 'wajib' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($detail->jenis) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if(in_array(auth()->user()->role, ['admin', 'admin_pt', 'admin_prodi']))
                                    <form action="{{ route('admin.kurikulum.detail.remove', [$kurikulum, $detail]) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus mata kuliah dari kurikulum?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                    </form>
                                    @else
                                    <span class="text-gray-400 italic">Read-only</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Belum ada mata kuliah dalam kurikulum ini.
                                </td>
                            </tr>
                            @endforelse
                            @if($kurikulum->details->count() > 0)
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                    Total SKS
                                </td>
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $kurikulum->details->sum(function($d) { return $d->mataKuliah->sks; }) }}
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
