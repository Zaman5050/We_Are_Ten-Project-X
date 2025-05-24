<!-- resources/views/welcome.blade.php -->
@extends('layouts.guest')

@section('title', 'Forget Password')

@section('content')

    <h2 class="signup-headings">Forgot Password</h2>
    <form method="POST" action="{{ tenant_route('password.email.send') }}" class="g-2">

        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @csrf
        <div class="sign-up-input-container">
            <label class="signup-labels text-uppercase" for="email">Email</label>
            <input class="signup-inputs @error('email') is-invalid @enderror" autocomplete="false" id="email" name="email" placeholder="Enter Email" type="email">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

        </div>
        <button class="log-in mt-2" type="submit">Reset Password</a>
    </form>
@endsection
