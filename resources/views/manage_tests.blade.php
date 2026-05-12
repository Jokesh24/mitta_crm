<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Parameters - Rich Labz CRM</title>
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

        .input-select {
            position: relative;
        }

        .input-select select {
            padding-left: 38px !important;
            padding-right: 38px !important;
            appearance: none;
            background-color: #f9f9fb;
        }

        .input-select i.dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6c757d;
        }

        .table-crm table th,
        .table-crm table td {
            white-space: nowrap !important;
        }
    </style>

</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Manage Parameter</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-test') }}" class="create-invoice-btn">
                        Add New Parameter
                    </a>

                    <a href="{{ route('tests.order') }}" class="create-invoice-btn">
                        Parameter Display Order
                    </a>
                </div>
            </div>

            <form class="neat-search-section mb-3" autocomplete="off" id="filterForm"
                style="display: flex; align-items: end; gap: 12px; flex-wrap: wrap;">

                <div class="input-icon-search" style="flex: 1 1 200px;">
                    <label>Search</label>
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchText" class="form-control form-control-sm"
                        placeholder="Parameter name">
                </div>

                <!-- <div class="input-icon-search" style="flex: 1 1 150px;">
                <label>Sample Type</label>
                <i class="bi bi-droplet"></i>
                <input type="text" id="sampleFilter" class="form-control form-control-sm" placeholder="Blood/Urine">
            </div> -->

                <!-- <div class="input-select" style="flex: 1 1 150px;">
                <label>Status</label>
                <i class="bi bi-check-circle left-icon"></i>
                <select id="statusFilter" class="form-control form-control-sm">
                    <option value="">All</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </div> -->

                <!-- <div style="flex: 0 0 auto; display: flex; gap: 8px; align-items: center;">
                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width: 36px; height: 36px;" title="Refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>

                    <button type="button" id="openSidebarBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width: 36px; height: 36px;" title="Advanced Filters">
                        <i class="bi bi-sliders"></i>
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm px-4">Submit</button>
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
            <div class="table-crm">
                <div class="table-responsive">

                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr>
                                <th style="width: 60px;">SI.No</th>
                                <th>Parameter Name</th>
                                <!-- <th>Sample Type</th> -->
                                <th>Unit</th>
                                <th>Price</th>
                                <th>Normal Range</th>
                                <!-- <th>Status</th> -->
                                <th style="width: 170px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="testTableBody">
                            @foreach($tests as $index => $test)
                                <tr>
                                    <td>{{ ($tests->currentPage() - 1) * $tests->perPage() + ($index + 1) }}</td>
                                    <td>{{ $test->test_name }}</td>
                                    <!-- <td>{{ $test->sample_type ?? '-' }}</td> -->
                                    <td>{{ $test->unit->unit_name ?? '-' }}</td>
                                    <td>{{ $test->price ?? 'N/A' }}</td>
                                    <td>{{ $test->normal_range ?? '-' }}</td>
                                    <!-- <td>
                                                        @if($test->status == 1)
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td> -->
                                    <td class="table-actions">
                                        <a href="{{ url('admin/edit_parameter/' . $test->id) }}" class="action-btn edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="{{ url('admin/delete-parameter/' . $test->id) }}" class="action-btn delete"
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
                    {{ $tests->links() }}
                </nav>


            </div>

        </div>
    </div>

    @include('includes.footer_links')

</body>

</html>


<script>
    function fetchTests(page = 1) {
        let search = $('#searchText').val();

        $.ajax({
            url: "{{ route('ajax-tests') }}?page=" + page,
            type: "GET",
            data: {
                searchText: search
            },
            success: function (response) {

                let rows = "";

                if (response.tests.data.length > 0) {

                    response.tests.data.forEach((test, index) => {
                        rows += `
<tr>
    <td>${((response.tests.current_page - 1) * response.tests.per_page) + (index + 1)}</td>
    <td>${test.test_name}</td>
    <td>${test.unit ? test.unit.unit_name : '-'}</td>
    <td>${test.price ?? 'N/A'}</td>
    <td>${test.normal_range ?? '-'}</td>
    <td class="table-actions">
        <a href="{{ url('admin/edit_parameter') }}/${test.id}" class="action-btn edit">
            <i class="bi bi-pencil-fill"></i>
        </a>
        <a href="{{ url('admin/delete-parameter') }}/${test.id}" class="action-btn delete"
           onclick="return confirm('Are you sure?')">
           <i class="bi bi-trash-fill"></i>
        </a>
    </td>
</tr>
`;

                    });

                } else {
                    rows = `<tr><td colspan="5" class="text-center text-muted">No tests found</td></tr>`;
                }

                $('#testTableBody').html(rows);

                $('#paginationLinks').html(response.pagination);
            }
        });
    }

    // Live typing search
    $('#searchText').on('keyup', function () {
        fetchTests();
    });

    // Submit button
    $('#filterForm').submit(function (e) {
        e.preventDefault();
        fetchTests();
    });

    // Refresh button
    $('#refreshBtn').on('click', function () {
        $('#filterForm')[0].reset();
        fetchTests();
    });

    // AJAX pagination
    $(document).on('click', '#paginationLinks a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetchTests(page);
    });
</script>