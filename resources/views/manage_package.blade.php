<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Packages - Rich Labz CRM</title>

    @include('includes.header_links')


    <style>
        /* LEFT ICON (for inputs & selects) */
        .input-icon-search i,
        .input-select i.left-icon {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* TEXT INPUT */
        .input-icon-search input {
            padding-left: 38px !important;
        }

        /* SELECT WRAPPER */
        .input-select {
            position: relative;
        }

        /* SELECT FIELD */
        .input-select select {
            padding-left: 38px !important;
            padding-right: 38px !important;
            background-color: #f9f9fb;
            appearance: none;
        }

        /* RIGHT DROPDOWN ICON */
        .input-select i.dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #6c757d;
            pointer-events: none;
        }

        /* Keep table single line */
        .table-crm table th,
        .table-crm table td {
            white-space: nowrap !important;
        }

        /* CLASSY MODAL DESIGN */
        .classy-modal {
            border-radius: 18px;
            padding: 10px 20px 20px;
            backdrop-filter: blur(8px);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
            animation: fadeInModal 0.25s ease-out;
        }

        @keyframes fadeInModal {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .soft-divider {
            border: 0;
            height: 1px;
            background: #e9ecef;
            margin: 25px 0;
        }

        /* PRICE BADGE */
        .info-badge {
            background: #eef5ff;
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 16px;
            font-weight: 600;
            color: #0d63d9;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* TEST TAGS */
        .test-pill-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .test-pill-container .pill {
            background: #f1f8ff;
            border: 1px solid #cce4ff;
            padding: 7px 14px;
            border-radius: 25px;
            font-size: 14px;
            color: #0d6efd;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
        }

        .test-pill-container .pill:hover {
            background: #e0f0ff;
            transform: translateY(-2px);
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
                <div class="crm-title">Manage Packages</div>

                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-package') }}" class="create-invoice-btn">
                        Add New Package
                    </a>
                </div>
            </div>

            <!-- FILTER BAR -->
            <form class="neat-search-section mb-3" autocomplete="off" id="filterForm"
                style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;">

                <!-- Search -->
                <div class="input-icon-search" style="flex:1 1 200px;">
                    <label>Search</label>
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchText" class="form-control form-control-sm"
                        placeholder="Search package name, tests, price">
                </div>

                <!-- Test Count Filter -->
                <div class="input-icon-search" style="flex:1 1 150px;">
                    <label>Minimum Tests</label>
                    <i class="bi bi-layers-half"></i>
                    <input type="number" id="testCountFilter" class="form-control form-control-sm"
                        placeholder="e.g. 10">
                </div>

                <!-- Status -->
                <div class="input-select" style="flex:1 1 150px;">
                    <label>Status</label>
                    <i class="bi bi-check-circle left-icon"></i>
                    <select id="statusFilter" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option>Active</option>
                        <option>Inactive</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <!-- Buttons -->
                <div style="display:flex;gap:8px;align-items:center;">
                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;" title="Refresh">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>

                    <!-- <button type="button" id="openSidebarBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width:36px;height:36px;" title="Advanced Filters">
                        <i class="bi bi-sliders"></i>
                    </button> -->

                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        Submit
                    </button>
                </div>

            </form>

            <!-- PACKAGES TABLE -->
            <div class="table-crm">
                <div class="table-responsive">

                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr>
                                <th style="width:60px;">SI.No</th>
                                <th>Package Name</th>
                                <th>Total Tests</th>
                                <th>Price (₹)</th>
                                <th>Status</th>
                                <th style="width:170px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody id="packageTableBody">
                            @foreach($packages as $index => $p)
                                <tr>
                                    <td>{{ ($packages->currentPage() - 1) * $packages->perPage() + ($index + 1) }}</td>
                                    <td>{{ $p->package_name }}</td>
                                    <td>
                                        @php
                                            $testCount = 0;

                                            if (!empty($p->parameters)) {
                                                // NEW STRUCTURE
                                                $testCount += count($p->parameters['global'] ?? []);
                                                foreach ($p->parameters['groups'] ?? [] as $g) {
                                                    $testCount += count($g['params'] ?? []);
                                                }
                                            } elseif (!empty($p->tests)) {
                                                // OLD STRUCTURE
                                                $decodedTests = is_array($p->tests) ? $p->tests : json_decode($p->tests, true);
                                                $testCount = is_array($decodedTests) ? count($decodedTests) : 0;
                                            }
                                        @endphp

                                        {{ $testCount }}
                                    </td>
                                    </td>
                                    <td>{{ $p->package_price }}</td>

                                    <td>
                                        @if($p->status == 1)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="table-actions">
                                        <a href="javascript:void(0)" onclick="viewPackage({{ $p->id }})"
                                            class="action-btn view">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <a href="{{ route('edit-package', $p->id) }}" class="action-btn edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>


                    </table>



                </div>

                <!-- PACKAGE DETAILS MODAL -->
                <div class="modal fade" id="packageModal" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content classy-modal">

                            <div class="modal-header border-0">
                                <h4 class="modal-title fw-bold text-primary">
                                    <i class="bi bi-box-seam me-2"></i> Package Details
                                </h4>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <!-- Package Title -->
                                <h2 id="pkg_name" class="fw-bold mb-2 text-dark"></h2>

                                <div class="d-flex gap-4 mb-4 align-items-center">
                                    <div class="info-badge">
                                        <i class="bi bi-currency-rupee"></i>
                                        <span id="pkg_price"></span>
                                    </div>

                                    <div id="pkg_status"></div>
                                </div>

                                <hr class="soft-divider">

                                <!-- Tests Heading -->
                                <h5 class="fw-semibold mb-3">
                                    <i class="bi bi-layers-half me-2 text-primary"></i>Tests Included
                                </h5>

                                <!-- Test Pills -->
                                <div id="pkg_tests" class="test-pill-container"></div>

                            </div>

                            <div class="modal-footer border-0 justify-content-end">
                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                    Close
                                </button>
                            </div>

                        </div>
                    </div>
                </div>



                <!-- PAGINATION -->
                <nav id="paginationLinks">
                    {{ $packages->links() }}
                </nav>


            </div>

        </div>
    </div>


    @include('includes.footer_links')



</body>

</html>


<script>
    $(function () {

        function fetchPackages(page = 1) {
            $.ajax({
                url: "{{ route('ajax-packages') }}?page=" + page,
                type: "GET",
                data: {
                    searchText: $('#searchText').val(),
                    minTests: $('#testCountFilter').val(),
                    statusFilter: $('#statusFilter').val()
                },
                success: function (res) {

                    let rows = "";

                    if (res.packages.data.length > 0) {
                        res.packages.data.forEach((p, index) => {
                            let testCount = 0;

                            if (p.parameters) {
                                if (p.parameters.global) {
                                    testCount += p.parameters.global.length;
                                }
                                if (p.parameters.groups) {
                                    p.parameters.groups.forEach(g => {
                                        testCount += g.params ? g.params.length : 0;
                                    });
                                }
                            } else if (p.tests) {
                                try {
                                    const t = Array.isArray(p.tests) ? p.tests : JSON.parse(p.tests);
                                    testCount = t.length;
                                } catch (e) {
                                    testCount = 0;
                                }
                            }

                            rows += `
                            <tr>
                                <td>${((res.packages.current_page - 1) * res.packages.per_page) + (index + 1)}</td>
                                <td>${p.package_name}</td>
                                <td>${testCount}</td>
                                <td>${p.package_price}</td>
                                <td>${p.status == 1
                                    ? '<span class="badge bg-success">Active</span>'
                                    : '<span class="badge bg-danger">Inactive</span>'
                                }</td>
                                <td class="table-actions">
                                    <a href="#" class="action-btn view"><i class="bi bi-eye-fill"></i></a>
                                    <a href="/admin/edit-package/${p.id}" class="action-btn edit"><i class="bi bi-pencil-fill"></i></a>
                                </td>
                            </tr>
                        `;
                        });
                    } else {
                        rows = `<tr><td colspan="6" class="text-center text-muted">No packages found</td></tr>`;
                    }

                    $('#packageTableBody').html(rows);
                    $('#paginationLinks').html(res.pagination);
                }
            });
        }

        // Live Search
        $('#searchText, #testCountFilter, #statusFilter').on("keyup change", function () {
            fetchPackages();
        });

        // Pagination Click
        $(document).on("click", "#paginationLinks a", function (e) {
            e.preventDefault();
            let page = $(this).attr("href").split("page=")[1];
            fetchPackages(page);
        });

        // Refresh button resets filters
        $("#refreshBtn").on("click", function () {
            $('#searchText').val('');
            $('#testCountFilter').val('');
            $('#statusFilter').val('');
            fetchPackages();
        });

    });
</script>

<script>
    function viewPackage(id) {
        let fetchUrl = "{{ route('package-details', ['id' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', id);
        $.ajax({
            url: fetchUrl,
            type: "GET",
            success: function (res) {

                $("#pkg_name").text(res.package.package_name);
                $("#pkg_price").text(res.package.package_price);

                $("#pkg_status").html(
                    res.package.status == 1
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>'
                );

                const parameters = res.parameters;
                const tests = res.tests;

                $("#pkg_tests").html("");

                let order = 1;

                /* ---------- NEW STRUCTURE ---------- */
                if (parameters) {

                    if (parameters.global && parameters.global.length > 0) {

                        $("#pkg_tests").append(`
                        <div style="width:100%;font-weight:600;color:#0d6efd;margin-bottom:8px;">
                            General Parameters
                        </div>
                    `);

                        parameters.global.forEach(pid => {
                            $("#pkg_tests").append(`
                            <div class="pill">
                                <strong>${order++}.</strong> ${tests[pid] ?? 'Unknown'}
                            </div>
                        `);
                        });
                    }

                    if (parameters.groups && parameters.groups.length > 0) {

                        parameters.groups.forEach(group => {

                            if (group.params && group.params.length > 0) {

                                $("#pkg_tests").append(`
                                <div style="width:100%;font-weight:600;color:#6610f2;margin:15px 0 8px;">
                                    ${group.group_name}
                                </div>
                            `);

                                group.params.forEach(pid => {
                                    $("#pkg_tests").append(`
                                    <div class="pill">
                                        <strong>${order++}.</strong> ${tests[pid] ?? 'Unknown'}
                                    </div>
                                `);
                                });
                            }
                        });
                    }

                }
                /* ---------- LEGACY STRUCTURE ---------- */
                else {

                    $("#pkg_tests").append(`
                    <div style="width:100%;font-weight:600;color:#0d6efd;margin-bottom:8px;">
                        Tests
                    </div>
                `);

                    Object.values(tests).forEach(name => {
                        $("#pkg_tests").append(`
                        <div class="pill">
                            <strong>${order++}.</strong> ${name}
                        </div>
                    `);
                    });
                }

                $("#packageModal").modal("show");
            }
        });
    }
</script>