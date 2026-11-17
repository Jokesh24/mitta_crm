<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit OP Registration - Rich Labz CRM</title>
    @include('includes.header_links')

    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <style>
        /* ========== ICON + INPUT / SELECT COMMON STYLES ========== */
        .input-select,
        .input-icon {
            position: relative;
        }

        .input-select i.left-icon,
        .input-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 15px;
            z-index: 5;
        }

        .input-select select,
        select.form-control,
        select.form-select {
            padding-left: 38px !important;
            padding-right: 35px !important;
            background: #f9f9fb;
        }

        .input-icon input {
            padding-left: 38px !important;
            background: #f9f9fb;
        }

        .dropdown-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            pointer-events: none;
            color: #6c757d;
        }

        .input-select .ts-control {
            min-height: 38px;
            border-radius: 0.25rem;
            padding-left: 38px !important;
            background: #f9f9fb;
            border-color: #ced4da;
            box-shadow: none;
        }

        .input-select .ts-control input {
            background: transparent;
        }

        .input-select .ts-dropdown {
            border-radius: 0.25rem;
        }

        .section-block {
            padding: 16px 18px;
            border-radius: 10px;
            border: 1px solid #e3e6ef;
            background: #fff;
            margin-bottom: 18px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            color: #051a33;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .section-title::before {
            content: "";
            width: 4px;
            height: 18px;
            border-radius: 8px;
            background: linear-gradient(180deg, #4AA243, #8EC33D);
        }

        .section-subtext {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 12px;
        }

        .tests-table-wrapper {
            border: 1px solid #e3e6ef;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .tests-table-header {
            padding: 10px 14px;
            border-bottom: 1px solid #e3e6ef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, #f5f7fb, #ffffff);
        }

        .selected-tests-table {
            margin-bottom: 0;
            font-size: 13px;
        }

        .selected-tests-table thead {
            background: #f7f8fc;
        }

        .selected-tests-table thead th {
            padding: 9px 10px;
            font-weight: 600;
            color: #495057;
            border-bottom: 1px solid #e3e6ef;
            white-space: nowrap;
        }

        .selected-tests-table tbody td {
            padding: 8px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f2f7;
        }

        .selected-tests-table tbody tr:hover {
            background: #f9fbff;
        }

        .badge-test {
            background: #e7f1ff;
            color: #0b63ce;
            font-size: 11px;
            padding: 3px 7px;
            border-radius: 999px;
        }

        .badge-package {
            background: #e6f7ea;
            color: #2b8a3e;
            font-size: 11px;
            padding: 3px 7px;
            border-radius: 999px;
        }

        .badge-profile {
            background: #fff3bf;
            color: #f08c00;
            font-size: 11px;
            padding: 3px 7px;
            border-radius: 999px;
        }

        .tests-desc {
            font-size: 11px;
            color: #6c757d;
        }

        .price-input {
            max-width: 110px;
            text-align: right;
            font-size: 13px;
            padding-right: 6px;
            padding-left: 6px !important;
            height: 32px;
        }

        .grand-total-row td {
            border-top: 1px solid #e3e6ef;
            background: #051a33;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
        }

        .package-tests-box {
            font-size: 12px;
            color: #495057;
            background: #f5f7fb;
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px dashed #cbd3e2;
            margin-top: 4px;
        }
    </style>
</head>

<body>
    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">
            <div class="crm-header">
                <div>
                    <div class="crm-title">Edit OP Registration</div>
                    <div class="text-muted small">Update patient details, tests/packages and payment information.</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1"
                        onclick="history.back()"><i class="bi bi-arrow-left"></i> Back</button>
                    <a href="{{ route('manage-op') }}" class="create-invoice-btn">Manage OPs</a>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="crm-card">
                <form autocomplete="off" id="opForm" method="POST" action="{{ route('update-op', $op->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="section-block">
                        <div class="section-title">Patient Details</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="bi bi-phone"></i>
                                    <input type="text" id="mobile_number" name="mobile_number" class="form-control"
                                        value="{{ old('mobile_number', $op->patient->mobile_number ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <div class="input-select" id="existing_patient_wrapper" style="display:none;">
                                    <i class="bi bi-person-circle left-icon"></i>
                                    <select id="existing_patient_box" name="existing_patient_id"
                                        class="form-control"></select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                                <div class="input-icon" id="new_patient_wrapper">
                                    <i class="bi bi-person-circle"></i>
                                    <input type="text" id="new_patient_box" name="patient_name"
                                        value="{{ old('patient_name', $op->patient->patient_name ?? '') }}"
                                        class="form-control">
                                </div>
                                <!-- <input type="hidden" id="patient_type" name="patient_type" value="existing"> -->
                                <input type="hidden" id="patient_type" name="patient_type" value="new">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date of Birth</label>
                                <div class="input-icon">
                                    <i class="bi bi-calendar"></i>
                                    <input type="text" id="dob" name="dob" class="form-control datepicker"
                                        value="{{ old('dob', $op->patient->dob ? date('d-m-Y', strtotime($op->patient->dob)) : '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="bi bi-123"></i>
                                    <input type="number" id="age" name="age" class="form-control"
                                        value="{{ old('age', $op->patient->age ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-gender-ambiguous left-icon"></i>
                                    @php $genderVal = old('gender', $op->patient->gender ?? ''); @endphp
                                    <select name="gender" class="form-control">
                                        <option value="Male" {{ $genderVal == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ $genderVal == 'Female' ? 'selected' : '' }}>Female
                                        </option>
                                        <option value="Other" {{ $genderVal == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Company</label>
                                <div class="input-select">
                                    <i class="bi bi-building left-icon"></i>
                                    <select class="form-control" name="company">
                                        <option value="">Self</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company', $op->company_id) == $company->id ? 'selected' : '' }}>
                                                {{ $company->company_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-geo-alt left-icon"></i>
                                    <select id="location_select" class="form-control">
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->location }}" {{ $loc->location == old('location', $op->location) ? 'selected' : '' }}>{{ $loc->location }}</option>
                                        @endforeach
                                        <option value="__new">+ Add New Location</option>
                                    </select>
                                </div>
                                <input type="text" id="location_input" name="location" class="form-control mt-2"
                                    value="{{ old('location', $op->location) }}" style="display:none;">
                            </div>
                        </div>
                    </div>

                    <div class="section-block">
                        <div class="section-title">Test Type & Selection</div>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Test Type</label>
                                <select id="testType" class="form-control">
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="lab">Lab Test</option>
                                    <option value="package">Package</option>
                                    <option value="test_new">Profile / Special Test</option>
                                </select>
                            </div>
                            <div class="col-md-5 d-none" id="labTestWrapper">
                                <select id="labTests" class="form-control" multiple></select>
                            </div>
                            <div class="col-md-5 d-none" id="packageWrapper">
                                <select id="packageSelect" class="form-control"></select>
                                <div id="packageTestsBox" class="package-tests-box d-none"><strong>Includes:</strong>
                                    <span id="packageTestsList"></span>
                                </div>
                            </div>
                            <div class="col-md-5 d-none" id="testsNewWrapper">
                                <select id="testsNewSelect" class="form-control"></select>
                                <div id="testsNewBox" class="package-tests-box d-none"><strong>Includes:</strong> <span
                                        id="testsNewList"></span></div>
                            </div>
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary w-100" id="addSelectionBtn"><i
                                        class="bi bi-plus-circle"></i> Add</button>
                            </div>
                        </div>

                        <div class="tests-card mt-3">
                            <div class="tests-table-wrapper">
                                <table class="table selected-tests-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px; text-align:center;">#</th>
                                            <th style="width: 110px;">Type</th>
                                            <th>Test / Package</th>
                                            <th style="width: 160px; text-align:right;">Price (₹)</th>
                                            <th style="width: 70px; text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="selectedItemsBody"></tbody>
                                    <tfoot>
                                        <tr class="grand-total-row">
                                            <td colspan="3" class="text-end">Grand Total</td>
                                            <td style="text-align:right;"><span id="grandTotal">₹0.00</span></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="section-block">
                        <div class="section-title">Payment</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Amount (₹)</label>
                                <input type="number" step="0.01" id="paymentAmount" name="amount" class="form-control"
                                    value="{{ old('amount', $op->payment_amount ?? $op->grand_total) }}" />
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Payment Type</label>
                                <select name="payment_type" class="form-control">
                                    <option {{ old('payment_type', $op->payment_type) == 'Cash' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option {{ old('payment_type', $op->payment_type) == 'UPI' ? 'selected' : '' }}>UPI
                                    </option>
                                    <option {{ old('payment_type', $op->payment_type) == 'Card' ? 'selected' : '' }}>Card
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference No</label>
                                <input type="text" class="form-control" name="payment_ref"
                                    value="{{ old('payment_ref', $op->payment_ref) }}">
                            </div>
                        </div>
                    </div>

                    <div class="crm-action-bar mt-3">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update OP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('includes.footer_links')

    <script>
        $(function () {
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $(".datepicker").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, maxDate: 0 });

            let labTestsTom = null;
            const $selectedBody = $("#selectedItemsBody");

            // Database saved JSONs (Ensure these columns exist and are cast to array in Op Model)
            const existingTests = @json($op->tests_json ?? []);
            const existingPackages = @json($op->packages_json ?? []);
            const existingProfiles = @json($op->tests_new_json ?? []);

            function renderRow(type, id, name, price, extraInfo, key) {
                let badge = type === 'test' ? 'badge-test' : (type === 'package' ? 'badge-package' : 'badge-profile');
                let typeLabel = type === 'test' ? 'Param' : (type === 'package' ? 'Package' : 'Lab Test');

                const row = `<tr data-type="${type}" data-id="${id}" data-key="${key}">
                <td class="text-center row-index"></td>
                <td><span class="${badge}">${typeLabel}</span></td>
                <td><strong>${name}</strong><div class="tests-desc">${extraInfo}</div></td>
                <td class="text-end"><input type="number" class="form-control price-input ms-auto" value="${parseFloat(price).toFixed(2)}"></td>
                <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="bi bi-trash"></i></button></td>
            </tr>`;
                $selectedBody.append(row);
            }

            function initPageData() {
                $.when(
                    $.get("{{ route('get.lab.tests') }}"),
                    $.get("{{ route('get.packages') }}"),
                    $.get("{{ route('get.tests.new') }}")
                ).done(function (rTests, rPackages, rProfiles) {
                    let tests = rTests[0], packages = rPackages[0], profiles = rProfiles[0];

                    // 1. Setup Select dropdowns
                    $("#labTests").html(tests.map(t => `<option value="${t.id}" data-price="${t.price}">${t.test_name}</option>`).join(''));
                    labTestsTom = new TomSelect("#labTests", { plugins: ['remove_button'] });

                    $("#packageSelect").html('<option disabled selected>Select Package</option>' + packages.map(p => `<option value="${p.id}" data-name="${p.package_name}" data-price="${p.package_price}" data-tests="${p.tests.join(', ')}">${p.package_name}</option>`).join(''));

                    $("#testsNewSelect").html('<option disabled selected>Select Lab Test</option>' + profiles.map(t => `<option value="${t.id}" data-name="${t.test_name}" data-price="${t.price}" data-preview="${t.tests.join(', ')}">${t.test_name}</option>`).join(''));

                    // 2. Prefill Table from saved data
                    existingTests.forEach(item => {
                        let meta = tests.find(x => x.id == item.id);
                        if (meta) renderRow("test", item.id, meta.test_name, item.price, "", "test_" + item.id);
                    });

                    existingPackages.forEach(item => {
                        let meta = packages.find(x => x.id == item.id);
                        if (meta) renderRow("package", item.id, meta.package_name, item.price, meta.tests.join(", "), "package_" + item.id);
                    });

                    existingProfiles.forEach(item => {
                        let meta = profiles.find(x => x.id == item.id);
                        if (meta) renderRow("test_new", item.id, meta.test_name, item.price, meta.tests.join(", "), "test_new_" + item.id);
                    });

                    recalc();
                });
            }

            initPageData();

            // Type Selection Toggle
            $("#testType").on("change", function () {
                let v = $(this).val();
                $("#labTestWrapper").toggleClass("d-none", v !== "lab");
                $("#packageWrapper").toggleClass("d-none", v !== "package");
                $("#testsNewWrapper").toggleClass("d-none", v !== "test_new");
            });

            // Add Button Logic
            $("#addSelectionBtn").on("click", function () {
                let type = $("#testType").val();
                if (type === "lab") {
                    labTestsTom.getValue().forEach(id => {
                        let opt = labTestsTom.options[id];
                        if ($(`tr[data-key="test_${id}"]`).length === 0) renderRow("test", id, opt.test_name, opt.price, "", "test_" + id);
                    });
                } else {
                    let $sel = type === "package" ? $("#packageSelect") : $("#testsNewSelect");
                    let opt = $sel.find(":selected");
                    let id = opt.val(), name = opt.data('name'), price = opt.data('price'), desc = opt.data('tests') || opt.data('preview') || "";
                    let actualType = type === 'test_new' ? 'test_new' : 'package';
                    if (id && $(`tr[data-key="${actualType}_${id}"]`).length === 0) renderRow(actualType, id, name, price, desc, actualType + "_" + id);
                }
                recalc();
            });

            $(document).on("click", ".remove-row-btn", function () { $(this).closest("tr").remove(); recalc(); });
            $(document).on("input", ".price-input", recalc);

            function recalc() {
                let total = 0;
                $(".price-input").each(function () { total += parseFloat($(this).val()) || 0; });
                $("#grandTotal").text("₹" + total.toFixed(2));
                $("#paymentAmount").val(total.toFixed(2));
                $selectedBody.find("tr").each((i, row) => $(row).find(".row-index").text(i + 1));
            }

            $("#opForm").on("submit", function () {
                let t = [], p = [], n = [];
                $selectedBody.find("tr").each(function () {
                    let row = $(this), item = { id: row.data('id'), price: row.find('.price-input').val() };
                    let type = row.data('type');
                    if (type === 'test') t.push(item);
                    else if (type === 'package') p.push(item);
                    else if (type === 'test_new') n.push(item);
                });
                $(this).append(`<input type="hidden" name="tests_json" value='${JSON.stringify(t)}'>`);
                $(this).append(`<input type="hidden" name="packages_json" value='${JSON.stringify(p)}'>`);
                $(this).append(`<input type="hidden" name="tests_new_json" value='${JSON.stringify(n)}'>`);
            });

            $('#location_select').on('change', function () {
                let v = $(this).val();
                if (v === "__new") $("#location_input").show().val("");
                else $("#location_input").hide().val(v);
            });
        });
    </script>
</body>

</html>