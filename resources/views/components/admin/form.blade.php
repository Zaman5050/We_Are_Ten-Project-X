@props([
'company_uuid',
])
<form action="{{ route('super-admin.company.storeCompanyAdmin') }}" method="POST">
    @csrf

    <input type="hidden" name="company_uuid" value="{{ $company_uuid }}">

    <div class="sign-up-input-container">
        <label class="signup-labels" for="name">ADMIN NAME</label>
        <input class="bg-input signup-inputs @error('name') is-invalid @enderror" id="name" required placeholder="ADMIN Name"
            type="text" name="name" value="{{ old('name') }}">
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="sign-up-input-container">
        <label class="signup-labels" for="email">ADMIN EMAIL</label>
        <input class="bg-input signup-inputs @error('email') is-invalid @enderror" id="email" required
            placeholder="Email Address" type="email" name="email" value="{{ old('email') }}">
        @error('email')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="sign-up-input-container">
        <label class="signup-labels" for="password">CREATE PASSWORD</label>
        <input class="bg-input signup-inputs @error('password') is-invalid @enderror" id="password" required
            placeholder="********" type="password" autocomplete="new-password" name="password">
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="sign-up-input-container">
        <label class="signup-labels" for="password_confirmation">CONFIRM PASSWORD</label>
        <input class="bg-input signup-inputs @error('password_confirmation') is-invalid @enderror" id="password_confirmation" required
            placeholder="********" type="password" name="password_confirmation">
        @error('password_confirmation')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <button class="log-in" type="submit">Register Admin</button>
</form>