@props([
'company' => '',
])

<p data-company_status="{{ $company->status }}" data-uuid="{{ $company->uuid }}" role="button"
    {{ $attributes->merge(['class' => 'handleToggleStatus text-underline '.($company->status == 'active' ? 'text-danger' : 'text-success')]) }}>
    <u>
        {{ $company->status == 'active' ? 'Deactivate' : 'Reactivate' }}
    </u>
</p>