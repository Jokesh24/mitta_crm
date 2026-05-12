<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Test Results - Rich Labz CRM</title>

    @include('includes.header_links')

    <style>
        .result-card {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #e4e4e4;
        }

        .table thead th {
            font-weight: 600;
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table input[type="text"] {
            height: 36px;
            font-size: 14px;
        }

        .checkbox-col {
            width: 60px;
            text-align: center;
        }

        .unit-col {
            width: 120px;
        }

        .range-col {
            width: 150px;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 12px;
            top: 70%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon input {
            padding-left: 38px !important;
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
                <div class="crm-title">Add Test Results</div>

                <div class="d-flex gap-3">
                    <button type="button" class="btn btn-back d-flex align-items-center gap-1" onclick="history.back()">
                        <i class="bi bi-arrow-left"></i> Back
                    </button>

                    <a href="{{ route('manage-result') }}" class="create-invoice-btn">
                        Manage Results
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

            <!-- RESULT FORM CARD -->
            <div class="result-card mt-3">

                <form method="POST" action="{{ route('store-result') }}" id="resultForm" autocomplete="off">
                    @csrf

                    {{-- Hidden fields to track patient --}}
                    <input type="hidden" name="patient_id" id="patient_id" value="{{ old('patient_id') }}">
                    <input type="hidden" name="patient_type" id="patient_type" value="{{ old('patient_type', 'new') }}">

                    {{-- =========================
                    PATIENT DETAILS
                    ========================== --}}
                    <div class="row g-3 mb-4">

                        <!-- Mobile Number -->
                        <div class="col-md-6 input-icon">
                            <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                            <i class="bi bi-phone"></i>
                            <input type="text" class="form-control" id="mobile_number" name="mobile_number"
                                placeholder="Enter Mobile Number" value="{{ old('mobile_number') }}" required>
                            @error('mobile_number')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Patient Name / Dropdown -->
                        <div class="col-md-6">
                            <label class="form-label">Patient Name <span class="text-danger">*</span></label>

                            {{-- Existing patient dropdown (multiple patients for same mobile) --}}
                            <select id="existing_patient_box" name="existing_patient_id" class="form-control mb-1"
                                style="display:none;">
                            </select>

                            {{-- New / single patient text input --}}
                            <input type="text" class="form-control" id="patient_name" name="patient_name"
                                placeholder="Enter Patient Name" value="{{ old('patient_name') }}">

                            @error('existing_patient_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @error('patient_name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    {{-- =========================
                    TEST RESULT TABLE
                    (hidden until patient is resolved)
                    ========================== --}}
                    <div id="testsSection" style="display:none;">

                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Test</th>
                                    <th style="width: 200px;">Result</th>
                                    <th class="checkbox-col">Abnormal</th>
                                    <th class="unit-col">Units</th>
                                    <th class="range-col">Normal Range</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($tests as $index => $test)
                                    <tr>
                                        <td>
                                            {{ $test->test_name }}

                                            {{-- Hidden fields so controller knows what is what --}}
                                            <input type="hidden" name="tests[{{ $index }}][test_id]"
                                                value="{{ $test->id }}">
                                            <input type="hidden" name="tests[{{ $index }}][unit_id]"
                                                value="{{ $test->unit_id }}">
                                            <input type="hidden" name="tests[{{ $index }}][normal_range]"
                                                value="{{ $test->normal_range }}">
                                        </td>

                                        <td>
                                            <input type="text" name="tests[{{ $index }}][result_value]" class="form-control"
                                                placeholder="Enter Result">
                                        </td>

                                        <td class="text-center">
                                            <input type="checkbox" name="tests[{{ $index }}][is_abnormal]" value="1">
                                        </td>

                                        <td>
                                            {{ $test->unit->unit_name ?? '—' }}
                                        </td>

                                        <td>
                                            {{ $test->normal_range ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Results</button>
                    </div>

                </form>

            </div>

        </div>
    </div>

    @include('includes.footer_links')

    <script>
        $(function () {

            // =========================
            // Helpers
            // =========================
            function showTests() {
                $("#testsSection").slideDown(150);
            }

            function hideTests() {
                $("#testsSection").slideUp(150);
            }

            function clearPatient() {
                $("#patient_id").val("");
                $("#patient_name").val("");
                $("#existing_patient_box").hide().empty();
                $("#patient_type").val("new");
                hideTests();
            }

            // =========================
            // When mobile changes -> fetch patients
            // =========================
            $("#mobile_number").on("keyup", function () {
                let mobile = $(this).val().trim();

                // Don’t spam backend on every char for very short strings
                if (mobile.length < 5) {
                    clearPatient();
                    return;
                }

                $.ajax({
                    url: "{{ route('fetch-patient') }}",
                    type: "GET",
                    data: { mobile: mobile },
                    success: function (res) {

                        // Always reset state first
                        $("#existing_patient_box").hide().empty();
                        $("#patient_name").val("");
                        $("#patient_id").val("");
                        $("#patient_type").val("new");
                        hideTests();

                        if (res.exists) {

                            // MULTIPLE PATIENTS → dropdown
                            if (res.patients.length > 1) {

                                let $sel = $("#existing_patient_box");
                                $sel.show().append(`
                                    <option value="">Select Patient</option>
                                `);

                                res.patients.forEach(function (p) {
                                    $sel.append(`
                                        <option value="${p.id}"
                                                data-name="${p.patient_name}">
                                            ${p.patient_name}
                                        </option>
                                    `);
                                });

                                // We are working with existing patient(s)
                                $("#patient_type").val("existing");

                            } else {
                                // SINGLE PATIENT → auto fill & show tests
                                let p = res.patients[0];

                                $("#patient_id").val(p.id);
                                $("#patient_name").val(p.patient_name);
                                $("#patient_type").val("existing");

                                showTests();
                            }

                        } else {
                            // No patients → new patient mode, wait for name
                            $("#patient_type").val("new");
                            hideTests();
                        }
                    }
                });
            });

            // =========================
            // Existing patient dropdown change → fill name, show tests
            // =========================
            $(document).on("change", "#existing_patient_box", function () {
                let id = $(this).val();
                let name = $(this).find("option:selected").data("name");

                if (!id) {
                    $("#patient_id").val("");
                    $("#patient_name").val("");
                    $("#patient_type").val("new");
                    hideTests();
                    return;
                }

                $("#patient_id").val(id);
                $("#patient_name").val(name);
                $("#patient_type").val("existing");

                showTests();
            });

            // =========================
            // For NEW patient: show tests only when name is filled
            // =========================
            $("#patient_name").on("keyup blur", function () {
                let patientType = $("#patient_type").val();
                let name = $(this).val().trim();
                let mobile = $("#mobile_number").val().trim();

                if (patientType === "new" && name.length > 1 && mobile.length >= 5) {
                    showTests();
                } else if (name.length === 0) {
                    hideTests();
                }
            });

        });
    </script>

</body>

</html>