<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Mitta Excellence - Laboratory Report</title>
<style>
    @page { size: A4; margin: 10mm 12mm; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 13.8px;
        line-height: 1.42;
        color: #222324ff;
        background: white;
    }
    .container {
        width: 210mm;
        min-height: 297mm;
        margin: 0 auto;
        border: 1px solid #0c4890;
        padding: 22px 25px 90px 25px;
        position: relative;
        background: white;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px double #0c4890;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }
    .header img { height: 68px; }
    .labinfo { text-align: right; font-size: 13px; }
    .labname { font-size: 20px; font-weight: bold; letter-spacing: 1px; }

    h1 {
        text-align: center;
        font-size: 25px;
        font-weight: bold;
        text-transform: uppercase;
        margin: 14px 0 18px;
        letter-spacing: 1.2px;
    }

    .pbox {
        border: 1px solid #0c4890;
        padding: 12px 15px;
        background: #f9f9f9;
        margin-bottom: 18px;
    }
    .pgrid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap: 6px 22px; }
    .pitem span:first-child { font-weight: bold; display: inline-block; width: 120px; }

    .sect {
        font-size: 18px;
        font-weight: bold;
        text-transform: uppercase;
        border-bottom: 2.5px solid #0c4890;
        padding-bottom: 5px;
        margin: 22px 0 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        font-size: 14.2px;
    }
    th {
        background: #0c4890;
        color: white;
        padding: 10px;
        text-align: left;
        font-weight: bold;
    }
    td { padding: 9px 10px; border-bottom: 1px solid #444; }
    tbody tr:nth-child(even) { background: #f2f2f2; }
    .bold { font-weight: bold; }

    /* Watermark */
    .watermark {
        position: absolute;
        top: 52%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-28deg);
        width: 520px;
        opacity: 0.07;
        pointer-events: none;
    }

    /* QR Code - positioned exactly above footer centered */
    .qr-code {
        position: absolute;
        bottom: 55px; /* just above footer strip */
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
    }
    .qr-code img {
        width: 110px;
        height: 110px;
        object-fit: contain;
    }

    /* Footer strip */
    .bottom-strip {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 45px;
        background: #0c4890;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border-top: 4px double #0c4890;
        padding-right: 60px; /* space for footer image */
    }

    /* Footer right image */
    .footer-img {
        position: absolute;
        bottom: 50px;
        right: 10px;
    }
    .footer-img img {
        height: 140px; /* adjust as needed */
    }

    @media print {
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bottom-strip { background: #0c4890 !important; color: white !important; }
    }
</style>
</head>
<body>

<div class="container">

    <!-- Watermark -->
    <div class="watermark">
        <img src="img/logo.webp" style="width:100%;">
    </div>

    <!-- Header -->
    <div class="header">
        <img src="img/logo.webp" alt="Mitta Excellence">
        <div class="labinfo">
            <div class="labname">MITTA EXCELLENCE PVT. LTD.</div>
            LB Nagar Metro Station, Hyderabad
        </div>
    </div>

    <h1>Laboratory Report</h1>

    <div class="pbox">
        <div class="pgrid">
            <div class="pitem"><span>Patient Name</span> : Mr. ISMAIL C</div>
            <div class="pitem"><span>Age / Gender</span> : 29Y / Male</div>
            <div class="pitem"><span>OP Reg No.</span> : OP12700003293</div>
            <div class="pitem"><span>Lab Order No.</span> : 20521127-1256</div>
            <div class="pitem"><span>Req. Date</span> : 27-11-2025 02:47 AM</div>
            <div class="pitem"><span>Reported On</span> : 27-11-2025 02:59 AM</div>
        </div>
    </div>

    <div class="sect">Blood / BMI - GREEN CO</div>

    <table>
        <thead>
            <tr>
                <th>Test Name</th>
                <th>Result</th>
                <th>Units</th>
                <th>Reference Range</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>BLOOD PRESSURE</td><td class="bold">116/77</td><td>mmHg</td><td>—</td></tr>
            <tr><td>RANDOM BLOOD SUGAR</td><td class="bold">132</td><td>mg/dl</td><td>80 - 140</td></tr>
            <tr><td>Body Weight</td><td class="bold">68</td><td>kg</td><td>—</td></tr>
            <tr><td>PULSE</td><td class="bold">59</td><td>/min</td><td>—</td></tr>
            <tr><td>BODY TEMPERATURE</td><td class="bold">98.5</td><td>°F</td><td>—</td></tr>
            <tr><td>HIV</td><td class="bold">NEGATIVE</td><td>—</td><td>NEGATIVE</td></tr>
            <tr><td>HEARING TEST</td><td class="bold">POSITIVE</td><td>—</td><td>NEGATIVE</td></tr>
            <tr><td>VERTIGO TEST</td><td class="bold">NEGATIVE</td><td>—</td><td>NEGATIVE</td></tr>
            <tr><td>VISION TEST (Left)</td><td class="bold">6/6</td><td>—</td><td>6/6</td></tr>
            <tr><td>VISION TEST (Right)</td><td class="bold">6/6</td><td>—</td><td>6/6</td></tr>
        </tbody>
    </table>

    <!-- QR Code in the MIDDLE ABOVE FOOTER -->
    <div class="qr-code">
        <img src="img/qrcode.png" alt="QR Code">
    </div>

    <!-- Footer -->
    <div class="bottom-strip">
        <strong>Contact:</strong> 94477 66999 | 040-34100391 | Email: info@mittaexcellence.com
    </div>

    <!-- Footer Right Image -->
    <div class="footer-img">
        <img src="img/sign.jpeg" alt="">
    </div>

</div>
</body>
</html>
