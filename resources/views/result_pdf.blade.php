<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <title>Laboratory Report</title>

    <style>
        @page {
            margin-top: 250px;
            margin-bottom: 140px;
            margin-left: 20px;
            margin-right: 20px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* HEADER */

        #header {
            position: fixed;
            top: -220px;
            left: 0;
            right: 0;
            height: 200px;
        }

        /* FOOTER */

        #footer {
            position: fixed;
            bottom: -120px;
            left: 0;
            right: 0;
            height: 100px;
        }

        /* LOGO SECTION */

        .top-table {
            width: 100%;
            border-bottom: 2px solid #0c4890;
            padding-bottom: 10px;
        }

        .labname {
            font-size: 22px;
            font-weight: bold;
            color: #0c4890;
        }

        /* TITLE */

        .report-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        /* PATIENT INFO */

        .patient-table {
            width: 100%;
            border-collapse: collapse;
        }

        .patient-table td {
            padding: 3px 5px;
            vertical-align: top;
            font-size: 12px;
        }

        /* SECTION */

        .section-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px solid #0c4890;
            padding-bottom: 5px;
            margin-top: 10px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        /* TEST TABLE */

        .test-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .test-table th {
            background: #0c4890;
            color: white;
            padding: 8px;
            font-size: 12px;
            text-align: left;
        }

        .test-table td {
            padding: 7px;
            border-bottom: 1px solid #ccc;
            font-size: 12px;
            word-break: break-word;
            vertical-align: top;
        }

        .subgroup-header td {
            background: #f2f2f2;
            font-weight: bold;
            font-size: 12px;
        }

        /* FOOTER */

        .footer-images {
            width: 100%;
        }

        .footer-bg {
            background: #0c4890;
            color: white;
            text-align: center;
            padding: 8px;
            font-size: 12px;
            margin-top: 5px;
        }

        /* PAGE BREAK */

        .page-break {
            page-break-before: always;
        }

        tr {
            page-break-inside: avoid;
        }

        table {
            page-break-inside: auto;
        }
    </style>
</head>

<body>

    <!-- HEADER -->

    <div id="header">

        <table class="top-table">

            <tr>

                <td width="40%">
                    <img src="{{ public_path('img/logo.webp') }}" height="70">
                </td>

                <td width="60%" align="right">

                    <span class="labname">
                        MITTA EXCELLENCE PVT. LTD.
                    </span>

                    <br>

                    LB NAGAR METRO STATION, HYDERABAD

                    <br>

                    CONTACT NO: 9441766999, 040 - 31000391

                    <br>

                    Email: info@mittaexcellence.com

                </td>

            </tr>

        </table>

        <div class="report-title">
            LABORATORY REPORT
        </div>

        <table class="patient-table">

            <tr>

                <td width="50%">

                    <b>Patient Name:</b>
                    {{ $header->patient->patient_name }}

                    <br>

                    <b>Age/Gender:</b>
                    {{ $header->patient->age }}Y /
                    {{ $header->patient->gender }}

                    <br>

                    <b>OP Reg No:</b>
                    {{ $op->op_reg_no ?? '—' }}

                    <br>

                    <b>Patient Code:</b>
                    {{ $op->patient_code ?? '—' }}

                </td>

                <td width="50%">

                    <b>Req. Date:</b>
                    {{ $header->created_at->format('d-m-Y h:i A') }}

                    <br>

                    <b>Reported On:</b>

                    {{ $header->updated_at
    ? $header->updated_at->format('d-m-Y h:i A')
    : '—'
                    }}

                    <br>

                    <b>Printed Date:</b>
                    {{ now()->format('d-m-Y h:i A') }}

                </td>

            </tr>

        </table>

    </div>

    <!-- FOOTER -->

    <div id="footer">

        <table class="footer-images">

            <tr>

                <td width="25%">
                    <img src="{{ public_path('img/iso.png') }}" height="60">
                </td>

                <td width="25%">
                    <img src="{{ public_path('img/gmp.png') }}" height="60">
                </td>

                <td width="25%" align="center">
                    <img src="{{ public_path('img/qrcode.png') }}" height="60">
                </td>

                <td width="25%" align="right">
                    <img src="{{ public_path('img/sign.jpg') }}" height="70">
                </td>

            </tr>

        </table>

        <div class="footer-bg">

            <strong>Contact:</strong>

            9441766999 |

            040 - 31000391 |

            info@mittaexcellence.com

        </div>

    </div>

    <!-- CONTENT -->

    <div id="content">

        @if(isset($resultGroups) && count($resultGroups) > 0)

            @foreach($resultGroups as $index => $group)

                <div class="{{ $index > 0 ? 'page-break' : '' }}">

                    <div class="section-title">
                        {{ $group['title'] }}
                    </div>

                    <table class="test-table">

                        <thead>

                            <tr>

                                <th width="40%">
                                    Test Name
                                </th>

                                <th width="20%">
                                    Result
                                </th>

                                <th width="15%">
                                    Units
                                </th>

                                <th width="25%">
                                    Reference Range
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($group['subgroups'] as $sub)

                                @if(!empty($sub['group_name']))

                                    <tr class="subgroup-header">

                                        <td colspan="4">

                                            {{ strtoupper($sub['group_name']) }}

                                        </td>

                                    </tr>

                                @endif

                                @foreach($sub['results'] as $item)

                                    <tr>

                                        <td>

                                            {{ $item->test->test_name }}

                                        </td>

                                        <td>

                                            <b>
                                                {{ $item->result_value }}
                                            </b>

                                            @if($item->is_abnormal)

                                                <span style="color:red;">
                                                    *
                                                </span>

                                            @endif

                                        </td>

                                        <td>

                                            {{ $item->unit->unit_name ?? '—' }}

                                        </td>

                                        <td>

                                            {{ $item->normal_range ?? '—' }}

                                        </td>

                                    </tr>

                                @endforeach

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endforeach

        @endif

    </div>

</body>

</html>