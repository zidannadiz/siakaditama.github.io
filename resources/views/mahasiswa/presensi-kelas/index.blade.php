@extends('layouts.app')

@section('title', 'Presensi Kelas')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Presensi Kelas</h1>
        <p class="text-gray-600 mt-1">Masukkan kode kelas untuk bergabung</p>
    </div>

    <!-- Form Join Kelas -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Gabung Kelas</h2>
        <form id="joinForm" class="space-y-4">
            @csrf
            <div>
                <label for="kode_kelas" class="block text-sm font-medium text-gray-700 mb-2">Kode Kelas</label>
                <input type="text" id="kode_kelas" name="kode_kelas" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-2xl font-mono tracking-widest uppercase"
                       placeholder="XXXXXX" maxlength="6">
                <p class="text-sm text-gray-500 mt-2">Masukkan 6 digit kode kelas yang diberikan dosen</p>
            </div>
            <button type="submit" id="joinBtn" 
                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Gabung Kelas
            </button>
        </form>

        <div id="alertContainer" class="mt-4"></div>
    </div>

    <!-- Kelas Aktif -->
    @if($activeClasses->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Kelas Aktif yang Bisa Anda Ikuti</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($activeClasses as $class)
                    <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                        <h3 class="font-semibold text-gray-900">{{ $class->jadwalKuliah->mataKuliah->nama_mk ?? 'N/A' }}</h3>
                        <p class="text-sm text-gray-600 mt-1">Pertemuan {{ $class->pertemuan }} • {{ $class->tanggal->format('d/m/Y') }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $class->dosen->nama ?? 'N/A' }}</p>
                        <button onclick="fillCode('{{ $class->kode_kelas }}')" 
                                class="mt-3 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                            Gabung dengan Kode: {{ $class->kode_kelas }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
document.getElementById('joinForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const kodeKelas = document.getElementById('kode_kelas').value.toUpperCase();
    const alertContainer = document.getElementById('alertContainer');
    const joinBtn = document.getElementById('joinBtn');
    
    joinBtn.disabled = true;
    joinBtn.textContent = 'Memproses...';
    alertContainer.innerHTML = '';

    try {
        const joinResponse = await fetch('{{ route("mahasiswa.presensi-kelas.join") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ kode_kelas: kodeKelas })
        });
        
        const result = await joinResponse.json();
        
        if (result.success) {
            alertContainer.innerHTML = `
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                    ${result.message}
                </div>
            `;
            setTimeout(() => {
                window.location.href = '{{ route("mahasiswa.presensi-kelas.history") }}';
            }, 2000);
        } else {
            alertContainer.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    ${result.message}
                </div>
            `;
        }
    } catch (error) {
        console.error(error);
        alertContainer.innerHTML = `
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                Terjadi kesalahan. Silakan coba lagi.
            </div>
        `;
    } finally {
        joinBtn.disabled = false;
        joinBtn.textContent = 'Gabung Kelas';
    }
});

function fillCode(kode) {
    document.getElementById('kode_kelas').value = kode;
    document.getElementById('joinForm').dispatchEvent(new Event('submit'));
}

// Auto uppercase
document.getElementById('kode_kelas').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>
@endsection

