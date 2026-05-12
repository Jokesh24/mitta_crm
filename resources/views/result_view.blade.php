<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>View Test Result - OP #{{ $op->id }} - Rich Labz CRM</title>

    @include('includes.header_links')

    <style>
        body {
            background: #f0f2f7;
            font-family: "Inter", sans-serif;
        }

        /* MAIN REPORT BOX */
        .report-box {
            max-width: 960px;
            margin: 25px auto;
            background: #ffffff;
            border-radius: 14px;
            padding: 32px;
            box-shadow: 0px 3px 16px rgba(0, 0, 0, 0.08);
        }

        /* HEADER */
        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 18px;
            border-bottom: 1px solid #e0e0e0;
        }

        .report-header img {
            width: 150px;
        }

        .report-info {
            text-align: right;
            font-size: 14px;
            color: #444;
            line-height: 1.5;
        }

        .main-title {
            text-align: center;
            font-size: 22px;
            font-weight: 700;
            margin-top: 20px;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
            color: #1b2a4e;
            text-transform: uppercase;
        }

        /* PATIENT DETAILS CARD */
        .patient-card {
            background: #fafbff;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #eceefe;
            margin-bottom: 25px;
        }

        .patient-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px 25px;
        }

        .patient-item span {
            font-weight: 600;
            font-size: 14px;
            color: #374151;
            margin-right: 5px;
        }

        .patient-item {
            font-size: 14px;
            color: #525252;
        }

        /* SECTION TITLE (Package/Profile Name) */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            padding: 10px 14px;
            background: #eef2ff;
            border-left: 4px solid #4f46e5;
            border-radius: 4px;
            margin-bottom: 0;
            color: #1e1b4b;
            margin-top: 25px;
            text-transform: uppercase;
        }

        /* TABLE */
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .result-table thead th {
            padding: 12px 10px;
            font-size: 12px;
            font-weight: 700;
            background: #f9fafb;
            color: #64748b;
            text-transform: uppercase;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .result-table td {
            padding: 12px 10px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }

        /* SUB-GROUP HEADING ROW */
        .subgroup-row {
            background-color: #f8fafc;
        }

        .subgroup-row td {
            font-weight: 700;
            font-size: 12px;
            color: #475569;
            padding: 8px 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .abnormal-star {
            color: #dc2626;
            font-weight: 800;
            margin-left: 4px;
        }

        /* BUTTONS */
        .btn-area {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #f1f5f9;
        }

        .download-btn {
            background: #2563eb;
            color: white;
            padding: 10px 24px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .download-btn:hover {
            background: #1d4ed8;
            color: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        @media print {

            .btn-area,
            .sidebar,
            .header {
                display: none !important;
            }

            body {
                background: white;
            }

            .report-box {
                box-shadow: none;
                margin: 0;
                padding: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    @include('includes.sidebar')
    @include('includes.header')

    <div class="content">
        <div class="report-box">

            <div class="report-header">
                <img src="{{ asset('public/img/logo.webp') }}" alt="Lab Logo">
                <div class="report-info">
                    <b style="font-size: 18px; color: #1e293b;">Mitta Excellence Pvt. Ltd.</b><br>
                    LB Nagar Metro Station, Hyderabad <br>
                    Contact: 9447766999, 040-34100391 <br>
                    Email: info@mittaexcellence.com
                </div>
            </div>

            <div class="main-title">Diagnostic Report</div>

            <div class="patient-card">
                <div class="patient-grid">
                    <div class="patient-item">
                        <span>Patient Name :</span> {{ $header->patient->patient_name }}
                    </div>
                    <div class="patient-item">
                        <span>Age / Gender :</span> {{ $header->patient->age }}Y / {{ $header->patient->gender }}
                    </div>
                    <div class="patient-item">
                        <span>OP Reg No :</span> {{ $op->op_reg_no ?? 'N/A' }}
                    </div>
                    <div class="patient-item">
                        <span>Patient Code :</span> {{ $op->patient_code ?? 'N/A' }}
                    </div>
                    <div class="patient-item">
                        <span>Collected On :</span> {{ $header->created_at->format('d-m-Y h:i A') }}
                    </div>
                    <div class="patient-item">
                        <span>Reported On :</span> {{ $header->updated_at->format('d-m-Y h:i A') }}
                    </div>
                </div>
            </div>

            @if(isset($resultGroups) && count($resultGroups) > 0)
                @foreach($resultGroups as $group)

                    <div class="section-title">
                        {{ $group['title'] }}
                    </div>

                    <table class="result-table">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Test Parameter</th>
                                <th style="width: 20%;">Result</th>
                                <th style="width: 15%;">Units</th>
                                <th style="width: 25%;">Reference Range</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($group['subgroups'] as $sub)
                                {{-- Sub-test Heading (like "Liver Enzymes") --}}
                                @if($sub['group_name'])
                                    <tr class="subgroup-row">
                                        <td colspan="4">
                                            <i class="bi bi-arrow-return-right me-1"></i> {{ strtoupper($sub['group_name']) }}
                                        </td>
                                    </tr>
                                @endif

                                @foreach($sub['results'] as $row)
                                    <tr>
                                        <td class="{{ $sub['group_name'] ? 'ps-4' : '' }}">
                                            {{ $row->test->test_name }}
                                        </td>
                                        <td style="font-weight: 700;">
                                            {{ $row->result_value }}
                                            @if($row->is_abnormal)
                                                <span class="abnormal-star">*</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->unit->unit_name ?? '-' }}</td>
                                        <td style="color: #64748b; font-size: 13px;">{{ $row->normal_range }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                @endforeach
            @else
                <div style="text-align:center; padding: 40px; color: #64748b;">
                    <i class="bi bi-exclamation-circle" style="font-size: 40px;"></i>
                    <p class="mt-2">No results found for this registration.</p>
                </div>
            @endif

            <div class="btn-area">
                <a href="javascript:window.print()" class="btn btn-outline-primary px-4">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="{{ route('download-result', $header->id) }}" class="download-btn">
                    <i class="bi bi-file-earmark-pdf"></i> Download PDF Report
                </a>
            </div>

        </div>
    </div>

    @include('includes.footer_links')

</body>

</html>