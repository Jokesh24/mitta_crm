<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage OP Registrations</title>

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

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Status Badges */
        .badge-entry {
            background: #0d6efd;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        .badge-completed {
            background: #198754;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
        }

        /* Action icons */
        .table-actions i {
            font-size: 16px;
            margin-right: 6px;
            cursor: pointer;
        }

        .view {
            color: #0d6efd;
        }

        .edit {
            color: #198754;
        }
    </style>
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <!-- HEADER -->
            <div class="crm-header">
                <div class="crm-title">Manage OP Registrations</div>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-back" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-op') }}" class="create-invoice-btn">Add New OP</a>
                </div>
            </div>

            <!-- FILTER BAR -->
            <form class="neat-search-section mb-3" id="filterForm"
                style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;">

                <div class="input-icon-search" style="flex:1 1 240px;">
                    <label>Search</label>
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchText" class="form-control form-control-sm"
                        placeholder="Patient Name, Mobile, Company">
                </div>

                <div class="input-icon-search" style="flex:1 1 150px;">
                    <label>Date From</label>
                    <i class="bi bi-calendar"></i>
                    <input type="text" id="dateFrom" class="form-control form-control-sm datepicker" placeholder="From">
                </div>

                <div class="input-icon-search" style="flex:1 1 150px;">
                    <label>Date To</label>
                    <i class="bi bi-calendar"></i>
                    <input type="text" id="dateTo" class="form-control form-control-sm datepicker" placeholder="To">
                </div>

                <!-- <div style="flex:0 0 auto; display:flex; gap:8px;">
                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;">
                        <i class="bi bi-arrow-clockwise"></i>
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

            <!-- TABLE SECTION -->
            <div class="table-crm">
                <div class="table-responsive">
                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr style="text-align:center">
                                <th>SI.No</th>
                                <th>Patient ID</th>
                                <th>OP Reg No</th>
                                <th>Patient Name</th>
                                <th>Mobile</th>
                                <th>Date</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Company</th>
                                <th>Payment Ref</th>
                                <!-- <th>Status</th> -->
                                <th>Location</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody id="opTableBody">
                            @foreach($ops as $index => $op)
                                <tr>
                                    <td>{{ ($ops->currentPage() - 1) * $ops->perPage() + ($index + 1) }}</td>
                                    <td>{{ $op->patient_code }}</td>
                                    <td>{{ $op->op_reg_no }}</td>
                                    <td>{{ $op->patient->patient_name ?? '-' }}</td>
                                    <td>{{ $op->patient->mobile_number ?? '-' }}</td>
                                    <td>{{ $op->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $op->patient->age ?? '-' }}</td>
                                    <td>{{ $op->patient->gender ?? '-' }}</td>
                                    <td>{{ $op->company->company_name ?? '-' }}</td>
                                    <td>{{ $op->payment_ref }}</td>

                                    <!-- <td>
                                                                                @if($op->status == 1)
                                                                                    <span class="badge-completed">Completed</span>
                                                                                @else
                                                                                    <span class="badge-entry">Pending</span>
                                                                                @endif
                                                                            </td> -->
                                    <td>{{ $op->location ?? 'N/A' }}</td>

                                    <td class="table-actions">
                                        <!-- <a href="#" class="view"><i class="bi bi-eye-fill"></i></a> -->
                                        <a href="{{ url('/admin/edit-op/' . $op->id) }}" class="edit"><i
                                                class="bi bi-pencil-fill"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <!-- PAGINATION -->
                <nav id="paginationLinks">
                    {{ $ops->links() }}
                </nav>
            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        $(function () {

            $(".datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true
            });

            function fetchOps(page = 1) {
                $.ajax({
                    url: "{{ route('ajax-op') }}?page=" + page,
                    type: "GET",
                    data: {
                        searchText: $('#searchText').val(),
                        dateFrom: $('#dateFrom').val(),
                        dateTo: $('#dateTo').val()
                    },
                    success: function (res) {

                        let rows = "";

                        if (res.ops.data.length > 0) {

                            res.ops.data.forEach((op, index) => {
                                rows += `
<tr>
    <td>${((res.ops.current_page - 1) * res.ops.per_page) + (index + 1)}</td>
    <td>${op.patient_code ?? "-"}</td>
    <td>${op.op_reg_no ?? "-"}</td>
    <td>${op.patient?.patient_name ?? "-"}</td>
    <td>${op.patient?.mobile_number ?? "-"}</td>
    <td>${op.created_at_format}</td>
    <td>${op.patient?.age ?? "-"}</td>
    <td>${op.patient?.gender ?? "-"}</td>
    <td>${op.company?.company_name ?? "-"}</td>
    <td>${op.payment_ref ?? "-"}</td>
    <td>${op.location ?? "N/A"}</td>

    <td class="table-actions">
        <a href="${op.edit_url}" class="edit">
            <i class="bi bi-pencil-fill"></i>
        </a>
    </td>
</tr>
`;


                            });

                        } else {
                            rows = `<tr><td colspan="12" class="text-center text-muted">No records found</td></tr>`;
                        }

                        $('#opTableBody').html(rows);
                        $('#paginationLinks').html(res.pagination);
                    }
                });
            }

            // Live search
            $('#searchText').on('keyup', function () {
                fetchOps();
            });

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                fetchOps();
            });

            $('#refreshBtn').on('click', function () {
                $('#filterForm')[0].reset();
                fetchOps();
            });

            // 📅 Date change triggers search (IMPORTANT)
            $("#dateFrom, #dateTo").on("change", function () {
                fetchOps();
            });

            $(document).on('click', '#paginationLinks a', function (e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetchOps(page);
            });

        });
    </script>

</body>

</html>