<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Locations - Rich Labz CRM</title>

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
            background-color: #f9f9fb;
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
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .badge-inactive {
            background: #dc3545;
            color: #fff;
            padding: 4px 10px;
            border-radius: 6px;
        }

        .table-actions a {
            margin-right: 6px;
            font-size: 16px;
        }

        .edit {
            color: #198754;
        }

        .delete {
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
                <div class="crm-title">Manage Locations</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-location') }}" class="create-invoice-btn">
                        Add New Location
                    </a>
                </div>
            </div>

            <!-- FILTER BAR -->
            <form id="filterForm" class="neat-search-section mb-3"
                style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;">

                <div class="input-icon-search" style="flex:1 1 220px;">
                    <label>Search</label>
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchText" class="form-control form-control-sm"
                        placeholder="Location, State, Pincode">
                </div>

                <div class="input-select" style="flex:1 1 150px;">
                    <label>Status</label>
                    <i class="bi bi-check2-circle left-icon"></i>
                    <select id="statusFilter" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <div style="flex:0 0 auto; display:flex; gap:8px;">
                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>

                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        Submit
                    </button>
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
                                <th style="width:60px;">SI.No</th>
                                <th>Location Name</th>
                                <th>State</th>
                                <th>Pincode</th>
                                <th>Status</th>
                                <th style="width:140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="locationTableBody">
                            @foreach($locations as $index => $location)
                                <tr>
                                    <td>{{ ($locations->currentPage() - 1) * $locations->perPage() + ($index + 1) }}</td>
                                    <td>{{ $location->location_name }}</td>
                                    <td>{{ $location->state }}</td>
                                    <td>{{ $location->pincode ?? '-' }}</td>

                                    <td>
                                        @if($location->status == 1)
                                            <span class="badge-active">Active</span>
                                        @else
                                            <span class="badge-inactive">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="table-actions">
                                        <a href="{{ route('edit-location', $location->id) }}" class="edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <a href="#" class="delete" onclick="confirmDelete({{ $location->id }})">
                                            <i class="bi bi-trash3-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

                <!-- PAGINATION -->
                <nav id="paginationLinks">
                    {{ $locations->links() }}
                </nav>

            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        function fetchLocations(page = 1) {

            $.ajax({
                url: "{{ route('ajax-locations') }}?page=" + page,
                type: "GET",
                data: {
                    searchText: $('#searchText').val(),
                    statusFilter: $('#statusFilter').val()
                },
                success: function (response) {

                    let rows = "";

                    if (response.locations.data.length > 0) {
                        response.locations.data.forEach((location, index) => {

                            rows += `
                    <tr>
                        <td>${((response.locations.current_page - 1) * response.locations.per_page) + (index + 1)}</td>
                        <td>${location.location_name}</td>
                        <td>${location.state}</td>
                        <td>${location.pincode ?? '-'}</td>

                        <td>${location.status == 1
                                    ? '<span class="badge-active">Active</span>'
                                    : '<span class="badge-inactive">Inactive</span>'}
                        </td>

                        <td class="table-actions">
                            <a href="/admin/edit-location/${location.id}" class="edit">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <a href="#" class="delete" onclick="confirmDelete(${location.id})">
                                <i class="bi bi-trash3-fill"></i>
                            </a>
                        </td>
                    </tr>
                `;
                        });
                    } else {
                        rows = `<tr><td colspan="6" class="text-center text-muted">No locations found</td></tr>`;
                    }

                    $('#locationTableBody').html(rows);
                    $('#paginationLinks').html(response.pagination);
                }
            });
        }

        $('#searchText').on('keyup', function () {
            fetchLocations();
        });

        $('#statusFilter').on('change', function () {
            fetchLocations();
        });

        $('#filterForm').on('submit', function (e) {
            e.preventDefault();
            fetchLocations();
        });

        $('#refreshBtn').on('click', function () {
            $('#filterForm')[0].reset();
            fetchLocations();
        });

        $(document).on('click', '#paginationLinks a', function (e) {
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            fetchLocations(page);
        });

        // Load initial data
        fetchLocations();

        function confirmDelete(id) {
            if (!confirm("Are you sure?")) return;
            window.location.href = "/admin/delete-location/" + id;
        }
    </script>

</body>

</html>