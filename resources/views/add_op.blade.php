<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add OP Registration - Rich Labz CRM</title>

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

        /* Tom Select tweaks */
        .input-select .ts-wrapper {
            width: 100%;
        }

        .input-select .ts-control {
            min-height: 38px;
            border-radius: 0.25rem;
            padding-left: 38px !important;
            padding-right: 35px !important;
            background: #f9f9fb;
            border-color: #ced4da;
        }

        /* ========== SECTION HEADINGS ========== */
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

        /* ========== SELECTED ITEMS TABLE ========== */
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
            border-bottom: 1px solid #e3e6ef;
        }

        .selected-tests-table tbody td {
            padding: 8px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f2f7;
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
            margin-top: 2px;
        }

        .price-input {
            max-width: 110px;
            text-align: right;
            font-size: 13px;
            height: 32px;
        }

        .grand-total-row td {
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
                    <div class="crm-title">Add OP Registration</div>
                    <div class="text-muted small">Register OP, add tests/packages and capture payment details.</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back" onclick="history.back()"><i class="bi bi-arrow-left"></i>
                        Back</button>
                    <a href="{{ route('manage-op') }}" class="create-invoice-btn">Manage OPs</a>
                </div>
            </div>

            <div class="crm-card">
                <form autocomplete="off" id="opForm" method="POST" action="{{ route('store-op') }}">
                    @csrf

                    <div class="section-block">
                        <div class="section-title">Patient Details</div>
                        <div class="section-subtext">Search existing patient by mobile or add a new patient.</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="bi bi-phone"></i>
                                    <input type="text" id="mobile_number" name="mobile_number"
                                        value="{{ old('mobile_number') }}" class="form-control"
                                        placeholder="Enter Mobile Number">
                                </div>
                                <div id="mobile_error" class="text-danger small mt-1"></div>
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
                                        value="{{ old('patient_name') }}" class="form-control"
                                        placeholder="Enter Patient Name">
                                </div>
                                <input type="hidden" id="patient_type" name="patient_type"
                                    value="{{ old('patient_type', 'new') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Date of Birth</label>
                                <div class="input-icon">
                                    <i class="bi bi-calendar"></i>
                                    <input type="text" id="dob" name="dob" class="form-control datepicker"
                                        placeholder="Select DOB" value="{{ old('dob') }}">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <div class="input-icon"><i class="bi bi-123"></i>
                                    <input type="number" id="age" name="age" value="{{ old('age') }}"
                                        class="form-control" placeholder="Enter Age">
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-gender-ambiguous left-icon"></i>
                                    <select name="gender" class="form-control">
                                        <option value="" disabled selected>Select Gender</option>
                                        <option {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Company</label>
                                <div class="input-select">
                                    <i class="bi bi-building left-icon"></i>
                                    <select class="form-control" name="company">
                                        <option value="" selected disabled>Select Company</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" {{ old('company') == $company->id ? 'selected' : '' }}>{{ $company->company_name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Location <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-geo-alt left-icon"></i>
                                    <select id="location_select" class="form-control">
                                        <option value="" disabled selected>Select or Enter Location</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->location }}">{{ $loc->location }}</option>
                                        @endforeach
                                        <option value="__new">+ Add New Location</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                                <input type="text" id="location_input" name="location" class="form-control mt-2"
                                    style="display:none;" value="{{ old('location') }}">
                            </div>
                        </div>
                    </div>

                    <div class="section-block">
                        <div class="section-title">Test Type & Selection</div>
                        <div class="section-subtext">Choose between Parameters, Packages, or Lab Tests (Profiles).</div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Test Type <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-diagram-3 left-icon"></i>
                                    <select id="testType" class="form-control">
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="lab">Parameters</option>
                                        <option value="package">Package</option>
                                        <option value="test_new">Lab Test</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-5 d-none" id="labTestWrapper">
                                <label class="form-label">Parameters</label>
                                <div class="input-select">
                                    <i class="bi bi-flask left-icon"></i>
                                    <select id="labTests" class="form-control" multiple></select>
                                </div>
                            </div>

                            <div class="col-md-5 d-none" id="packageWrapper">
                                <label class="form-label">Package</label>
                                <div class="input-select">
                                    <i class="bi bi-box-seam left-icon"></i>
                                    <select id="packageSelect" class="form-control"></select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                                <div id="packageTestsBox" class="package-tests-box d-none"><strong>Includes:</strong>
                                    <span id="packageTestsList"></span></div>
                            </div>

                            <div class="col-md-5 d-none" id="testsNewWrapper">
                                <label class="form-label">Lab Test (Profile)</label>
                                <div class="input-select">
                                    <i class="bi bi-collection left-icon"></i>
                                    <select id="testsNewSelect" class="form-control"></select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
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
                                <div class="table-responsive">
                                    <table class="table selected-tests-table">
                                        <thead>
                                            <tr>
                                                <th class="text-center" style="width:50px;">#</th>
                                                <th style="width:110px;">Type</th>
                                                <th>Item Name</th>
                                                <th class="text-end" style="width:160px;">Price (₹)</th>
                                                <th class="text-center" style="width:70px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedItemsBody"></tbody>
                                        <tfoot>
                                            <tr class="grand-total-row">
                                                <td colspan="3" class="text-end">Grand Total</td>
                                                <td class="text-end"><span id="grandTotal">₹0.00</span></td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-block">
                        <div class="section-title">Payment Details</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Amount (₹)</label>
                                <div class="input-icon"><i class="bi bi-cash-stack"></i>
                                    <input type="number" step="0.01" id="paymentAmount" name="amount"
                                        class="form-control" value="{{ old('amount') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Payment Type</label>
                                <div class="input-select">
                                    <i class="bi bi-credit-card-2-back left-icon"></i>
                                    <select name="payment_type" class="form-control">
                                        <option value="" disabled selected>Select Payment</option>
                                        <option>Cash</option>
                                        <option>UPI</option>
                                        <option>Card</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Reference No</label>
                                <div class="input-icon"><i class="bi bi-upc-scan"></i>
                                    <input type="text" class="form-control" name="payment_ref" placeholder="Ref No">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="crm-action-bar mt-3">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Register OP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('includes.footer_links')

    <script>
        $(function () {
            // Setup CSRF for AJAX
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

            // Datepicker
            $(".datepicker").datepicker({ dateFormat: "dd-mm-yy", changeMonth: true, changeYear: true, maxDate: 0 });

            $("#dob").on("change", function () {
                let p = $(this).val().split("-");
                let d = new Date(p[2], p[1] - 1, p[0]), t = new Date();
                let age = t.getFullYear() - d.getFullYear();
                if (t.getMonth() < d.getMonth() || (t.getMonth() == d.getMonth() && t.getDate() < d.getDate())) age--;
                $("#age").val(age);
            });

            // Mobile Validation & Fetch
            $("#mobile_number").on("input", function () {
                let val = $(this).val().replace(/\D/g, "").substring(0, 10);
                $(this).val(val);
                if (val.length === 10 && /^[6-9]/.test(val)) {
                    $("#mobile_error").text("");
                    $.get("{{ route('fetch-patient') }}", { mobile: val }, function (res) {
                        if (res.exists) {
                            if (res.patients.length === 1) {
                                let p = res.patients[0];
                                $("#new_patient_box").val(p.patient_name);
                                $("#age").val(p.age);
                                $("select[name='gender']").val(p.gender);
                                $("#patient_type").val("new");
                            } else {
                                $("#existing_patient_wrapper").show();
                                $("#new_patient_wrapper").hide();
                                $("#existing_patient_box").html('<option disabled selected>Select Patient</option>');
                                res.patients.forEach(p => {
                                    $("#existing_patient_box").append(`<option value="${p.id}" data-age="${p.age}" data-gender="${p.gender}">${p.patient_name}</option>`);
                                });
                                $("#existing_patient_box").append('<option value="new_patient">+ Add New</option>');
                                $("#patient_type").val("existing");
                            }
                        }
                    });
                }
            });

            $(document).on("change", "#existing_patient_box", function () {
                if ($(this).val() === 'new_patient') {
                    $("#existing_patient_wrapper").hide();
                    $("#new_patient_wrapper").show();
                    $("#patient_type").val("new");
                } else {
                    let opt = $(this).find(":selected");
                    $("#age").val(opt.data('age'));
                    $("select[name='gender']").val(opt.data('gender'));
                }
            });

            // Location Logic
            $("#location_select").on("change", function () {
                if ($(this).val() === '__new') $("#location_input").show().val("");
                else $("#location_input").show().val($(this).val());
            });

            // Type Selection & Loading
            let labTom = null;
            $("#testType").on("change", function () {
                let type = $(this).val();
                $("#labTestWrapper, #packageWrapper, #testsNewWrapper").addClass("d-none");
                if (type === 'lab') {
                    $("#labTestWrapper").removeClass("d-none");
                    if (!labTom) $.get("{{ route('get.lab.tests') }}", function (data) {
                        let html = data.map(t => `<option value="${t.id}" data-name="${t.test_name}" data-price="${t.price}">${t.test_name} - ₹${t.price}</option>`).join("");
                        $("#labTests").html(html);
                        labTom = new TomSelect("#labTests", { plugins: ['remove_button'] });
                    });
                } else if (type === 'package') {
                    $("#packageWrapper").removeClass("d-none");
                    $.get("{{ route('get.packages') }}", function (data) {
                        let html = '<option disabled selected>Select Package</option>';
                        html += data.map(p => `<option value="${p.id}" data-name="${p.package_name}" data-price="${p.package_price}" data-tests="${p.tests.join(', ')}">${p.package_name}</option>`).join("");
                        $("#packageSelect").html(html);
                    });
                } else if (type === 'test_new') {
                    $("#testsNewWrapper").removeClass("d-none");
                    $.get("{{ route('get.tests.new') }}", function (data) {
                        let html = '<option disabled selected>Select Lab Test</option>';
                        html += data.map(t => `<option value="${t.id}" data-name="${t.test_name}" data-price="${t.price}" data-preview="${t.tests.join(', ')}">${t.test_name}</option>`).join("");
                        $("#testsNewSelect").html(html);
                    });
                }
            });

            $("#packageSelect").on("change", function () { $("#packageTestsList").text($(this).find(":selected").data('tests')); $("#packageTestsBox").removeClass("d-none"); });
            $("#testsNewSelect").on("change", function () { $("#testsNewList").text($(this).find(":selected").data('preview')); $("#testsNewBox").removeClass("d-none"); });

            // Add to Table
            $("#addSelectionBtn").on("click", function () {
                let type = $("#testType").val();
                let html = "";
                if (type === 'lab') {
                    let items = labTom.getValue();
                    items.forEach(id => {
                        let opt = labTom.options[id], key = "test_" + id;
                        if ($(`tr[data-key="${key}"]`).length) return;
                        html += `<tr data-type="test" data-id="${id}" data-key="${key}"><td class="row-index"></td><td><span class="badge-test">Param</span></td><td>${opt.text}</td><td class="text-end"><input type="number" class="form-control price-input ms-auto" value="${opt.price}"></td><td class="text-center"><button class="btn btn-sm btn-outline-danger remove-row-btn"><i class="bi bi-trash"></i></button></td></tr>`;
                    });
                } else if (type === 'package' || type === 'test_new') {
                    let sel = type === 'package' ? $("#packageSelect") : $("#testsNewSelect");
                    let opt = sel.find(":selected"), id = opt.val(), key = type + "_" + id;
                    if (!id || $(`tr[data-key="${key}"]`).length) return;
                    let badge = type === 'package' ? 'badge-package' : 'badge-profile';
                    let label = type === 'package' ? 'Package' : 'Lab Test';
                    let desc = type === 'package' ? opt.data('tests') : opt.data('preview');
                    html = `<tr data-type="${type}" data-id="${id}" data-key="${key}"><td class="row-index"></td><td><span class="${badge}">${label}</span></td><td><strong>${opt.data('name')}</strong><div class="tests-desc">${desc}</div></td><td class="text-end"><input type="number" class="form-control price-input ms-auto" value="${opt.data('price')}"></td><td class="text-center"><button class="btn btn-sm btn-outline-danger remove-row-btn"><i class="bi bi-trash"></i></button></td></tr>`;
                }
                $("#selectedItemsBody").append(html);
                recalc();
            });

            $(document).on("click", ".remove-row-btn", function () { $(this).closest("tr").remove(); recalc(); });
            $(document).on("input", ".price-input", recalc);

            function recalc() {
                let total = 0;
                $(".price-input").each(function () { total += parseFloat($(this).val()) || 0; });
                $("#grandTotal").text("₹" + total.toFixed(2));
                $("#paymentAmount").val(total.toFixed(2));
                $("#selectedItemsBody tr").each((i, row) => $(row).find(".row-index").text(i + 1));
            }

            $("#opForm").on("submit", function () {
                let t = [], p = [], n = [];
                $("#selectedItemsBody tr").each(function () {
                    let item = { id: $(this).data('id'), price: $(this).find('.price-input').val() };
                    let type = $(this).data('type');
                    if (type === 'test') t.push(item);
                    else if (type === 'package') p.push(item);
                    else if (type === 'test_new') n.push(item);
                });
                $(this).append(`<input type="hidden" name="tests_json" value='${JSON.stringify(t)}'>`);
                $(this).append(`<input type="hidden" name="packages_json" value='${JSON.stringify(p)}'>`);
                $(this).append(`<input type="hidden" name="tests_new_json" value='${JSON.stringify(n)}'>`);
            });
        });
    </script>
</body>

</html>