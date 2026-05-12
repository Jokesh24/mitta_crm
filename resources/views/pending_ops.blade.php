<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Pending OP Registrations - Rich Labz CRM</title>

    @include('includes.header_links')

    <style>
        .input-icon-search {
            position: relative;
        }

        .input-icon-search i {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon-search input {
            padding-left: 38px !important;
        }

        .badge-entry {
            background: #0d6efd;
            color: white;
            padding: 4px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
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
                <div class="crm-title">Pending OP Registrations</div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-back" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>
                </div>
            </div>

            <!-- SEARCH BAR -->
            <form id="filterForm" class="mb-3" autocomplete="off">
                <div class="row g-2">

                    <div class="col-md-4">
                        <label>Search</label>
                        <div class="input-icon-search">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchText" class="form-control" placeholder="Search Name or Mobile">
                        </div>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Submit</button>
                        <button type="button" id="refreshBtn" class="btn btn-light border">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>

                </div>
            </form>

            <!-- TABLE -->
            <div class="table-crm">
                <div class="table-responsive">

                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>SI.No</th>
                                <th>Patient</th>
                                <th>Mobile</th>
                                <th>Date</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Result</th>
                            </tr>
                        </thead>

                        <tbody id="opTableBody">

                            @foreach($ops as $index => $op)
                                <tr>
                                    <td>{{ ($ops->currentPage() - 1) * $ops->perPage() + ($index + 1) }}</td>
                                    <td>{{ $op->patient->patient_name }}</td>
                                    <td>{{ $op->patient->mobile_number }}</td>
                                    <td>{{ $op->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $op->patient->age }}</td>
                                    <td>{{ $op->patient->gender }}</td>

                                    <td>
                                        <span onclick="window.location.href='{{ route('add-result-from-op', $op->id) }}'"
                                            class="badge-status badge-entry">
                                            Enter Results
                                        </span>

                                    </td>
                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                    <!-- PAGINATION -->
                    <nav id="paginationLinks">
                        {{ $ops->links() }}
                    </nav>

                </div>
            </div>

        </div>
    </div>

    @include('includes.footer_links')


    <!-- ========================= AJAX SEARCH ========================= -->
    <!-- ========================= AJAX SEARCH ========================= -->
    <script>
        $(function () {

            // Submit search
            $("#filterForm").on("submit", function (e) {
                e.preventDefault();
                fetchPendingOps();
            });

            // Live typing triggers search
            $("#searchText").on("keyup", function () {
                fetchPendingOps();
            });

            // Refresh
            $("#refreshBtn").on("click", function () {
                $("#searchText").val("");
                fetchPendingOps();
            });

            // Pagination click
            $(document).on("click", "#paginationLinks a", function (e) {
                e.preventDefault();
                // Handle cases where the link has other query params
                const href = $(this).attr("href") || "";
                const pageMatch = href.match(/[?&]page=(\d+)/);
                const page = pageMatch ? pageMatch[1] : 1;
                fetchPendingOps(page);
            });

            // Load AJAX table
            function fetchPendingOps(page = 1) {
                $.ajax({
                    url: "{{ route('ajax.pending.ops') }}?page=" + page,
                    type: "GET",
                    data: {
                        searchText: $("#searchText").val()
                    },
                    success: function (res) {

                        // Build table rows
                        let rows = "";

                        if (res.ops && res.ops.data && res.ops.data.length > 0) {
                            res.ops.data.forEach(function (op, index) {

                                // Build enter-results URL dynamically using op.id
                                // This avoids blade interpolation problems when creating rows client-side
                                const enterUrl = "{{ url('admin/op') }}/" + op.id + "/enter-results";

                                rows += `
                                <tr>
                                    <td>${((res.ops.current_page - 1) * res.ops.per_page) + (index + 1)}</td>
                                    <td>${escapeHtml(op.patient.patient_name)}</td>
                                    <td>${escapeHtml(op.patient.mobile_number)}</td>
                                    <td>${formatDate(op.created_at)}</td>
                                    <td>${op.patient.age ?? '-'}</td>
                                    <td>${op.patient.gender ?? '-'}</td>
                                    <td>
                                        <span onclick="window.location.href='${enterUrl}'"
                                              class="badge-status badge-entry">
                                            Enter Results
                                        </span>
                                    </td>
                                </tr>
                            `;
                            });
                        } else {
                            rows = `<tr><td colspan="7" class="text-center text-muted">No pending OPs found.</td></tr>`;
                        }

                        $("#opTableBody").html(rows);
                        $("#paginationLinks").html(res.pagination || '');
                    },
                    error: function (xhr, status, err) {
                        console.error("AJAX error fetching pending ops:", err);
                    }
                });
            }

            // Basic date formatter (en-GB)
            function formatDate(dateStr) {
                if (!dateStr) return '';
                // Try to parse ISO date string safely
                const d = new Date(dateStr);
                if (isNaN(d)) return dateStr;
                return d.toLocaleDateString('en-GB');
            }

            // Small helper to avoid injecting raw HTML from server responses
            function escapeHtml(unsafe) {
                if (unsafe === null || unsafe === undefined) return '';
                return String(unsafe)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

        });
    </script>


</body>

</html>