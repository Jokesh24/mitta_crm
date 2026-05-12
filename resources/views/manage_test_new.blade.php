<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manage Tests</title>

    @include('includes.header_links')

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <style>
        .input-icon-search i { position: absolute; left: 12px; top: 70%; transform: translateY(-50%); color: #6c757d; }
        .input-icon-search input { padding-left: 38px !important; }
        .input-select { position: relative; }
        .input-select select { padding-left: 38px !important; background-color: #f9f9fb; appearance: none; }
        .input-select i.left-icon { position: absolute; left: 12px; top: 70%; transform: translateY(-50%); color: #6c757d; }
        .input-select i.dropdown-icon { position: absolute; right: 12px; top: 70%; transform: translateY(-50%); color: #6c757d; pointer-events: none; }

        .classy-modal { border-radius: 18px; padding: 20px; background: rgba(255, 255, 255, 0.95); box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15); }
        .param-list-grid { display: flex; flex-direction: column; gap: 10px; }
        .view-group-header { font-weight: bold; color: #0056b3; font-size: 13px; margin-top: 10px; border-bottom: 1px solid #eee; padding-bottom: 3px; }
        .param-item { background: #f8f9fa; border: 1px solid #dee2e6; padding: 6px 12px; border-radius: 6px; font-size: 14px; color: #495057; display: inline-block; margin-right: 5px; }

        .sortable-list { list-style: none; padding: 0; margin: 0; border: 1px solid #e9ecef; border-radius: 8px; max-height: 350px; overflow-y: auto; background: #fff; }
        .sortable-item { padding: 10px 15px; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center; background: #fff; cursor: grab; }
        .sortable-group-label { background: #e7f1ff; padding: 5px 15px; font-weight: bold; font-size: 12px; color: #0056b3; display: flex; justify-content: space-between; align-items: center; }
        .remove-btn { color: #dc3545; cursor: pointer; font-size: 18px; }
        .sortable-ghost { background: #e7f1ff; opacity: 0.8; }
    </style>
</head>

<body>
    @include('includes.sidebar')
    @include('includes.header')

    <select id="master_parameter_source" style="display:none;">
        @php $allParams = \App\Models\Test::where('status', 1)->orderBy('test_name')->get(); @endphp
        @foreach($allParams as $p)
            <option value="{{ $p->id }}">{{ $p->test_name }}</option>
        @endforeach
    </select>

    <div class="content">
        <div class="container-crm">
            <div class="crm-header">
                <div class="crm-title">Manage Tests</div>
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-back" onclick="history.back()"><i class="bi bi-arrow-left"></i> Back</button>
                    <a href="{{ route('add-test-new') }}" class="create-invoice-btn">Add New Test</a>
                </div>
            </div>

            <form class="neat-search-section mb-3" id="filterForm" style="display:flex;align-items:end;gap:12px;flex-wrap:wrap;">
                <div class="input-icon-search" style="flex:1 1 200px;">
                    <label>Search</label><i class="bi bi-search"></i>
                    <input type="text" id="searchText" class="form-control form-control-sm" placeholder="Search Test Name">
                </div>
                <div class="input-select" style="flex:1 1 150px;">
                    <label>Status</label><i class="bi bi-check-circle left-icon"></i>
                    <select id="statusFilter" class="form-control form-control-sm">
                        <option value="">All</option><option>Active</option><option>Inactive</option>
                    </select>
                    <i class="bi bi-chevron-down dropdown-icon"></i>
                </div>
                <button type="button" id="refreshBtn" class="btn btn-light border rounded-circle p-0" style="width:36px;height:36px;"><i class="bi bi-arrow-clockwise"></i></button>
            </form>

            <div class="table-crm">
                <div class="table-responsive">
                    <table class="table invoice-table align-middle mb-1">
                        <thead>
                            <tr>
                                <th style="width:60px;">SI.No</th>
                                <th>Test Name</th>
                                <th>Param Count</th>
                                <th>Price (₹)</th>
                                <th>Status</th>
                                <th style="width:170px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="testTableBody">
                            @foreach($tests as $index => $t)
                                <tr>
                                    <td>{{ ($tests->currentPage() - 1) * $tests->perPage() + ($index + 1) }}</td>
                                    <td>{{ $t->test_name }}</td>
                                    <td>
                                        @php 
                                            $json = json_decode($t->parameters, true);
                                            $total = count($json['global'] ?? []);
                                            foreach($json['groups'] ?? [] as $g) { $total += count($g['params']); }
                                            echo $total;
                                        @endphp
                                    </td>
                                    <td>{{ $t->price }}</td>
                                    <td>{!! $t->status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>' !!}</td>
                                    <td class="table-actions">
                                        <a href="javascript:void(0)" onclick="viewTest({{ $t->id }})" class="action-btn view"><i class="bi bi-eye-fill"></i></a>
                                        <a href="javascript:void(0)" onclick="editTest({{ $t->id }})" class="action-btn edit"><i class="bi bi-pencil-fill"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <nav id="paginationLinks">{{ $tests->links() }}</nav>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content classy-modal">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold text-primary" id="view_test_name"></h4>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <span class="badge bg-primary fs-6" id="view_price"></span>
                        <span id="view_status"></span>
                    </div>
                    <div id="view_params_container" class="param-list-grid"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content classy-modal">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title fw-bold text-primary">Edit Test Structure</h4>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-4">
                    <form id="editTestForm">
                        <input type="hidden" id="edit_test_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Test Name</label>
                                <input type="text" class="form-control" id="edit_test_name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Price</label>
                                <input type="number" class="form-control" id="edit_price" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted">Status</label>
                                <select class="form-select" id="edit_status">
                                    <option value="1">Active</option><option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 p-3 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="fw-bold mb-0">Structure Management</label>
                                <button type="button" class="btn btn-sm btn-dark" onclick="addNewGroupEdit()">+ Add Sub-Test Group</button>
                            </div>
                            <div id="edit_structure_container">
                                </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-2">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Update Test</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('includes.footer_links')

    <script>
        $(function () {
            // Handle table filters
            $('#searchText, #statusFilter').on("keyup change", function () { fetchTests(); });
            $(document).on("click", "#paginationLinks a", function (e) {
                e.preventDefault();
                fetchTests($(this).attr("href").split("page=")[1]);
            });
            $("#refreshBtn").click(function() { $('#searchText').val(''); $('#statusFilter').val(''); fetchTests(); });

            function fetchTests(page = 1) {
                $.ajax({
                    url: "{{ route('ajax-tests-new') }}?page=" + page,
                    type: "GET",
                    data: { searchText: $('#searchText').val(), statusFilter: $('#statusFilter').val() },
                    success: function (res) {
                        let rows = "";
                        if (res.tests.data.length > 0) {
                            res.tests.data.forEach((t, index) => {
                                let json = JSON.parse(t.parameters);
                                let pCount = (json.global ? json.global.length : 0);
                                if(json.groups) json.groups.forEach(g => pCount += g.params.length);

                                let statusBadge = t.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                                rows += `<tr>
                                    <td>${((res.tests.current_page - 1) * res.tests.per_page) + (index + 1)}</td>
                                    <td>${t.test_name}</td>
                                    <td>${pCount}</td>
                                    <td>${t.price}</td>
                                    <td>${statusBadge}</td>
                                    <td class="table-actions">
                                        <a href="javascript:void(0)" onclick="viewTest(${t.id})" class="action-btn view"><i class="bi bi-eye-fill"></i></a>
                                        <a href="javascript:void(0)" onclick="editTest(${t.id})" class="action-btn edit"><i class="bi bi-pencil-fill"></i></a>
                                    </td>
                                </tr>`;
                            });
                        } else { rows = `<tr><td colspan="6" class="text-center text-muted">No tests found</td></tr>`; }
                        $('#testTableBody').html(rows);
                        $('#paginationLinks').html(res.pagination);
                    }
                });
            }

            // Submit Edit Form
            $("#editTestForm").submit(function(e) {
                e.preventDefault();
                let structure = { global: [], groups: [] };

                // Get Global
                if(document.getElementById('edit_global_select').tomselect) {
                    structure.global = document.getElementById('edit_global_select').tomselect.getValue();
                }

                // Get Groups
                $(".edit-group-item").each(function() {
                    let gTitle = $(this).find(".g-title-edit").val();
                    let gParams = $(this).find(".ts-edit-group")[0].tomselect.getValue();
                    if(gParams.length > 0) {
                        structure.groups.push({ group_name: gTitle, params: gParams });
                    }
                });

                $.ajax({
                    url: "{{ route('update-test-new') }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: $("#edit_test_id").val(),
                        test_name: $("#edit_test_name").val(),
                        price: $("#edit_price").val(),
                        status: $("#edit_status").val(),
                        parameters_order: JSON.stringify(structure)
                    },
                    success: function(res) {
                        if(res.status == 'success') {
                            $("#editModal").modal("hide");
                            fetchTests();
                            alert("Updated Successfully");
                        } else { alert(res.message); }
                    }
                });
            });
        });

        function viewTest(id) {
            let url = "{{ route('test-new-details', ':id') }}".replace(':id', id);
            $.ajax({
                url: url, type: "GET",
                success: function (res) {
                    $("#view_test_name").text(res.test.test_name);
                    $("#view_price").html(`<i class="bi bi-currency-rupee"></i> ${res.test.price}`);
                    $("#view_status").html(res.test.status == 1 ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>');
                    
                    let container = $("#view_params_container").html("");
                    let data = JSON.parse(res.test.parameters);
                    
                    // Render Global
                    if(data.global && data.global.length > 0) {
                        container.append('<div class="view-group-header">General Parameters</div><div>');
                        data.global.forEach(pid => {
                            let text = $(`#master_parameter_source option[value='${pid}']`).text();
                            container.append(`<span class="param-item">${text}</span>`);
                        });
                        container.append('</div>');
                    }

                    // Render Groups
                    if(data.groups) {
                        data.groups.forEach(g => {
                            container.append(`<div class="view-group-header">${g.group_name}</div><div>`);
                            g.params.forEach(pid => {
                                let text = $(`#master_parameter_source option[value='${pid}']`).text();
                                container.append(`<span class="param-item">${text}</span>`);
                            });
                            container.append('</div>');
                        });
                    }
                    $("#viewModal").modal("show");
                }
            });
        }

        const masterOptions = [];
        document.querySelectorAll("#master_parameter_source option").forEach(opt => {
            masterOptions.push({value: opt.value, text: opt.text});
        });

        function editTest(id) {
            let url = "{{ route('test-new-details', ':id') }}".replace(':id', id);
            $.ajax({
                url: url, type: "GET",
                success: function (res) {
                    $("#edit_test_id").val(res.test.id);
                    $("#edit_test_name").val(res.test.test_name);
                    $("#edit_price").val(res.test.price);
                    $("#edit_status").val(res.test.status);

                    let container = $("#edit_structure_container").html("");
                    let data = JSON.parse(res.test.parameters);

                    // Global Section
                    let globalHtml = `
                        <div class="mb-3 p-2 border-bottom">
                            <label class="small fw-bold">Ungrouped Parameters</label>
                            <select id="edit_global_select" multiple></select>
                        </div>`;
                    container.append(globalHtml);
                    let tsGlobal = new TomSelect("#edit_global_select", { plugins: ['remove_button'], options: masterOptions });
                    tsGlobal.setValue(data.global || []);

                    // Group Sections
                    if(data.groups) {
                        data.groups.forEach((g, i) => {
                            let gId = 'edit_g_' + i + Date.now();
                            renderGroupRow(gId, g.group_name, g.params);
                        });
                    }

                    $("#editModal").modal("show");
                }
            });
        }

        function renderGroupRow(id, name = "", selected = []) {
            let html = `
                <div class="edit-group-item mb-3 p-2 border rounded bg-white" id="${id}">
                    <div class="d-flex justify-content-between mb-2">
                        <input type="text" class="form-control form-control-sm w-50 g-title-edit" value="${name}" placeholder="Group Name">
                        <button type="button" class="btn btn-sm text-danger" onclick="document.getElementById('${id}').remove()"><i class="bi bi-trash"></i></button>
                    </div>
                    <select class="ts-edit-group" multiple></select>
                </div>`;
            $("#edit_structure_container").append(html);
            let ts = new TomSelect(`#${id} .ts-edit-group`, { plugins: ['remove_button'], options: masterOptions });
            ts.setValue(selected);
        }

        function addNewGroupEdit() {
            let name = prompt("Enter Sub-Test Name:");
            if(name) renderGroupRow('new_g_' + Date.now(), name);
        }
    </script>
</body>
</html>