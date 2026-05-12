<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Branches - Rich Labz CRM</title>
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
            background-color: #f9f9fb;
        }

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .badge-active {
            background: #28a745 !important;
            color: #fff !important;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .badge-inactive {
            background: #dc3545 !important;
            color: #fff !important;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .table-actions a {
            margin-right: 6px;
            font-size: 15px;
        }
    </style>
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <!-- PAGE HEADER -->
            <div class="crm-header">
                <div class="crm-title">Manage Branches</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-branch') }}" class="create-invoice-btn">
                        Add New Branch
                    </a>
                </div>
            </div>

            <!-- FILTER BAR -->
            <form id="filterForm" class="neat-search-section mb-3"
                style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;" autocomplete="off">

                <!-- Search -->
                <div class="input-icon-search" style="flex:1 1 240px;">
                    <label>Search</label>
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchText" class="form-control form-control-sm"
                        placeholder="Branch name, location">
                </div>

                <!-- Status Filter (RESTORED) -->
                <div class="input-select" style="flex:1 1 150px;">
                    <label>Status</label>
                    <i class="bi bi-check-circle left-icon"></i>

                    <select id="statusFilter" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>

                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <!-- Buttons -->
                <!-- <div style="flex:0 0 auto;display:flex;gap:8px;">
                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        Submit
                    </button>
                </div> -->

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
                                <th style="width:60px;">SI.No</th>
                                <th>Branch Name</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th style="width:170px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="branchTableBody">
                            @foreach($branches as $index => $branch)
                                <tr>
                                    <td>{{ ($branches->currentPage() - 1) * $branches->perPage() + ($index + 1) }}</td>
                                    <td>{{ $branch->branch_name }}</td>
                                    <td>{{ $branch->location->location_name ?? '-' }}</td>

                                    <td>
                                        @if($branch->status == 1)
                                            <span class="badge-active">Active</span>
                                        @else
                                            <span class="badge-inactive">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="table-actions">
                                        <a href="{{ route('edit-branch', $branch->id) }}" class="action-btn edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>

                                        <a href="{{ route('delete-branch', $branch->id) }}" class="action-btn delete"
                                            onclick="return confirm('Are you sure?')">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

                <!-- PAGINATION -->
                <nav id="paginationLinks">
                    {{ $branches->links() }}
                </nav>

            </div>

        </div>
    </div>

    @include('includes.footer_links')


    <!-- AJAX SCRIPT -->
    <script>
        function fetchBranches(page = 1) {
            let search = $('#searchText').val();
            let status = $('#statusFilter').val();

            $.ajax({
                url: "{{ route('ajax-branches') }}?page=" + page,
                type: "GET",
                data: {
                    searchText: search,
                    statusFilter: status
                },
                success: function (response) {

                    let rows = "";

                    if (response.branches.data.length > 0) {

                        response.branches.data.forEach((branch, index) => {
                            rows += `
                        <tr>
                            <td>${((response.branches.current_page - 1) * response.branches.per_page) + (index + 1)}</td>
                            <td>${branch.branch_name}</td>
                            <td>${branch.location ? branch.location.location_name : '-'}</td>
                            <td>
                                ${branch.status == 1
                                    ? '<span class="badge-active">Active</span>'
                                    : '<span class="badge-inactive">Inactive</span>'}
                            </td>
                            <td class="table-actions">
                                <a href="/admin/edit-branch/${branch.id}" class="action-btn edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="/admin/delete-branch/${branch.id}" class="action-btn delete"
                                   onclick="return confirm('Are you sure?')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                        });

                    } else {
                        rows = `<tr><td colspan="5" class="text-center text-muted">No branches found</td></tr>`;
                    }

                    $('#branchTableBody').html(rows);

                    $('#paginationLinks').html(response.pagination);
                }
            });
        }

        $('#searchText').keyup(function () { fetchBranches(); });
        $('#statusFilter').change(function () { fetchBranches(); });

        $('#filterForm').submit(function (e) {
            e.preventDefault();
            fetchBranches();
        });

        $('#refreshBtn').click(function () {
            $('#filterForm')[0].reset();
            fetchBranches();
        });

        $(document).on('click', '#paginationLinks a', function (e) {
            e.preventDefault();
            let page = $(this).attr('href').split('page=')[1];
            fetchBranches(page);
        });
    </script>

</body>

</html>