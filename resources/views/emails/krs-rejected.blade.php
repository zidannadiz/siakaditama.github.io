@extends('emails.layouts.app')

@section('header', 'KRS Ditolak')

@section('content')
<p>Yth. <strong>{{ $user->name }}</strong>,</p>

<p>Kami menginformasikan bahwa <strong>Kartu Rencana Studi (KRS)</strong> Anda telah <strong>ditolak</strong>.</p>

<div class="alert alert-error">
    <strong>KRS Ditolak</strong><br>
    Mata kuliah yang Anda pilih tidak dapat disetujui.
</div>

<div class="info-box">
    <div class="info-row">
        <span class="info-label">Nama Mahasiswa:</span>
        <span class="info-value">{{ $krs->mahasiswa->nama }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">NIM:</span>
        <span class="info-value">{{ $krs->mahasiswa->nim }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Mata Kuliah:</span>
        <span class="info-value">{{ $krs->jadwalKuliah->mataKuliah->nama_mk }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Kode Mata Kuliah:</span>
        <span class="info-value">{{ $krs->jadwalKuliah->mataKuliah->kode_mk }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Semester:</span>
        <span class="info-value">{{ $krs->semester->nama_semester ?? '-' }}</span>
    </div>
    <div class="info-row">
        <span class="info-label">Status:</span>
        <span class="info-value" style="color: #ef4444; font-weight: 600;">DITOLAK</span>
    </div>
</div>

@if($reason)
<div class="alert alert-info">
    <strong>Alasan Penolakan:</strong><br>
    {{ $reason }}
</div>
@endif

<p>Silakan periksa kembali KRS Anda dan hubungi bagian akademik jika Anda memiliki pertanyaan.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('mahasiswa.krs.index') }}" class="button">Lihat KRS Saya</a>
</div>

<p>Jika Anda memiliki pertanyaan, silakan hubungi admin atau bagian akademik.</p>

<p>Terima kasih,<br>
<strong>Tim SIAKAD</strong></p>
@endsection

