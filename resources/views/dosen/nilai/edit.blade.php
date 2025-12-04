@extends('layouts.app')

@section('title', 'Edit Nilai')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Nilai</h1>
        <p class="text-gray-600 mt-1">{{ $nilai->jadwalKuliah->mataKuliah->nama_mk }} - {{ $nilai->mahasiswa->nama }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('dosen.nilai.update', $nilai) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nilai_tugas" class="block text-sm font-medium text-gray-700 mb-2">Nilai Tugas (30%)</label>
                    <input type="number" id="nilai_tugas" name="nilai_tugas" 
                           value="{{ old('nilai_tugas', $nilai->nilai_tugas) }}"
                           min="0" max="100" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nilai_tugas') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nilai_uts" class="block text-sm font-medium text-gray-700 mb-2">Nilai UTS (30%)</label>
                    <input type="number" id="nilai_uts" name="nilai_uts" 
                           value="{{ old('nilai_uts', $nilai->nilai_uts) }}"
                           min="0" max="100" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nilai_uts') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="nilai_uas" class="block text-sm font-medium text-gray-700 mb-2">Nilai UAS (40%)</label>
                    <input type="number" id="nilai_uas" name="nilai_uas" 
                           value="{{ old('nilai_uas', $nilai->nilai_uas) }}"
                           min="0" max="100" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('nilai_uas') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nilai Akhir</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 font-semibold" id="nilai_akhir_display">
                        {{ $nilai->nilai_akhir ? number_format($nilai->nilai_akhir, 2) : '-' }}
                    </div>
                    <p class="mt-1 text-xs text-gray-500">(30% Tugas + 30% UTS + 40% UAS)</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Huruf Mutu</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 font-semibold" id="huruf_mutu_display">
                        {{ $nilai->huruf_mutu ?? '-' }}
                    </div>
                    <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-xs font-semibold text-blue-900 mb-1">Skala Penilaian:</p>
                        <p class="text-xs text-blue-800">A: ≥85 (4.00) | A-: 80-84 (3.75) | B+: 75-79 (3.50)</p>
                        <p class="text-xs text-blue-800">B: 70-74 (3.00) | B-: 65-69 (2.75) | C+: 60-64 (2.50)</p>
                        <p class="text-xs text-blue-800">C: 55-59 (2.00) | C-: 50-54 (1.75) | D: 40-49 (1.00) | E: &lt;40 (0.00)</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bobot</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 font-semibold" id="bobot_display">
                        {{ $nilai->bobot ? number_format($nilai->bobot, 2) : '-' }}
                    </div>
                </div>
            </div>

            <div>
                <label for="catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                <textarea id="catatan" name="catatan" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('catatan', $nilai->catatan) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('dosen.nilai.index', ['jadwal_id' => $nilai->jadwal_kuliah_id]) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors cursor-pointer">
                    Batal
                </a>
                <button type="submit" style="cursor: pointer;" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nilaiTugas = document.getElementById('nilai_tugas');
    const nilaiUts = document.getElementById('nilai_uts');
    const nilaiUas = document.getElementById('nilai_uas');
    const nilaiAkhirDisplay = document.getElementById('nilai_akhir_display');
    const hurufMutuDisplay = document.getElementById('huruf_mutu_display');
    const bobotDisplay = document.getElementById('bobot_display');

    function calculateGrade() {
        const tugas = parseFloat(nilaiTugas.value) || 0;
        const uts = parseFloat(nilaiUts.value) || 0;
        const uas = parseFloat(nilaiUas.value) || 0;

        if (tugas === 0 && uts === 0 && uas === 0) {
            nilaiAkhirDisplay.textContent = '-';
            hurufMutuDisplay.textContent = '-';
            bobotDisplay.textContent = '-';
            return;
        }

        // Hitung nilai akhir
        const nilaiAkhir = (tugas * 0.3) + (uts * 0.3) + (uas * 0.4);
        nilaiAkhirDisplay.textContent = nilaiAkhir.toFixed(2);

        // Konversi ke huruf mutu dan bobot
        let hurufMutu = '-';
        let bobot = '-';

        if (nilaiAkhir >= 85) {
            hurufMutu = 'A';
            bobot = '4.00';
        } else if (nilaiAkhir >= 80) {
            hurufMutu = 'A-';
            bobot = '3.75';
        } else if (nilaiAkhir >= 75) {
            hurufMutu = 'B+';
            bobot = '3.50';
        } else if (nilaiAkhir >= 70) {
            hurufMutu = 'B';
            bobot = '3.00';
        } else if (nilaiAkhir >= 65) {
            hurufMutu = 'B-';
            bobot = '2.75';
        } else if (nilaiAkhir >= 60) {
            hurufMutu = 'C+';
            bobot = '2.50';
        } else if (nilaiAkhir >= 55) {
            hurufMutu = 'C';
            bobot = '2.00';
        } else if (nilaiAkhir >= 50) {
            hurufMutu = 'C-';
            bobot = '1.75';
        } else if (nilaiAkhir >= 40) {
            hurufMutu = 'D';
            bobot = '1.00';
        } else {
            hurufMutu = 'E';
            bobot = '0.00';
        }

        hurufMutuDisplay.textContent = hurufMutu;
        bobotDisplay.textContent = bobot;

        // Warna berdasarkan huruf mutu
        if (hurufMutu === 'A') {
            hurufMutuDisplay.className = 'px-4 py-2 bg-green-100 border-2 border-green-500 rounded-lg text-green-900 font-bold text-lg';
        } else if (hurufMutu === 'A-') {
            hurufMutuDisplay.className = 'px-4 py-2 bg-green-50 border border-green-300 rounded-lg text-green-800 font-semibold';
        } else if (hurufMutu.startsWith('B')) {
            hurufMutuDisplay.className = 'px-4 py-2 bg-blue-100 border border-blue-300 rounded-lg text-blue-800 font-semibold';
        } else if (hurufMutu.startsWith('C')) {
            hurufMutuDisplay.className = 'px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-lg text-yellow-800 font-semibold';
        } else if (hurufMutu === 'D') {
            hurufMutuDisplay.className = 'px-4 py-2 bg-orange-100 border border-orange-300 rounded-lg text-orange-800 font-semibold';
        } else if (hurufMutu === 'E') {
            hurufMutuDisplay.className = 'px-4 py-2 bg-red-100 border border-red-300 rounded-lg text-red-800 font-semibold';
        } else {
            hurufMutuDisplay.className = 'px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-900 font-semibold';
        }
    }

    nilaiTugas.addEventListener('input', calculateGrade);
    nilaiUts.addEventListener('input', calculateGrade);
    nilaiUas.addEventListener('input', calculateGrade);

    // Hitung saat pertama kali load
    calculateGrade();
});
</script>
@endsection

