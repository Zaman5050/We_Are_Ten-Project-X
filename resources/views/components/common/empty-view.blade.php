@props([
    'message' => 'Sorry! No records were found.',
])
<div class="create-project-container ">
    <div class="text-center">
        <img style="width:217px ;height:214px ; object-fit: cover;"
            src="{{ asset('assets/images/add-material-default.png') }}">
        <h2 style="margin-bottom: 12px;" class="signup-headings">
            {{ $message }}
        </h2>
        <span class="create-to-start d-flex justify-content-center gap-1">
            {{ $slot }}
        </span>
    </div>
</div>
