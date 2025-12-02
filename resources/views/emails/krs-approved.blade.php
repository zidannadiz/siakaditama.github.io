@extends('emails.layouts.app')

@section('header', 'KRS Disetujui')

@section('content')
<p>Yth. <strong>{{ $user->name }}</strong>,</p>

<p>Kami menginformasikan bahwa <strong>Kartu Rencana Studi (KRS)</strong> Anda telah <strong>disetujui</strong>.</p>

<div class="alert alert-success">
    <strong>KRS Disetujui!</strong><br>
    Mata kuliah yang Anda pilih telah disetujui oleh admin.
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
        <span class="info-label">SKS:</span>
        <span class="info-value">{{ $krs->jadwalKuliah->mataKuliah->sks }} SKS</span>
    </div>
    <div class="info-row">
        <span class="info-label">Status:</span>
        <span class="info-value" style="color: #10b981; font-weight: 600;">DISETUJUI</span>
    </div>
</div>

<p>Anda dapat melihat detail KRS Anda dengan mengakses sistem SIAKAD.</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('mahasiswa.krs.index') }}" class="button">Lihat KRS Saya</a>
</div>

<p>Jika Anda memiliki pertanyaan, silakan hubungi admin atau bagian akademik.</p>

<p>Terima kasih,<br>
<strong>Tim SIAKAD</strong></p>
@endsection

