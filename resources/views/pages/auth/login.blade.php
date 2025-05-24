<!-- resources/views/welcome.blade.php -->
@extends('layouts.guest')

@section('title', 'Login')

@section('content')

    <h2 class="signup-headings">Log In</h2>
    <form action="{{route('auth.login')}}" method="POST">
        @csrf
        <div class="sign-up-input-container">
            <label class="signup-labels">EMAIL</label>
            <input class="signup-inputs @error('email') is-invalid @enderror" placeholder="Email Address" type="email" name="email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="sign-up-input-container">
            <label class="signup-labels">PASSWORD</label>
            <input class="signup-inputs @error('password') is-invalid @enderror" placeholder="********" type="password" name="password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <a class="forgot-password" href="{{ tenant_route('password.email.show') }}">Forgot Password?</a>
        <button class="log-in" type="submit">Log In</button>
    </form>
@endsection
