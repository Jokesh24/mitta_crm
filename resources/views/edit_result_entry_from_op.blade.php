<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Test Results - OP #{{ $op->id }} - Rich Labz CRM</title>
    @include('includes.header_links')

    <style>
        .patient-summary { background: #fff; border-radius: 12px; border: 1px solid #e2e6f0; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .section-card { background: #fff; border-radius: 12px; border: 1px solid #e2e6f0; padding: 25px; margin-bottom: 25px; }
        .group-header { background: #f8fafc; color: #0f172a; font-weight: 800; padding: 12px 18px; border-left: 5px solid #2563eb; margin-bottom: 20px; font-size: 16px; display: flex; align-items: center; justify-content: space-between; }
        .subgroup-header-row { background: #f1f5f9; }
        .subgroup-header-row td { font-weight: 700; font-size: 12px; color: #475569; text-transform: uppercase; padding: 8px 15px !important; }
        .table thead th { background: #f8fafc; color: #64748b; font-size: 11px; text-transform: uppercase; border-top: none; }
        .input-result { border-radius: 6px; border: 1px solid #cbd5e1; height: 35px; text-align: center; font-weight: 600; color: #2563eb; }
        .input-result:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
        .sticky-action-bar { position: sticky; bottom: 0; background: #fff; border-top: 1px solid #e2e6f0; padding: 15px 0; z-index: 100; margin-top: 30px; box-shadow: 0 -5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="container-crm">
            <div class="crm-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Edit Result Entry</h3>
                    <p class="text-muted mb-0">Update values for <strong>Reg No: {{ $op->op_reg_no }}</strong></p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="history.back()">Back</button>
                    <button type="submit" form="resultForm" class="btn btn-primary px-4 shadow-sm">Update Results</button>
                </div>
            </div>

            <div class="patient-summary row g-3">
                <div class="col-md-3"><strong>Patient Name:</strong><br>{{ $op->patient->patient_name }}</div>
                <div class="col-md-2"><strong>Age/Sex:</strong><br>{{ $op->patient->age }}Y / {{ $op->patient->gender }}</div>
                <div class="col-md-2"><strong>Mobile:</strong><br>{{ $op->patient->mobile_number }}</div>
                <div class="col-md-3"><strong>Reference:</strong><br>{{ $op->company->company_name ?? 'Self' }}</div>
                <div class="col-md-2 text-end"><strong>Date:</strong><br>{{ $op->created_at->format('d-M-Y') }}</div>
            </div>

            <form id="resultForm" method="POST" action="{{ route('update-result-from-op', $header->id) }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $op->patient_id }}">
                <input type="hidden" name="mobile_number" value="{{ $op->patient->mobile_number }}">

                {{-- Hidden Array Logic --}}
                @foreach($uniqueTests as $idx => $uTest)
                    @php 
                        $val = $existing[$uTest->id]['value'] ?? ''; 
                        $abn = $existing[$uTest->id]['abnormal'] ?? 0;
                    @endphp
                    <input type="hidden" name="tests[{{ $idx }}][test_id]" value="{{ $uTest->id }}">
                    <input type="hidden" name="tests[{{ $idx }}][result_value]" id="val_{{ $uTest->id }}" value="{{ $val }}">
                    <input type="hidden" name="tests[{{ $idx }}][is_abnormal]" id="abn_{{ $uTest->id }}" value="{{ $abn }}">
                @endforeach

                {{-- Packages and Profiles --}}
                @foreach($allGroups as $group)
                    <div class="section-card">
                        <div class="group-header">
                            <span><i class="bi bi-layers-half me-2"></i>{{ $group['title'] }}</span>
                            <span class="badge bg-primary rounded-pill" style="font-size: 10px;">{{ $group['is_profile'] ? 'PROFILE' : 'PACKAGE' }}</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th style="width:35%">Parameter Name</th>
                                        <th style="width:20%" class="text-center">Observation / Result</th>
                                        <th style="width:10%" class="text-center">Abnormal</th>
                                        <th style="width:15%">Unit</th>
                                        <th style="width:20%">Normal Range</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($group['subgroups'] as $sub)
                                        @if($sub['name'])
                                            <tr class="subgroup-header-row">
                                                <td colspan="5"><i class="bi bi-chevron-double-right me-1"></i>{{ strtoupper($sub['name']) }}</td>
                                            </tr>
                                        @endif
                                        @foreach($sub['tests'] as $test)
                                            <tr>
                                                <td class="ps-4 text-dark fw-medium">{{ $test->test_name }}</td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm input-result ui-sync" 
                                                           data-id="{{ $test->id }}" 
                                                           value="{{ $existing[$test->id]['value'] ?? '' }}"
                                                           placeholder="Enter value">
                                                </td>
                                                <td class="text-center">
                                                    <input type="checkbox" class="form-check-input ui-abn-sync" 
                                                           data-id="{{ $test->id }}"
                                                           {{ (!empty($existing[$test->id]['abnormal'])) ? 'checked' : '' }}>
                                                </td>
                                                <td class="small text-secondary">{{ $test->unit->unit_name ?? '-' }}</td>
                                                <td class="small text-muted">{{ $test->normal_range }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                {{-- Individual Tests --}}
                @if($individualTests->count() > 0)
                    <div class="section-card">
                        <div class="group-header"><span><i class="bi bi-flask me-2"></i>INDIVIDUAL TESTS</span></div>
                        <table class="table align-middle">
                            <tbody>
                                @foreach($individualTests as $test)
                                    <tr>
                                        <td style="width:35%" class="fw-medium">{{ $test->test_name }}</td>
                                        <td style="width:20%">
                                            <input type="text" class="form-control form-control-sm input-result ui-sync" 
                                                   data-id="{{ $test->id }}"
                                                   value="{{ $existing[$test->id]['value'] ?? '' }}">
                                        </td>
                                        <td style="width:10%" class="text-center">
                                            <input type="checkbox" class="form-check-input ui-abn-sync" 
                                                   data-id="{{ $test->id }}"
                                                   {{ (!empty($existing[$test->id]['abnormal'])) ? 'checked' : '' }}>
                                        </td>
                                        <td style="width:15%" class="small text-secondary">{{ $test->unit->unit_name ?? '-' }}</td>
                                        <td style="width:20%" class="small text-muted">{{ $test->normal_range }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="sticky-action-bar">
                    <div class="container-crm d-flex justify-content-end gap-2 px-4">
                        <button type="button" class="btn btn-light border" onclick="history.back()">Cancel</button>
                        <button type="submit" class="btn btn-primary px-5 shadow">Update & Save Results</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('includes.footer_links')

    <script>
        $(function() {
            // Sync Logic
            $(document).on('input', '.ui-sync', function() {
                let id = $(this).data('id');
                let val = $(this).val();
                $('#val_' + id).val(val);
                $(`.ui-sync[data-id="${id}"]`).not(this).val(val);
            });

            $(document).on('change', '.ui-abn-sync', function() {
                let id = $(this).data('id');
                let isChecked = $(this).is(':checked');
                $('#abn_' + id).val(isChecked ? 1 : 0);
                $(`.ui-abn-sync[data-id="${id}"]`).not(this).prop('checked', isChecked);
            });
        });
    </script>
</body>
</html>