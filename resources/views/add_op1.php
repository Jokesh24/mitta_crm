<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add OP Registration - Rich Labz CRM</title>

    @include('includes.header_links')


    <!-- Tom Select -->
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

        /* Tom Select tweaks inside input-select */
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
            box-shadow: none;
        }

        .input-select .ts-control input {
            background: transparent;
        }

        .input-select .ts-dropdown {
            border-radius: 0.25rem;
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
        .tests-card {
            margin-top: 10px;
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

        .tests-table-header .title {
            font-size: 14px;
            font-weight: 600;
            color: #051a33;
        }

        .tests-table-header .subtitle {
            font-size: 11px;
            color: #6c757d;
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

        .remove-row-btn {
            width: 28px;
            height: 28px;
            padding: 0;
        }

        .grand-total-row td {
            border-top: 1px solid #e3e6ef;
            background: #051a33;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
        }

        .grand-total-label {
            text-align: right;
        }

        .grand-total-value {
            font-size: 16px;
        }

        /* Package tests preview (under package select) */
        .package-tests-box {
            font-size: 12px;
            color: #495057;
            background: #f5f7fb;
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px dashed #cbd3e2;
            margin-top: 4px;
        }

        .package-tests-box strong {
            font-weight: 600;
        }

        @media (max-width: 767.98px) {
            .section-block {
                padding: 12px 12px;
            }

            .tests-table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
            }
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
                <div>
                    <div class="crm-title">Add OP Registration</div>
                    <div class="text-muted small">Register OP, add tests/packages and capture payment details.</div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="manage_op.php" class="create-invoice-btn">
                        Manage OPs
                    </a>
                </div>
            </div>

            <!-- MAIN CARD -->
            <div class="crm-card">
                <form autocomplete="off" id="opForm">

                    <!-- ========== PATIENT DETAILS ========== -->
                    <div class="section-block">
                        <div class="section-title">Patient Details</div>
                        <div class="section-subtext">Select existing patient or enter key information to continue.</div>

                        <div class="row g-3">

                            <!-- MOBILE NUMBER (DROPDOWN) -->
                            <div class="col-md-4">
                                <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-phone left-icon"></i>
                                    <select name="mobile" class="form-control" required>
                                        <option value="" selected disabled>Select Mobile Number</option>
                                        <!-- In future, populate from DB -->
                                        <option value="9876543210">9876543210</option>
                                        <option value="9123456789">9123456789</option>
                                        <option value="9955664477">9955664477</option>
                                        <option value="9000080000">9000080000</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <!-- PATIENT NAME -->
                            <div class="col-md-4">
                                <label class="form-label">Patient Name <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-person-circle left-icon"></i>
                                    <select name="patient_name" class="form-control" required>
                                        <option value="" selected disabled>Select Patient</option>
                                        <!-- In future, populate from DB based on mobile -->
                                        <option>Ramesh Kumar</option>
                                        <option>Priya Sharma</option>
                                        <option>Suresh Varma</option>
                                        <option>Manisha Reddy</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <!-- DOB -->
                            <div class="col-md-4">
                                <label class="form-label">Date of Birth</label>
                                <div class="input-icon">
                                    <i class="bi bi-calendar"></i>
                                    <input type="text" id="dob" name="dob" class="form-control datepicker"
                                        placeholder="Select DOB">
                                </div>
                            </div>

                            <!-- AGE -->
                            <div class="col-md-4">
                                <label class="form-label">Age <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="bi bi-123"></i>
                                    <input type="number" id="age" name="age" class="form-control" required
                                        placeholder="Enter Age">
                                </div>
                            </div>

                            <!-- GENDER -->
                            <div class="col-md-4">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-gender-ambiguous left-icon"></i>
                                    <select name="gender" class="form-control" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                        <option>Other</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <!-- COMPANY NAME -->
                            <div class="col-md-4">
                                <label class="form-label">Company Name <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-building left-icon"></i>
                                    <select class="form-control" name="company" required>
                                        <option value="" selected disabled>Select Company</option>
                                        <!-- In future, populate from DB -->
                                        <option>Dr.cita</option>
                                        <option>Mitta Excellence</option>
                                        <option>Vijaya Diagnostics</option>
                                        <option>Apollo Diagnostics</option>
                                        <option>MedPlus Labs</option>
                                        <option>SRL Diagnostics</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <!-- STATUS -->
                            <div class="col-md-4">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-check-circle left-icon"></i>
                                    <select class="form-control" name="status" required>
                                        <option value="" disabled selected>Select Status</option>
                                        <option>Active</option>
                                        <option>Inactive</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- ========== TEST TYPE & SELECTION ========== -->
                    <div class="section-block">
                        <div class="section-title">Test Type & Selection</div>
                        <div class="section-subtext">
                            Choose whether this OP is for individual lab tests or a package. You can mix both if
                            required.
                        </div>

                        <!-- TEST TYPE ROW -->
                        <div class="row g-3 align-items-end">

                            <!-- TEST TYPE -->
                            <div class="col-md-3">
                                <label class="form-label">Test Type <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-diagram-3 left-icon"></i>
                                    <select id="testType" class="form-control" required>
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="lab">Lab Test</option>
                                        <option value="package">Package</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <!-- LAB TESTS MULTI SELECT (Tom Select) -->
                            <div class="col-md-5 d-none" id="labTestWrapper">
                                <label class="form-label">Tests (Multi Select)</label>
                                <div class="input-select">
                                    <i class="bi bi-flask left-icon"></i>
                                    <select id="labTests" class="form-control" multiple>
                                        <!-- Static now; later populate from DB -->
                                        <option value="cbc" data-name="Complete Blood Count (CBC)" data-price="499">
                                            Complete Blood Count (CBC) - ₹499
                                        </option>
                                        <option value="lft" data-name="Liver Function Test (LFT)" data-price="899">
                                            Liver Function Test (LFT) - ₹899
                                        </option>
                                        <option value="rft" data-name="Renal Function Test (RFT)" data-price="799">
                                            Renal Function Test (RFT) - ₹799
                                        </option>
                                        <option value="fbs" data-name="Fasting Blood Sugar (FBS)" data-price="199">
                                            Fasting Blood Sugar (FBS) - ₹199
                                        </option>
                                        <option value="ppbs" data-name="Post Prandial Blood Sugar (PPBS)"
                                            data-price="249">
                                            Post Prandial Blood Sugar (PPBS) - ₹249
                                        </option>
                                        <option value="urine" data-name="Urine Routine" data-price="199">
                                            Urine Routine - ₹199
                                        </option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <!-- PACKAGES SELECT -->
                            <div class="col-md-5 d-none" id="packageWrapper">
                                <label class="form-label">Package</label>
                                <div class="input-select">
                                    <i class="bi bi-box-seam left-icon"></i>
                                    <select id="packageSelect" class="form-control">
                                        <option value="" selected disabled>Select Package</option>
                                        <!-- Static now; later populate from DB -->
                                        <option value="pkg-basic" data-name="Basic Health Checkup" data-price="1499"
                                            data-tests="CBC, Fasting Blood Sugar, Urine Routine">
                                            Basic Health Checkup - ₹1499
                                        </option>
                                        <option value="pkg-advanced" data-name="Advanced Health Profile"
                                            data-price="2999"
                                            data-tests="CBC, LFT, RFT, Lipid Profile, Thyroid Profile">
                                            Advanced Health Profile - ₹2999
                                        </option>
                                        <option value="pkg-diabetic" data-name="Diabetic Care Package" data-price="1999"
                                            data-tests="FBS, PPBS, HbA1c, Urine Routine">
                                            Diabetic Care Package - ₹1999
                                        </option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                                <div id="packageTestsBox" class="package-tests-box d-none">
                                    <strong>Includes:</strong>
                                    <span id="packageTestsList"></span>
                                </div>
                            </div>

                            <!-- ADD BUTTON -->
                            <div class="col-md-2">
                                <button type="button" class="btn btn-primary w-100" id="addSelectionBtn">
                                    <i class="bi bi-plus-circle me-1"></i> Add
                                </button>
                            </div>

                        </div>

                        <!-- SELECTED ITEMS TABLE -->
                        <div class="tests-card mt-3">
                            <div class="tests-table-wrapper">
                                <div class="tests-table-header">
                                    <div>
                                        <div class="title">Selected Tests & Packages</div>
                                        <div class="subtitle">Edit price if needed. Grand total updates automatically.
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
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
                                        <tbody id="selectedItemsBody">
                                            <!-- Dynamic rows here -->
                                        </tbody>
                                        <tfoot>
                                            <tr class="grand-total-row">
                                                <td colspan="3" class="grand-total-label">Grand Total</td>
                                                <td style="text-align:right;">
                                                    <span class="grand-total-value" id="grandTotal">₹0.00</span>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ========== PAYMENT DETAILS ========== -->
                    <div class="section-block">
                        <div class="section-title">Payment Details</div>
                        <div class="section-subtext">Review total amount and capture payment information.</div>

                        <div class="row g-3">

                            <!-- AMOUNT -->
                            <div class="col-md-4">
                                <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="bi bi-cash-stack"></i>
                                    <input type="number" step="0.01" min="0" id="paymentAmount" name="amount"
                                        class="form-control" required placeholder="Amount" />
                                </div>
                                <small class="text-muted">Synced with grand total. You can override if required.</small>
                            </div>

                            <!-- PAYMENT TYPE -->
                            <div class="col-md-4">
                                <label class="form-label">Payment Type <span class="text-danger">*</span></label>
                                <div class="input-select">
                                    <i class="bi bi-credit-card-2-back left-icon"></i>
                                    <select name="payment_type" id="paymentType" class="form-control" required>
                                        <option value="" selected disabled>Select Payment Type</option>
                                        <option>Cash</option>
                                        <option>UPI</option>
                                        <option>Card</option>
                                        <option>Net Banking</option>
                                    </select>
                                    <i class="bi bi-chevron-down dropdown-icon"></i>
                                </div>
                            </div>

                            <!-- PAYMENT REFERENCE -->
                            <div class="col-md-4">
                                <label class="form-label">Payment Reference No <span
                                        class="text-danger">*</span></label>
                                <div class="input-icon">
                                    <i class="bi bi-upc-scan"></i>
                                    <input type="text" class="form-control" name="payment_ref" required
                                        placeholder="Enter Reference Number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
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

            /* ========== DATEPICKER + AGE CALC (OLD FUNCTIONALITY) ========== */
            $(".datepicker").datepicker({
                dateFormat: "dd-mm-yy",
                changeMonth: true,
                changeYear: true,
                maxDate: 0
            });

            $("#dob").on("change", function () {
                let dob = $(this).val();
                if (!dob) return;

                let p = dob.split("-");
                let d = new Date(p[2], p[1] - 1, p[0]);
                let t = new Date();

                let age = t.getFullYear() - d.getFullYear();
                let m = t.getMonth() - d.getMonth();
                if (m < 0 || (m === 0 && t.getDate() < d.getDate())) age--;

                $("#age").val(age);
            });

            /* ========== TOM SELECT FOR LAB TESTS MULTI-SELECT ========== */
            var labTestsTom = new TomSelect("#labTests", {
                plugins: {
                    remove_button: { title: "Remove" }
                },
                maxItems: null,
                valueField: "value",
                labelField: "text",
                searchField: ["text"],
                create: false,
                persist: false
                // underlying <select> "selected" state is kept in sync
            });

            /* ========== TEST TYPE TOGGLE ========== */
            const $testType = $("#testType");
            const $labWrapper = $("#labTestWrapper");
            const $packageWrapper = $("#packageWrapper");

            $testType.on("change", function () {
                const type = $(this).val();
                if (type === "lab") {
                    $labWrapper.removeClass("d-none");
                    $packageWrapper.addClass("d-none");
                } else if (type === "package") {
                    $packageWrapper.removeClass("d-none");
                    $labWrapper.addClass("d-none");
                } else {
                    $labWrapper.addClass("d-none");
                    $packageWrapper.addClass("d-none");
                }
            });

            /* ========== PACKAGE TESTS PREVIEW ========== */
            const $packageSelect = $("#packageSelect");
            const $packageTestsBox = $("#packageTestsBox");
            const $packageTestsList = $("#packageTestsList");

            $packageSelect.on("change", function () {
                const sel = $(this).find("option:selected");
                const tests = sel.data("tests");
                if (tests) {
                    $packageTestsList.text(tests);
                    $packageTestsBox.removeClass("d-none");
                } else {
                    $packageTestsBox.addClass("d-none");
                }
            });

            /* ========== ADD SELECTION TO TABLE ========== */
            const $labTests = $("#labTests");
            const $selectedBody = $("#selectedItemsBody");
            const $grandTotalSpan = $("#grandTotal");
            const $paymentAmount = $("#paymentAmount");

            function formatCurrency(num) {
                num = Number(num) || 0;
                return "₹" + num.toFixed(2);
            }

            function recalcGrandTotal() {
                let total = 0;
                $selectedBody.find(".price-input").each(function () {
                    const v = parseFloat($(this).val());
                    if (!isNaN(v)) total += v;
                });
                $grandTotalSpan.text(formatCurrency(total));
                // sync to payment amount (keep same behaviour)
                $paymentAmount.val(total.toFixed(2));
            }

            function refreshSlNos() {
                $selectedBody.find("tr").each(function (index) {
                    $(this).find(".row-index").text(index + 1);
                });
            }

            function rowExists(key) {
                return $selectedBody.find('tr[data-key="' + key + '"]').length > 0;
            }

            $("#addSelectionBtn").on("click", function () {
                const type = $testType.val();
                if (!type) {
                    alert("Please select Test Type.");
                    return;
                }

                if (type === "lab") {
                    // use underlying select options (Tom Select keeps them in sync)
                    const selectedOptions = $labTests.find("option:selected");
                    if (!selectedOptions.length) {
                        alert("Please select at least one test.");
                        return;
                    }

                    selectedOptions.each(function () {
                        const opt = $(this);
                        const id = opt.val();
                        const name = opt.data("name") || opt.text();
                        const price = parseFloat(opt.data("price")) || 0;
                        const key = "test_" + id;

                        if (rowExists(key)) return; // prevent duplicates

                        const row = `
                    <tr data-type="test" data-id="${id}" data-key="${key}">
                        <td class="text-center row-index"></td>
                        <td><span class="badge-test">Test</span></td>
                        <td>${name}</td>
                        <td class="text-end">
                            <input type="number" class="form-control form-control-sm price-input" 
                                   min="0" step="0.01" value="${price.toFixed(2)}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                        $selectedBody.append(row);
                    });

                } else if (type === "package") {
                    const opt = $packageSelect.find("option:selected");
                    const id = opt.val();
                    if (!id) {
                        alert("Please select a package.");
                        return;
                    }

                    const name = opt.data("name") || opt.text();
                    const price = parseFloat(opt.data("price")) || 0;
                    const tests = opt.data("tests") || "";
                    const key = "package_" + id;

                    if (rowExists(key)) {
                        return; // prevent duplicate package
                    }

                    const testsInfo = tests
                        ? `<div class="tests-desc"><i class="bi bi-list-check me-1"></i>${tests}</div>`
                        : "";

                    const row = `
                <tr data-type="package" data-id="${id}" data-key="${key}">
                    <td class="text-center row-index"></td>
                    <td><span class="badge-package">Package</span></td>
                    <td>
                        ${name}
                        ${testsInfo}
                    </td>
                    <td class="text-end">
                        <input type="number" class="form-control form-control-sm price-input" 
                               min="0" step="0.01" value="${price.toFixed(2)}">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-row-btn">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                    $selectedBody.append(row);
                }

                refreshSlNos();
                recalcGrandTotal();
            });

            /* ========== REMOVE ROW ========== */
            $selectedBody.on("click", ".remove-row-btn", function () {
                $(this).closest("tr").remove();
                refreshSlNos();
                recalcGrandTotal();
            });

            /* ========== PRICE EDIT -> RECALCULATE GRAND TOTAL ========== */
            $selectedBody.on("input change", ".price-input", function () {
                recalcGrandTotal();
            });

            /* BASIC FORM SUBMIT HANDLER (DEMO) */
            $("#opForm").on("submit", function (e) {
                e.preventDefault();

                // In future, you can build a JSON payload of selected tests/packages and send to backend
                // Example:
                // let items = [];
                // $selectedBody.find("tr").each(function() {
                //     items.push({
                //         type: $(this).data("type"),
                //         id: $(this).data("id"),
                //         price: $(this).find(".price-input").val()
                //     });
                // });

                alert("OP Registration saved (demo). Hook this to backend as needed.");
            });

        });
    </script>

</body>

</html>