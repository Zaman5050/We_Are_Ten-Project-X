<!-- resources/views/welcome.blade.php -->
@extends('layouts.guest')

@section('title', 'Login')

@section('content')
 
    <h2 class="signup-headings">Reset Password</h2>
    <form method="POST" action="{{ tenant_route('password.reset.put') }}" class="g-2">
        @csrf

        <input type="hidden" name="token" value="{{ request()->token }}"> 
        <input type="hidden" name="email" value="{{ request()->email }}"> 
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <div class="sign-up-input-container">
            <label class="signup-labels">CREATE NEW PASSWORD</label>
            <input class="signup-inputs @error('password') is-invalid @enderror" placeholder="********" type="password" autocomplete="new-password" name="password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="sign-up-input-container">
            <label class="signup-labels">CONFIRM PASSWORD</label>
            <input class="signup-inputs @error('password_confirmation') is-invalid @enderror" placeholder="********" type="password" name="password_confirmation">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="log-in">Log In</button>
    </form>
@endsection
