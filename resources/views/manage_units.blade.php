<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Test & Measurement - Rich Labz CRM</title>

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
            padding-right: 40px !important;
            background: #f9f9fb;
        }

        .input-select i.dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #6c757d;
            pointer-events: none;
        }

        .badge-active {
            background: #28a745 !important;
            color: #fff;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .badge-inactive {
            background: #dc3545 !important;
            color: #fff;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .table-actions a {
            margin-right: 6px;
            font-size: 14px;
        }

        .table-actions .edit {
            color: #198754;
        }

        .table-actions .delete {
            color: #dc3545;
        }

        .table-actions a:hover {
            opacity: 0.7;
        }
    </style>
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Manage units</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-unit') }}" class="create-invoice-btn">
                        Add New Unit
                    </a>
                </div>
            </div>

            <form class="neat-search-section mb-3 row" autocomplete="off" id="filterForm">


                <div class="col-md-4">
                    <div class="input-icon-search">
                        <label>Search</label>
                        <i class="bi bi-search"></i>
                        <input type="text" id="searchText" class="form-control form-control-sm"
                            placeholder="Search Unit">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-select">
                        <label>Status</label>
                        <i class="bi bi-toggle-on left-icon"></i>
                        <select id="statusFilter" class="form-control form-control-sm">
                            <option value="">All</option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        <i class="bi bi-chevron-down dropdown-icon"></i>
                    </div>
                </div>

                <div class="col-md-3">
                    <div>
                        <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                            style="width:36px; height:36px;">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>

                        <button type="submit" class="btn btn-primary btn-sm px-4">
                            Submit
                        </button>
                    </div>

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
            <div class="table-crm">
                <div class="table-responsive">
                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr>
                                <th style="width:60px;">SI.No</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="testTableBody">
                            @foreach($units as $index => $unit)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $unit->unit_name }}</td>
                                    <td>
                                        @if($unit->status == 1)
                                            <span class="badge-active">Active</span>
                                        @else
                                            <span class="badge-inactive">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="table-actions">
                                        <a href="{{ route('edit-unit', $unit->id) }}" class="edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="{{ route('delete-unit', $unit->id) }}" class="delete"
                                            onclick="return confirm('Are you sure want to delete this unit?')">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <nav id="paginationLinks">
                    {{ $units->links() }}
                </nav>


            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        $('#refreshBtn').click(function () {
            $('#filterForm')[0].reset();
        });
    </script>

</body>

</html>


<script>
    function fetchUnits(page = 1) {
        let search = $('#searchText').val();
        let status = $('#statusFilter').val();

        $.ajax({
            url: "{{ route('units-list') }}?page=" + page,
            type: "GET",
            data: {
                searchText: search,
                statusFilter: status
            },
            success: function (response) {

                // 1. Update table body
                let rows = '';
                if (response.units.data.length > 0) {

                    response.units.data.forEach((unit, index) => {
                        rows += `
                        <tr>
                            <td>${((response.units.current_page - 1) * response.units.per_page) + (index + 1)}</td>
                            <td>${unit.unit_name}</td>
                            <td>
                                ${unit.status == 1
                                ? '<span class="badge-active">Active</span>'
                                : '<span class="badge-inactive">Inactive</span>'
                            }
                            </td>
                            <td class="table-actions">
                                <a href="edit-unit/${unit.id}" class="edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="delete-unit/${unit.id}" class="delete" onclick="return confirm(\'Are you sure want to delete this unit?\')">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    });

                } else {
                    rows = `<tr><td colspan="4" class="text-center text-muted">No units found</td></tr>`;
                }

                $('#testTableBody').html(rows);

                // 2. Update pagination
                $('#paginationLinks').html(response.pagination);
            }
        });
    }

    // Search typing
    $('#searchText').on('keyup', function () {
        fetchUnits();
    });

    // Status filter change
    $('#statusFilter').on('change', function () {
        fetchUnits();
    });

    // Submit button
    $('#filterForm').submit(function (e) {
        e.preventDefault();
        fetchUnits();
    });

    // Refresh button
    $('#refreshBtn').click(function () {
        $('#filterForm')[0].reset();
        fetchUnits();
    });

    // Pagination click
    $(document).on('click', '#paginationLinks a', function (e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        fetchUnits(page);
    });
</script>