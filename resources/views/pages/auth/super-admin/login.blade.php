<!-- resources/views/welcome.blade.php -->
@extends('layouts.guest')

@section('title', 'Login')

@section('content')

    <h2 class="signup-headings">Log In</h2>
    <form action="{{route('auth.login')}}" method="POST">
        @csrf
        <div class="sign-up-input-container">
            <label class="signup-labels" for="email">EMAIL</label>
            <input class="signup-inputs @error('email') is-invalid @enderror" id="email" required placeholder="Email Address" type="email" name="email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="sign-up-input-container">
            <label class="signup-labels" for="password">PASSWORD</label>
            <input class="signup-inputs @error('password') is-invalid @enderror" id="password" required placeholder="********" type="password" name="password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button class="log-in" type="submit">Log In</button>
    </form>
@endsection
