<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Test & Measurement - Rich Labz CRM</title>
    @include('includes.header_links')

    <style>
        .btn-add-test,
        .btn-remove-test {
            height: 32px !important;
            width: 60px !important;
            padding: 0 !important;
            margin-top: 28px;
            font-size: 13px !important;
            border-radius: 6px !important;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff !important;
            border: none !important;
        }

        .btn-add-test {
            background: #28a745 !important;
        }

        .btn-add-test:hover {
            background: #218838 !important;
        }

        .btn-remove-test {
            background: #dc3545 !important;
        }

        .btn-remove-test:hover {
            background: #c82333 !important;
        }

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 70%;
            transform: translateY(-50%);
            font-size: 16px;
            pointer-events: none;
            color: #6c757d;
        }

        .input-select {
            position: relative;
        }

        .input-select select {
            padding-left: 38px !important;
            padding-right: 35px !important;
            background: #f9f9fb;
        }

        .input-select i.left-icon {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .row.g-3 {
            row-gap: 10px !important;
        }
    </style>
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">

            <div class="crm-header">
                <div class="crm-title">Add Units</div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-units') }}" class="create-invoice-btn">
                        Manage Units
                    </a>
                </div>
            </div>

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

            <div class="crm-card">
                <form id="testMeasureForm" action="{{ route('store-units') }}" method="POST" autocomplete="off">
                    @csrf

                    <div id="testContainer">

                        <div class="row g-3 testRow">

                            <div class="col-md-3">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                <i class="bi bi-bounding-box"></i>
                                    <input type="text" class="form-control" name="unit[]"
                                        placeholder="Unit (mg/dL, etc)">
                                </div>
                                @error('unit.*')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="input-select">
                                <i class="bi bi-check-circle left-icon"></i>
                                <select class="form-control" name="status[]">
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                                @error('status.*')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-2 d-flex">
                                <button type="button" class="btn-add-test" onclick="addRow()">
                                    <i class="bi bi-plus-lg"></i>
                                </button>
                            </div>

                        </div>

                    </div>

                    <div class="crm-action-bar mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Tests</button>
                    </div>

                </form>
            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        function addRow() {
            let container = document.getElementById("testContainer");

            let newRow = document.createElement("div");
            newRow.classList.add("row", "g-3", "testRow", "mt-1");

            newRow.innerHTML = `
            <div class="col-md-3 input-icon">
                <label class="form-label">Unit <span class="text-danger">*</span></label>
                <i class="bi bi-bounding-box"></i>
                <input type="text" class="form-control" name="unit[]" required placeholder="Unit">
            </div>

            <div class="col-md-3 input-select">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <i class="bi bi-check-circle left-icon"></i>
                <select class="form-control" name="status[]" required>
                    <option value="" disabled selected>Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
                <i class="bi bi-chevron-down dropdown-icon"></i>
            </div>

            <div class="col-md-2 d-flex">
                <button type="button" class="btn-remove-test" onclick="removeRow(this)">
                    <i class="bi bi-dash-lg"></i>
                </button>
            </div>
        `;

            container.appendChild(newRow);
        }

        function removeRow(button) {
            button.closest(".testRow").remove();
        }
    </script>

</body>

</html>