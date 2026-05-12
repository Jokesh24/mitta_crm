<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Companies - Rich Labz CRM</title>

    @include('includes.header_links')

    <style>
        .input-icon-search i,
        .input-select i.left-icon {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon-search input {
            padding-left: 38px !important;
        }

        .input-select select {
            padding-left: 38px !important;
            padding-right: 40px !important;
            appearance: none;
        }

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .badge-active {
            background: #28a745;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .badge-inactive {
            background: #dc3545;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .table-actions a {
            margin-right: 6px;
            font-size: 14px;
        }

        .table-actions .view {
            color: #0d6efd;
        }

        .table-actions .edit {
            color: #198754;
        }

        .table-actions .delete {
            color: #dc3545;
        }
    </style>

</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Manage Companies</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-company') }}" class="create-invoice-btn">
                        Add New Company
                    </a>
                </div>
            </div>

            <!-- FILTER BAR -->
            <form class="neat-search-section mb-3" autocomplete="off" id="filterForm"
                style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;">

                <div class="input-icon-search" style="flex:1 1 220px;">
                    <label>Search</label>
                    <i class="bi bi-search"></i>
                    <input id="searchText" type="text" class="form-control form-control-sm"
                        placeholder="Company Name, Contact Person, Phone">
                </div>

                <div class="input-select" style="flex:1 1 150px;">
                    <label>Status</label>
                    <i class="bi bi-toggle-on left-icon"></i>
                    <select id="statusFilter" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <div style="flex:0 0 auto;display:flex;gap:8px;align-items:center;">
                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm px-4">Submit</button>
                </div>

            </form>
            @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <strong><i class="bi bi-check-circle-fill"></i></strong> 
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <strong><i class="bi bi-exclamation-triangle-fill"></i></strong> 
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
         
            <!-- TABLE -->
            <div class="table-crm">
                <div class="table-responsive">

                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr>
                                <th style="width: 60px;">SI.No</th>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th style="width:170px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="companyTableBody">
                            @foreach($companies as $index => $company)
                                <tr>
                                    <td>{{ ($companies->currentPage() - 1) * $companies->perPage() + ($index + 1) }}</td>
                                    <td>{{ $company->company_name }}</td>
                                    <td>{{ $company->contact_person }}</td>
                                    <td>{{ $company->phone }}</td>
                                    <td>{{ $company->email }}</td>
                                    <td>
                                        @if($company->status == 1)
                                            <span class="badge-active">Active</span>
                                        @else
                                            <span class="badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="table-actions">
                                        <a href="#" class="action-btn view"><i class="bi bi-eye-fill"></i></a>
                                        <a href="{{ route('edit-company', $company->id) }}" class="action-btn edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="#" onclick="confirmDelete({{ $company->id }})" class="action-btn delete"><i
                                                class="bi bi-trash3-fill"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

                <!-- PAGINATION -->
                <nav id="paginationLinks">
                    {{ $companies->links() }}
                </nav>

            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        function fetchCompanies(page = 1) {

            let search = $('#searchText').val().trim();
            let status = $('#statusFilter').val();

            $.ajax({
                url: "{{ route('ajax-companies') }}?page=" + page,
                type: "GET",
                data: {
                    searchText: search,
                    statusFilter: status
                },
                success: function (response) {

                    let rows = "";

                    if (response.companies.data.length > 0) {
                        response.companies.data.forEach((company, index) => {

                            rows += `
                        <tr>
                            <td>${((response.companies.current_page - 1) * response.companies.per_page) + (index + 1)}</td>
                            <td>${company.company_name}</td>
                            <td>${company.contact_person ?? '-'}</td>
                            <td>${company.phone ?? '-'}</td>
                            <td>${company.email ?? '-'}</td>
                            <td>${company.status == 1
                                    ? '<span class="badge-active">Active</span>'
                                    : '<span class="badge-inactive">Inactive</span>'
                                }</td>
                            <td class="table-actions">
                                <a href="#" class="action-btn view"><i class="bi bi-eye-fill"></i></a>
                                <a href="#" class="action-btn edit"><i class="bi bi-pencil-fill"></i></a>
                                <a href="#" onclick="confirmDelete(${company.id})"
                                   class="action-btn delete"><i class="bi bi-trash3-fill"></i></a>
                            </td>
                        </tr>
                    `;
                        });
                    } else {
                        rows = `<tr><td colspan="7" class="text-center text-muted">No companies found</td></tr>`;
                    }

                    $('#companyTableBody').html(rows);
                    $('#paginationLinks').html(response.pagination);
                }
            });
        }

        $('#searchText').on('keyup', function () {
            fetchCompanies();
        });

        $('#statusFilter').on('change', function () {
            fetchCompanies();
        });

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            fetchCompanies();
        });

        $('#refreshBtn').on('click', function () {
            $('#filterForm')[0].reset();
            fetchCompanies();
        });

        $(document).on('click', '#paginationLinks a', function (e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            fetchCompanies(page);
        });
    </script>

</body>

</html>