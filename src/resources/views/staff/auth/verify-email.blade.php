@extends('layouts.default')

@section('title','メール認証')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/verify.css')  }}">
@endsection

@section('content')
@include('components.header')
<div class="checkout-container">
    <p class="checkout-text">登録していただいたメールアドレスに認証メールを送付しました。<br />メール認証を完了してください。</p>

    <form method="GET" action="{{ route('verification.now') }}">
        <button type="submit" class="auth-button">認証はこちらから</button>
    </form>

    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
        @csrf
        <button type="submit" class="link">認証メールを再送する</button>
    </form>
</div>
@endsection