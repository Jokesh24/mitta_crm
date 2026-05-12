<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Mitta Excellence - Laboratory Report</title>

    <style>
        @page {
            size: A4;
            margin: 12mm 12mm 12mm 12mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        /* HEADER TABLE */
        .header-table {
            width: 100%;
            border-bottom: 3px double #0c4890;
            margin-bottom: 15px;
        }

        .labname {
            font-size: 20px;
            font-weight: bold;
        }

        /* SECTION TITLE */
        .section-title {
            font-size: 17px;
            font-weight: bold;
            border-bottom: 2px solid #0c4890;
            padding-bottom: 4px;
            margin-top: 18px;
            page-break-after: avoid;
        }

        /* TEST TABLE */
        table.test-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        table.test-table th {
            background: #0c4890;
            color: white;
            padding: 8px;
            text-align: left;
        }

        table.test-table td {
            padding: 8px;
            border-bottom: 1px solid #777;
            word-wrap: break-word;
        }

        /* SUB-GROUP STYLE */
        .subgroup-header {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 12px;
            color: #333;
        }

        /* FOOTER FIXED */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 8mm;
            background: #0c4890;
            color: white;
            text-align: center;
            padding-top: 10px;
            font-size: 13px;
        }
    </style>
</head>

<body>

    <table class="header-table">
        <tr>
            <td width="40%">
                <img src="{{ public_path('img/logo.webp') }}" height="70">
            </td>
            <td width="60%" align="right">
                <span class="labname">MITTA EXCELLENCE PVT. LTD.</span><br>
                LB NAGAR METRO STATION , HYDERABAD<br>
                CONTACT NO : 9441766999, 040 - 31000391<br>
                Email Id : info@mittaexcellence.com
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td align="center" style="font-size:22px; font-weight:bold; padding:8px 0;">
                LABORATORY REPORT
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="6" style="margin-bottom:15px;">
        <tr>
            <td width="50%" valign="top">
                <table width="100%">
                    <tr>
                        <td><b>Patient Name:</b> {{ $header->patient->patient_name }}</td>
                    </tr>
                    <tr>
                        <td><b>Age/Gender:</b> {{ $header->patient->age }}Y / {{ $header->patient->gender }}</td>
                    </tr>
                    <tr>
                        <td><b>OP Reg No:</b> {{ $op->op_reg_no ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td><b>Patient Code:</b> {{ $op->patient_code ?? '—' }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%" valign="top">
                <table width="100%">
                    <tr>
                        <td><b>Req. Date:</b> {{ $header->created_at->format('d-m-Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td><b>Reported On:</b>
                            {{ $header->updated_at ? $header->updated_at->format('d-m-Y h:i A') : '—' }}</td>
                    </tr>
                    <tr>
                        <td><b>Printed Date:</b> {{ now()->format('d-m-Y h:i A') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if(isset($resultGroups) && count($resultGroups) > 0)

        @foreach($resultGroups as $group)

            <div class="section-title">
                {{ strtoupper($group['title']) }}
            </div>

            <table class="test-table">
                <thead>
                    <tr>
                        <th width="40%">Test Name</th>
                        <th width="20%">Result</th>
                        <th width="15%">Units</th>
                        <th width="25%">Reference Range</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group['subgroups'] as $sub)
                        {{-- SUB-HEADER ROW --}}
                        @if(!empty($sub['group_name']))
                            <tr class="subgroup-header">
                                <td colspan="4" style="padding-left: 10px;">
                                    {{ strtoupper($sub['group_name']) }}
                                </td>
                            </tr>
                        @endif

                        @foreach($sub['results'] as $item)
                            <tr>
                                <td style="{{ !empty($sub['group_name']) ? 'padding-left: 20px;' : '' }}">
                                    {{ $item->test->test_name }}
                                </td>
                                <td>
                                    <b>{{ $item->result_value }}</b>
                                    @if($item->is_abnormal)
                                        <span style="font-size:16px; color:red;">*</span>
                                    @endif
                                </td>
                                <td>{{ $item->unit->unit_name ?? '—' }}</td>
                                <td>{{ $item->normal_range ?? '—' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>

        @endforeach

    @else
        <div class="section-title">Test Results</div>
        <table class="test-table">
            <thead>
                <tr>
                    <th width="40%">Test Name</th>
                    <th width="20%">Result</th>
                    <th width="15%">Units</th>
                    <th width="25%">Reference Range</th>
                </tr>
            </thead>
            <tbody>
                @foreach($header->results as $item)
                    <tr>
                        <td>{{ $item->test->test_name }}</td>
                        <td>
                            <b>{{ $item->result_value }}</b>
                            @if($item->is_abnormal)
                                <span style="font-size:16px; color:red;">*</span>
                            @endif
                        </td>
                        <td>{{ $item->unit->unit_name ?? '—' }}</td>
                        <td>{{ $item->normal_range ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <table width="100%" style="margin-top:35px; margin-bottom: 50px;">
        <tr>
            <td width="25%">
                <img src="{{ public_path('img/iso.png') }}" height="110">
            </td>
            <td width="25%">
                <img src="{{ public_path('img/gmp.png') }}" height="110">
            </td>
            <td width="25%" align="center">
                <img src="{{ public_path('img/qrcode.png') }}" height="110">
            </td>
            <td width="25%" align="right">
                <img src="{{ public_path('img/sign.jpg') }}" height="120">
            </td>
        </tr>
    </table>

    <div class="footer">
        <strong>Contact:</strong> 9441766999 | 040 - 31000391 |
        Email: info@mittaexcellence.com
    </div>

</body>

</html>