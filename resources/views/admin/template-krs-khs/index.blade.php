@extends('layouts.app')

@section('title', 'Template KRS/KHS')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
<div>
            <h1 class="text-3xl font-bold text-gray-900">Template KRS/KHS</h1>
            <p class="text-gray-600 mt-1">Kelola template Word untuk KRS dan KHS</p>
        </div>
        <a href="{{ route('admin.template-krs-khs.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Upload Template Baru
        </a>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('admin.template-krs-khs.index') }}" class="flex gap-4">
            <select name="jenis" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Jenis</option>
                <option value="krs" {{ request('jenis') === 'krs' ? 'selected' : '' }}>KRS</option>
                <option value="khs" {{ request('jenis') === 'khs' ? 'selected' : '' }}>KHS</option>
            </select>
            <select name="is_active" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Semua Status</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Non-Aktif</option>
            </select>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                Filter
            </button>
            <a href="{{ route('admin.template-krs-khs.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">
                Reset
            </a>
        </form>
    </div>

    <!-- Templates List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Template</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($templates as $template)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $template->jenis === 'krs' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ strtoupper($template->jenis) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $template->nama_template }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-500">{{ $template->deskripsi ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($template->is_active)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                Non-Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $template->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.template-krs-khs.edit', $template) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                            <a href="{{ route('admin.template-krs-khs.download', $template) }}" class="text-green-600 hover:text-green-900">Download</a>
                            <form action="{{ route('admin.template-krs-khs.toggle-status', $template) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-orange-600 hover:text-orange-900">
                                    {{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.template-krs-khs.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus template ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" style="cursor: pointer;">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada template. <a href="{{ route('admin.template-krs-khs.create') }}" class="text-blue-600 hover:underline">Upload template baru</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $templates->links() }}
    </div>
</div>
@endsection
