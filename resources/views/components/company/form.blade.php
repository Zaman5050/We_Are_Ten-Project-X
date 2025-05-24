<form action="{{ route('super-admin.companies.store') }}" method="POST" class="row g-3">
    @csrf
    <div class="sign-up-input-container">
        <label class="signup-labels" for="name">Company Name</label>
        <input class="bg-input signup-inputs @error('name') is-invalid @enderror" id="name" required placeholder="Enter Company"
            type="text" name="name" value="{{ old('name') }}">
        @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="sign-up-input-container">
        <label class="signup-labels" id="email">Company Email</label>
        <input class="bg-input signup-inputs @error('email') is-invalid @enderror " required placeholder="Enter Email"
            id="email" type="email" name="email" value="{{ old('email') }}" required>
        @error('email')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>
    <div class="sign-up-input-container">
        <label class="signup-labels" for="subdomain">Virtual domain</label>
        <input class="bg-input signup-inputs @error('subdomain') is-invalid @enderror " required
            placeholder="projectinsite.co.uk" id="subdomain" type="text" name="subdomain" readonly>
        @error('subdomain')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <button class="log-in" type="submit">Register Company</button>
</form>

@push('scripts')

<script>
$(document).ready(function() {
    $('#name').on('keyup change', (e) => {
        const mainDomaian = "{{config('app.domain')}}";
        const nameValue = e.target.value.trim(); // Trim leading and trailing whitespace
        const sanitizedValue = nameValue.replace(/[^a-zA-Z0-9 ]/g, ''); // Remove special characters
        const websiteValue = sanitizedValue === '' ? '' : sanitizedValue.replace(/\s+/g, '').toLowerCase() + '.'+mainDomaian;
        $('#subdomain').val(websiteValue);
    });
});

</script>

@endpush