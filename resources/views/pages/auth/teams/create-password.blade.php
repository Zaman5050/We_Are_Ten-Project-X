<!-- resources/views/welcome.blade.php -->
@extends('layouts.guest')

@section('title', 'Set Up Account')

@section('content')

    <h2 class="signup-headings">Set Up Account</h2>
    <h2 class="signup-headings fs-16 darker-grotesque-regular text-uppercase mb--20">You have been invited by {{ $company?->name }} as a Team Member</h2>

    <form  action="{{ tenant_route('create-password.store', ['token' => $token]) }}" method="POST" >
        @csrf
        <div class="sign-up-input-container">
            <label class="signup-labels">CREATE PASSWORD</label>
            <input class="signup-inputs @error('password') is-invalid @enderror" placeholder="********" type="password"
                autocomplete="new-password" name="password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="sign-up-input-container">
            <label class="signup-labels">CONFIRM PASSWORD</label>
            <input class="signup-inputs @error('password_confirmation') is-invalid @enderror" placeholder="********"
                type="password" name="password_confirmation">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
         <button class="log-in" type="submit">Log In</button>
    </form>
@endsection
