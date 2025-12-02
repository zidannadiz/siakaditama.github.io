@extends('emails.layouts.app')

@section('header', 'Nilai Baru')

@section('content')
<p>Yth. <strong>{{ $user->name }}</strong>,</p>

<p>Kami menginformasikan bahwa <strong>nilai</strong> untuk mata kuliah yang Anda ambil telah <strong>diinput</strong> oleh dosen.</p>

<div class="alert alert-info">
    <strong>Nilai Baru Tersedia!</strong><br>
    Anda dapat melihat nilai Anda di KHS (Kartu Hasil Studi).
</div>

<div class="info-box">
    <div class="info-row">
        <span class="info-label">Nama Mahasiswa:</span>
        <span class="info-value">{{ $nilai->mahasiswa->nama }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">NIM:</span>
        <span class="info-value">{{ $nilai->mahasiswa->nim }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Mata Kuliah:</span>
        <span class="info-value">{{ $nilai->jadwalKuliah->mataKuliah->nama_mk }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Kode Mata Kuliah:</span>
        <span class="info-value">{{ $nilai->jadwalKuliah->mataKuliah->kode_mk }}</span>
    </div>
    @if($nilai->nilai_tugas !== null)
    <div class="info-row">
        <span class="info-label">Nilai Tugas:</span>
        <span class="info-value">{{ number_format($nilai->nilai_tugas, 2) }}</span>
    </div>
    @endif
    @if($nilai->nilai_uts !== null)
    <div class="info-row">
        <span class="info-label">Nilai UTS:</span>
        <span class="info-value">{{ number_format($nilai->nilai_uts, 2) }}</span>
    </div>
    @endif
    @if($nilai->nilai_uas !== null)
    <div class="info-row">
        <span class="info-label">Nilai UAS:</span>
        <span class="info-value">{{ number_format($nilai->nilai_uas, 2) }}</span>
    </div>
    @endif
    @if($nilai->nilai_akhir !== null)
    <div class="info-row">
        <span class="info-label">Nilai Akhir:</span>
        <span class="info-value"><strong>{{ number_format($nilai->nilai_akhir, 2) }}</strong></span>
    </div>
    <div class="info-row">
        <span class="info-label">Huruf Mutu:</span>
        <span class="info-value"><strong>{{ $nilai->huruf_mutu }} ({{ number_format($nilai->bobot, 2) }})</strong></span>
    </div>
    @endif
</div>

<p>Anda dapat melihat detail nilai lengkap dengan mengakses KHS di sistem SIAKAD.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('mahasiswa.khs.index') }}" class="button">Lihat KHS Saya</a>
</div>

<p>Jika Anda memiliki pertanyaan tentang nilai, silakan hubungi dosen pengampu mata kuliah.</p>

<p>Terima kasih,<br>
<strong>Tim SIAKAD</strong></p>
@endsection

