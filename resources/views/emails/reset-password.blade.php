@extends('emails.layouts.app')

@section('header', 'Reset Password')

@section('content')
<p>Yth. <strong>{{ $user->name }}</strong>,</p>

<div class="alert alert-info">
    <strong>Permintaan Reset Password</strong><br>
    Kami menerima permintaan untuk mereset password akun SIAKAD Anda.
</div>

<p>Untuk mereset password Anda, silakan klik tombol di bawah ini:</p>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $resetUrl }}" class="button">Reset Password</a>
</div>

<p style="color: #6b7280; font-size: 14px;">
    Atau salin dan buka link berikut di browser Anda:<br>
    <a href="{{ $resetUrl }}" style="color: #3b82f6; word-break: break-all;">{{ $resetUrl }}</a>
</p>

<div class="alert alert-warning" style="background-color: #fef3c7; border-left: 4px solid #f59e0b; color: #92400e; margin: 20px 0; padding: 15px; border-radius: 6px;">
    <strong>Perhatian:</strong>
    <ul style="margin: 10px 0 0 20px; padding: 0;">
        <li>Link reset password berlaku selama <strong>60 menit</strong></li>
        <li>Jika Anda tidak meminta reset password, abaikan email ini</li>
        <li>Jangan bagikan link ini kepada siapapun</li>
    </ul>
</div>

<p>Jika Anda memiliki pertanyaan atau mengalami masalah, silakan hubungi admin sistem.</p>

<p>Terima kasih,<br>
<strong>Tim SIAKAD</strong></p>
@endsection


