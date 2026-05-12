<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Test Results - Rich Labz CRM</title>

    @include('includes.header_links')

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <style>
        /* LEFT ICON */
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
            pointer-events: none;
        }

        /* BADGES */
        .badge-sent {
            background: #25D366 !important;
            color: #fff;
            padding: 4px 10px;
            border-radius: 8px;
        }

        .badge-not-sent {
            background: #6c757d !important;
            color: #fff;
            padding: 4px 10px;
            border-radius: 8px;
        }

        /* EXPORT BUTTON */
        .btn-excel {
            background: #1f8f45;
            color: #fff;
            border-radius: 6px;
            font-size: 13px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            gap: 6px;
            border: none;
        }

        .btn-excel:hover {
            background: #187a39;
        }
    </style>
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Manage Test Results</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-pending_ops') }}" class="create-invoice-btn">
                        Add New Result
                    </a>
                </div>
            </div>

            <form class="neat-search-section mb-3" id="filterForm" autocomplete="off">

                <div class="input-icon-search" style="flex:1 1 200px;">
                    <label>Search (Name / Mobile)</label>
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchText" class="form-control form-control-sm"
                        placeholder="Patient name or mobile">
                </div>

                <div class="input-select" style="flex:1 1 140px;">
                    <label>Location</label>
                    <i class="bi bi-geo-alt left-icon"></i>
                    <select id="searchLocation" class="form-select form-select-sm">
                        <option value="">All Locations</option>
                        @if(isset($locations) && count($locations) > 0)
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
                            @endforeach
                        @endif
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                {{-- NEW PACKAGE FILTER DROPDOWN --}}
                <div class="input-select" style="flex:1 1 180px;">
                    <label>Package</label>
                    <i class="bi bi-box-seam left-icon"></i>
                    <select id="searchPackage" class="form-select form-select-sm">
                        <option value="">All Packages</option>
                        @if(isset($packages) && count($packages) > 0)
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->package_name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                {{-- Find your filterForm and add this block inside --}}
                <div class="input-select" style="flex:1 1 180px;">
                    <label>Team Member</label>
                    <i class="bi bi-people left-icon"></i>
                    <select id="searchTeam" class="form-select form-select-sm">
                        <option value="">All Team</option>
                        @if(isset($teams) && count($teams) > 0)
                            @foreach($teams as $member)
                                <option value="{{ $member->id }}">{{ $member->first_name }} {{ $member->last_name }}
                                    ({{ $member->emp_code }})</option>
                            @endforeach
                        @endif
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <div class="input-icon-search" style="flex:1 1 120px;">
                    <label>Date From</label>
                    <i class="bi bi-calendar"></i>
                    <input type="text" id="dateFrom" class="form-control form-control-sm datepicker" placeholder="From">
                </div>

                <div class="input-icon-search" style="flex:1 1 120px;">
                    <label>Date To</label>
                    <i class="bi bi-calendar"></i>
                    <input type="text" id="dateTo" class="form-control form-control-sm datepicker" placeholder="To">
                </div>

                <div style="flex:0 0 auto;display:flex;gap:8px;align-items:center;">

                    <button type="button" id="openSidebarBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;" title="Advanced Filters">
                        <i class="bi bi-sliders"></i>
                    </button>

                    <button type="button" id="exportExcel" class="btn-excel">
                        <i class="bi bi-file-earmark-excel"></i> Export Excel
                    </button>

                    <button type="button" id="exportPdfZip" class="btn-excel" style="background:#d9534f;">
                        <i class="bi bi-file-zip-fill"></i> Export PDF ZIP
                    </button>

                    <button type="button" id="sendWhatsappBtn" class="btn-excel" style="background:#25D366;">
                        <i class="bi bi-whatsapp"></i> Send WA Report
                    </button>

                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;">
                        <i class="bi bi-arrow-clockwise"></i>
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

            <div class="table-crm">
                <div class="table-responsive">

                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr>
                                <th style="width:60px;">SI.No</th>
                                <th>Patient Name</th>
                                <th>Mobile</th>
                                <th>Date</th>
                                <th>WhatsApp Status</th>
                                <th style="width:170px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="resultTableBody">
                            @foreach($results as $index => $r)
                                <tr class="dynamic-row">
                                    <td>{{ $results->firstItem() + $index }}</td>
                                    <td>{{ $r->patient->patient_name ?? 'N/A' }}</td>
                                    <td>{{ $r->mobile_number }}</td>
                                    <td>{{ $r->created_at ? $r->created_at->format('d-m-Y') : '' }}</td>
                                    <td>
                                        @if($r->whatsapp_sent == 1)
                                            <span class="badge-sent">Sent</span>
                                        @else
                                            <span class="badge-not-sent">Not Sent</span>
                                        @endif
                                    </td>
                                    <td class="table-actions">
                                        <a href="{{ route('view-result', $r->id) }}" class="text-primary" title="View">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>

                                        <a href="{{ route('edit-result-from-op', $r->id) }}" class="text-primary ms-2"
                                            title="Edit PDF">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                        </a>

                                        @if($r->whatsapp_sent == 1)
                                            <a href="javascript:void(0);" class="text-warning send-single-wa ms-2"
                                                data-id="{{ $r->id }}" title="Resend WhatsApp Report">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        @else
                                            <a href="javascript:void(0);" class="text-success send-single-wa ms-2"
                                                data-id="{{ $r->id }}" title="Send WhatsApp Report">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        @endif

                                        <a href="javascript:void(0);" class="text-info send-sms-btn ms-2"
                                            data-id="{{ $r->id }}" title="Send SMS Report Link">
                                            <i class="bi bi-chat-left-text-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>

                </div>

                <nav id="paginationLinks">
                    {{ $results->links() }}
                </nav>

            </div>

        </div>
    </div>

    {{-- ADVANCED FILTER SIDEBAR --}}
    <div id="filterSidebar" class="position-fixed top-0 end-0 h-100 bg-white shadow-lg"
        style="width:380px;transform:translateX(100%);transition:0.3s;z-index:1055;">

        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Advanced Filters</h5>
            <button type="button" id="closeSidebar" class="btn-close"></button>
        </div>

        <div class="p-4">
            <form id="sidebarFilterForm">

                <div class="input-select mb-3">
                    <label class="form-label">Company</label>
                    <i class="bi bi-buildings left-icon"></i>
                    <select class="form-select form-select-sm">
                        <option value="">All Companies</option>
                        <option>Dr.Cita</option>
                        <option>Mitta Excellence</option>
                        <option>Apollo Diagnostics</option>
                        <option>Vijaya Diagnostics</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <div class="input-select mb-3">
                    <label class="form-label">Location</label>
                    <i class="bi bi-geo-alt left-icon"></i>
                    <select class="form-select form-select-sm">
                        <option value="">All Locations</option>
                        <option>Kukatpally</option>
                        <option>Ameerpet</option>
                        <option>Madhapur</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <div class="input-select mb-3">
                    <label class="form-label">Branch</label>
                    <i class="bi bi-building left-icon"></i>
                    <select class="form-select form-select-sm">
                        <option value="">All Branches</option>
                        <option>Main Branch</option>
                        <option>City Branch</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <div class="input-icon-search mb-3">
                    <label class="form-label">Employee Code</label>
                    <i class="bi bi-person-vcard"></i>
                    <input type="text" class="form-control form-control-sm" placeholder="Enter Employee Code">
                </div>

                <div class="input-select mb-3">
                    <label class="form-label">Status</label>
                    <i class="bi bi-check-circle left-icon"></i>
                    <select class="form-select form-select-sm">
                        <option value="">All</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" id="clearAdvancedBtn" class="btn btn-outline-secondary btn-sm flex-fill">
                        Clear
                    </button>
                    <button type="button" id="applyAdvancedBtn" class="btn btn-primary btn-sm flex-fill">
                        Apply
                    </button>
                </div>

            </form>
        </div>
    </div>

    <div id="sidebarOverlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark"
        style="opacity:0;visibility:hidden;transition:0.3s;z-index:1050;"></div>

    @include('includes.footer_links')

    <script>
        function formatDate(dateString) {
            if (!dateString) return '';
            let d = new Date(dateString);
            if (isNaN(d.getTime())) return '';
            let day = String(d.getDate()).padStart(2, '0');
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let year = d.getFullYear();
            return `${day}-${month}-${year}`;
        }

        // --- FETCH RESULTS (AJAX) ---
        function fetchResults(page = 1) {
            let search = $('#searchText').val().trim();
            let location = $('#searchLocation').val();
            let packageId = $('#searchPackage').val();
            let teamId = $('#searchTeam').val();
            let dateFrom = $('#dateFrom').val().trim();
            let dateTo = $('#dateTo').val().trim();

            $.ajax({
                url: "{{ route('ajax-results') }}?page=" + page,
                type: "GET",
                data: {
                    search: search,
                    location: location,
                    package_id: packageId,
                    team_id: teamId,
                    date_from: dateFrom,
                    date_to: dateTo
                },
                success: function (res) {
                    $('#resultTableBody .dynamic-row').remove();
                    let rows = '';

                    if (res.results.data.length > 0) {
                        let baseIndex = res.results.from ?? ((res.results.current_page - 1) * res.results.per_page + 1);
                        let viewUrlBase = "{{ url('admin/view_result') }}";
                        let editUrlBase = "{{ url('admin/edit_result') }}";

                        res.results.data.forEach(function (r, i) {
                            let si = baseIndex + i;
                            let badge = (r.whatsapp_sent == 1)
                                ? '<span class="badge-sent">Sent</span>'
                                : '<span class="badge-not-sent">Not Sent</span>';

                            let patientName = r.patient ? r.patient.patient_name : 'N/A';

                            // WhatsApp Logic
                            let waAction = (r.whatsapp_sent == 1)
                                ? `<a href="javascript:void(0);" class="text-warning send-single-wa ms-2" data-id="${r.id}" title="Resend WhatsApp Report"><i class="bi bi-whatsapp"></i></a>`
                                : `<a href="javascript:void(0);" class="text-success send-single-wa ms-2" data-id="${r.id}" title="Send WhatsApp Report"><i class="bi bi-whatsapp"></i></a>`;

                            // NEW: SMS Action Logic (matches your requirement for a secure link)
                            let smsAction = `<a href="javascript:void(0);" class="text-info send-sms-btn ms-2" data-id="${r.id}" title="Send SMS Report Link">
                                                <i class="bi bi-chat-left-text-fill"></i>
                                             </a>`;

                            rows += `
                                <tr class="dynamic-row">
                                    <td>${si}</td>
                                    <td>${patientName}</td>
                                    <td>${r.mobile_number}</td>
                                    <td>${formatDate(r.created_at)}</td>
                                    <td>${badge}</td>
                                    <td class="table-actions">
                                        <a href="${viewUrlBase}/${r.id}" class="text-primary" title="View"><i class="bi bi-eye-fill"></i></a>
                                        <a href="${editUrlBase}/${r.id}" class="text-dark ms-2" title="Edit Result"><i class="bi bi-pencil-square"></i></a>
                                        ${waAction}
                                        ${smsAction}
                                    </td>
                                </tr>`;
                        });
                    } else {
                        rows = `<tr class="dynamic-row"><td colspan="6" class="text-center text-muted">No results found</td></tr>`;
                    }

                    $('#resultTableBody').append(rows);
                    $('#paginationLinks').html(res.pagination);
                }
            });
        }

        // --- SEND SINGLE WHATSAPP ---
        $(document).on('click', '.send-single-wa', function () {
            let resultId = $(this).data('id');
            let $btn = $(this);
            let originalHtml = $btn.html();

            if (!confirm("Do you want to send the WhatsApp report to this patient?")) return;

            $btn.html('<i class="bi bi-hourglass-split"></i>').css('pointer-events', 'none');

            $.ajax({
                url: "{{ route('send-single-whatsapp') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", id: resultId },
                success: function (res) {
                    if (res.status === 'success') {
                        let currentPage = $('#paginationLinks .active span').text() || 1;
                        fetchResults(currentPage);
                        alert(res.message);
                    } else {
                        alert(res.message);
                        $btn.html(originalHtml).css('pointer-events', 'auto');
                    }
                }
            });
        });

        // --- EXPORT HANDLERS ---
        $("#exportExcel").on("click", function () {
            let search = $('#searchText').val().trim();
            let location = $('#searchLocation').val();
            let packageId = $('#searchPackage').val(); // NEW
            let teamId = $('#searchTeam').val();
            let dateFrom = $('#dateFrom').val().trim();
            let dateTo = $('#dateTo').val().trim();

            window.location.href = "{{ route('export-results') }}?search=" + encodeURIComponent(search)
                + "&location=" + encodeURIComponent(location)
                + "&package_id=" + encodeURIComponent(packageId)
                + "&team_id=" + encodeURIComponent(teamId)
                + "&date_from=" + encodeURIComponent(dateFrom)
                + "&date_to=" + encodeURIComponent(dateTo);
        });

        $("#exportPdfZip").on("click", function () {
            let search = $('#searchText').val().trim();
            let location = $('#searchLocation').val();
            let packageId = $('#searchPackage').val(); // NEW
            let teamId = $('#searchTeam').val();
            let dateFrom = $('#dateFrom').val().trim();
            let dateTo = $('#dateTo').val().trim();

            window.location.href = "{{ route('export-results-pdf-zip') }}?search=" + encodeURIComponent(search)
                + "&location=" + encodeURIComponent(location)
                + "&package_id=" + encodeURIComponent(packageId)
                + "&team_id=" + encodeURIComponent(teamId)
                + "&date_from=" + encodeURIComponent(dateFrom)
                + "&date_to=" + encodeURIComponent(dateTo);
        });

        // --- BULK WHATSAPP ---
        $("#sendWhatsappBtn").on("click", function () {
            let location = $('#searchLocation').val();
            if (!location) { alert("Please select a Location first."); return; }
            if (!confirm("Send reports to all pending patients in " + location + "?")) return;

            let $btn = $(this);
            let originalText = $btn.html();
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Sending...');

            $.ajax({
                url: "{{ route('send-bulk-whatsapp') }}",
                type: "POST",
                data: { _token: "{{ csrf_token() }}", location: location },
                success: function (res) {
                    alert(res.message);
                    fetchResults(1);
                },
                complete: function () { $btn.prop('disabled', false).html(originalText); }
            });
        });

        // --- SEND SMS REPORT ---
        $(document).on('click', '.send-sms-btn', function () {
            let resultId = $(this).data('id');
            let $btn = $(this);
            let originalHtml = $btn.html();

            if (!confirm("Generate secure link and send SMS to patient?")) return;

            $btn.html('<i class="bi bi-hourglass-split"></i>').css('pointer-events', 'none');

            $.ajax({
                url: "{{ route('send-sms-report') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: resultId
                },
                success: function (res) {
                    if (res.status === 'success') {
                        alert(res.message + "\n\nGenerated Link: " + res.preview_link);
                        console.log("SMS Link: ", res.preview_link);
                    } else {
                        alert(res.message);
                    }
                },
                error: function () {
                    alert("System error while triggering SMS.");
                },
                complete: function () {
                    $btn.html(originalHtml).css('pointer-events', 'auto');
                }
            });
        });

        $(function () {
            $(".datepicker").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true });
            $("#dateFrom, #dateTo, #searchLocation, #searchPackage, #searchTeam").on("change", function () { fetchResults(1); });
            $('#searchText').on('keyup', function () { fetchResults(1); });

            $("#refreshBtn").on("click", function () {
                $("#filterForm")[0].reset();
                fetchResults(1);
            });

            $(document).on('click', '#paginationLinks a', function (e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1] || 1;
                fetchResults(page);
            });

            $("#openSidebarBtn").on("click", function () {
                $("#filterSidebar").css("transform", "translateX(0)");
                $("#sidebarOverlay").css({ opacity: 0.5, visibility: "visible" });
            });

            $("#closeSidebar, #sidebarOverlay").on("click", function () {
                $("#filterSidebar").css("transform", "translateX(100%)");
                $("#sidebarOverlay").css({ opacity: 0, visibility: "hidden" });
            });

            fetchResults(1);
        });
    </script>
</body>

</html>