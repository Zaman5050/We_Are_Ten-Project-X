<form action="{{ route('super-admin.companies.update', $company->uuid) }}" method="POST" id="upd-company" class="row g-3">
    @csrf
    @method('PUT')
    <div class="col1">
        <div class="sign-up-input-container">
            <label class="signup-labels" for="name">Company Name</label>
            <input class="bg-input signup-inputs @error('name') is-invalid @enderror" id="name" required 
                placeholder="Enter Company" type="text" name="name"  readonly
                value="{{ old('name', $company->name) }}"> 
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    
        <div class="sign-up-input-container">
            <label class="signup-labels" for="email">Company Email</label>
            <input class="bg-input signup-inputs @error('email') is-invalid @enderror" required 
                placeholder="Enter Email" id="email" type="email" name="email" 
                value="{{ old('email', $company->email) }}"> 
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    
        <div class="sign-up-input-container">
            <label class="signup-labels" for="subdomain">Virtual Domain</label>
            <input class="bg-input signup-inputs @error('subdomain') is-invalid @enderror" required 
                placeholder="projectinsite.co.uk" id="subdomain" type="text" name="subdomain" readonly
                value="{{ old('subdomain', $company->virtual_url) }}"> 
            @error('subdomain')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col2">

        <div class="sign-up-input-container">
            <label class="signup-labels" for="admin_name">Admin Name</label>
            <input class="bg-input signup-inputs @error('admin_name') is-invalid @enderror" required 
                placeholder="Enter Admin Name" id="admin_name" type="text" name="admin_name" 
                value="{{ old('admin_name', $company->admin->full_name ?? '') }}"> 
            @error('admin_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    
        <!-- New Admin Email Field -->
        <div class="sign-up-input-container">
            <label class="signup-labels" for="admin_email">Admin Email</label>
            <input class="bg-input signup-inputs @error('admin_email') is-invalid @enderror" required 
                placeholder="Enter Admin Email" id="admin_email" type="email" name="admin_email" 
                value="{{ old('admin_email', $company->admin->email ?? '') }}"> 
            @error('admin_email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>
    <!-- New Admin Name Field -->

    <button class="log-in" type="submit">Update Company</button>
</form>
