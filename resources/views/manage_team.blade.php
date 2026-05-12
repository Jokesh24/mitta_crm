<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Manage Team - Rich Labz CRM</title>

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
            appearance: none;
        }

        .input-select i.dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            pointer-events: none;
            font-size: 16px;
            color: #6c757d;
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
                <div class="crm-title">Manage Team</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('add-team') }}" class="create-invoice-btn">
                        Add New Team Member
                    </a>
                </div>
            </div>

            <!-- FILTER SECTION -->
            <form class="neat-search-section mb-3" id="filterForm" autocomplete="off"
                style="display: flex; align-items: end; gap: 12px; flex-wrap: wrap;">

                <!-- SEARCH -->
                <div class="input-icon-search" style="flex: 1 1 200px;">
                    <label>Search</label>
                    <i class="bi bi-search"></i>
                    <input id="searchText" type="text" class="form-control form-control-sm"
                        placeholder="Name, Email, Phone">
                </div>

                <!-- ROLE -->
                <div class="input-select" style="flex: 1 1 150px;">
                    <label>Role</label>
                    <i class="bi bi-briefcase left-icon"></i>
                    <select id="roleFilter" class="form-control form-control-sm">
                        <option value="">All Roles</option>
                        <option value="1">Admin</option>
                        <option value="2">Lab Technician</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <!-- BRANCH -->
                <div class="input-select" style="flex: 1 1 150px;">
                    <label>Branch</label>
                    <i class="bi bi-building left-icon"></i>
                    <select id="branchFilter" class="form-control form-control-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->branch_name }}</option>
                        @endforeach
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>

                <!-- JOINED FROM -->
                <div class="input-icon-search" style="flex: 1 1 130px;">
                    <label>Joined From</label>
                    <i class="bi bi-calendar"></i>
                    <input id="dateFrom" type="text" class="form-control form-control-sm datepicker" placeholder="From"
                        autocomplete="off">
                </div>

                <!-- JOINED TO -->
                <div class="input-icon-search" style="flex: 1 1 130px;">
                    <label>Joined To</label>
                    <i class="bi bi-calendar"></i>
                    <input id="dateTo" type="text" class="form-control form-control-sm datepicker" placeholder="To"
                        autocomplete="off">
                </div>

                <!-- BUTTONS -->
                <div style="flex: 0 0 auto; display: flex; gap: 8px;">
                    <!-- <button type="submit" class="btn btn-primary btn-sm px-4">Submit</button> -->

                    <button type="button" id="refreshBtn" class="btn btn-sm btn-light border rounded-circle p-0"
                        style="width: 36px; height: 36px;" title="Refresh">
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
            <!-- TEAM TABLE -->
            <div class="table-crm">
                <div class="table-responsive">
                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr>
                                <th style="width: 60px;">SI.No</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Branch</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Date of Join</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="teamTableBody">
                            <!-- AJAX DATA LOADS HERE -->
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <div id="paginationLinks" class="mt-3"></div>
            </div>

        </div>
    </div>


    @include('includes.footer_links')

    <script>
        $(document).ready(function () {

            initDatePickers();
            loadTeam();

            function initDatePickers() {
                $(".datepicker").datepicker("destroy"); // clear old init if any
                $(".datepicker").datepicker({
                    dateFormat: "dd-mm-yy",
                    changeMonth: true,
                    changeYear: true,
                    onSelect: function () {
                        loadTeam(); // Trigger on date select
                    }
                });
            }

            function loadTeam(page = 1) {
                $.ajax({
                    url: "{{ route('team.list.ajax') }}?page=" + page,
                    type: "GET",
                    data: {
                        search: $("#searchText").val(),
                        role: $("#roleFilter").val(),
                        branch: $("#branchFilter").val(),
                        joined_from: $("#dateFrom").val(),
                        joined_to: $("#dateTo").val()
                    },
                    success: function (response) {
                        $("#teamTableBody").html(response.html);
                        $("#paginationLinks").html(response.pagination);

                        // re-init datepicker in case DOM refresh affects it
                        initDatePickers();
                    }
                });
            }

            // 🔎 Instant search
            $("#searchText").on("keyup", function () {
                loadTeam();
            });

            // 🧑 Role filter
            $("#roleFilter").on("change", function () {
                loadTeam();
            });

            // 🏢 Branch filter
            $("#branchFilter").on("change", function () {
                loadTeam();
            });

            // 🔄 Pagination click
            $(document).on("click", "#paginationLinks a", function (e) {
                e.preventDefault();
                let page = $(this).attr("href").split("page=")[1];
                loadTeam(page);
            });

            // 🔄 Refresh button
            $("#refreshBtn").on("click", function () {
                $("#filterForm")[0].reset();
                loadTeam();
            });

            // Make deleteTeam globally available
            window.deleteTeam = function (id) {
                if (!confirm("Are you sure you want to delete this member?")) return;

                $.ajax({
                    url: "/admin/delete_team/" + id,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function () {
                        loadTeam();
                    }
                });
            }

        });
    </script>


</body>

</html>