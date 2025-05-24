<ul class="nav nav-tabs border-0" id="myTab" role="tablist">
    <li class="nav-item">

        <a class="summary-finalcial px-2 {{ request()->routeIs('tenant.project.financials.budget') ? 'active' : '' }}"
            href="{{ tenant_route('project.financials.budget', ['project' => request()->project]) }}">
            Budget
        </a>
    </li>
    <li class="nav-item d-none">
        <a class="summary-finalcial px-2 {{ request()->routeIs('tenant.project.financials.costing') ? 'active' : '' }}"
            href="{{ tenant_route('project.financials.costing', ['project' => request()->project]) }}">
            Costing
        </a>
    </li>
    <li class="nav-item">
        <a class="summary-finalcial px-2 {{ request()->routeIs('tenant.project.financials.procurement') ? 'active' : '' }}"
            href="{{ tenant_route('project.financials.procurement', ['project' => request()->project]) }}">
            Procurement
        </a>
    </li>
    <li class="nav-item">
        <a class="summary-finalcial px-2 {{ request()->routeIs('tenant.project.financials.services') ? 'active' : '' }}"
            href="{{ tenant_route('project.financials.services', ['project' => request()->project]) }}">
            Services
        </a>
    </li>
</ul>
