@props([
'companies',
])
<table class="table mb-0 dashboard-table" >
    <thead>
        <tr>
            <th>Serial No.</th>
            <th>Company Name</th>
            <th>Virtual Domain Url</th>
            <th>Admin Name</th>
            <th>Actions </th>

        </tr>
    </thead>

    <tbody id="super-dashboard-table">

        @foreach ($companies as $companyIndex => $company)
        <tr>
            <td>{{ ++$companyIndex }}</td>
            <td>
                <a  class="text-decoration-underline" style="color: black" href="{{ route('super-admin.companies.edit', $company->uuid) }}">
                    {{ $company->name }}
                </a>
            </td>
            <td>{{ $company->virtual_url }}</td>
            <td>{{ $company?->admin?->full_name }}</td>
            <td>
                <div class="d-flex gap-3 align-items-center justify-content-start">

                    <x-company.toggle-status-modal
                    :company="$company"
                    />
                    <p class="handleChangePassword text-decoration-underline" role="button" data-uuid="{{ $company?->admin?->uuid }}">
                        Change Password
                    </p>
                </div>
            </td>
        </tr>
        @endforeach

    </tbody>

</table>
