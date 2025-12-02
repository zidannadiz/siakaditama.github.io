@extends('emails.layouts.app')

@section('header', $title)

@section('content')
<p>Yth. <strong>{{ $user->name }}</strong>,</p>

<div class="alert alert-{{ $type === 'error' ? 'error' : ($type === 'success' ? 'success' : 'info') }}">
    <strong>{{ $title }}</strong>
</div>

<div style="margin: 20px 0;">
    {!! nl2br(e($message)) !!}
</div>

@if($actionUrl)
<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $actionUrl }}" class="button">{{ $actionText }}</a>
</div>
@endif

<p>Terima kasih,<br>
<strong>Tim SIAKAD</strong></p>
@endsection

