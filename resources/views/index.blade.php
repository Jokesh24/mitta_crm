<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mitta Excellence Dashboard</title>

  @include('includes.header_links')

  <style>
    .project-card {
      background: #ffffff;
      border-radius: 25px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05), 0 8px 24px rgba(0, 0, 0, 0.05);
      border: 1px solid #edf0f3;
      padding: 22px 24px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
      margin-bottom: 20px;
    }

    .project-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .icon-container {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(145deg, #f1f3f7, #ffffff);
      box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .icon-container i {
      font-size: 1.4rem;
    }

    .card-header h5 {
      margin: 0;
      font-size: 18px;
      font-weight: 700;
      color: #333;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .card-header .total {
      font-size: 22px;
      font-weight: 800;
      color: #222;
      padding: 6px 14px;
      border-radius: 20px;
      background: #f2f5f9;
      min-width: 60px;
      text-align: center;
      user-select: none;
    }

    .card-footer {
      margin-top: 14px;
      display: flex;
      justify-content: space-between;
      font-weight: 500;
      color: #666;
      font-size: 15px;
    }

    .value.green {
      color: #1aa260;
      font-weight: 600;
    }

    .value.red {
      color: #dc3545;
      font-weight: 600;
    }

    .card-admin .icon-container i {
      color: #6c63ff;
    }

    .card-op .icon-container i {
      color: #17a2b8;
    }

    .card-result .icon-container i {
      color: #ff4d4d;
    }

    .card-pdf .icon-container i {
      color: #f5428d;
    }

    .card-whatsapp .icon-container i {
      color: #25D366;
    }

    .card-reports .icon-container i {
      color: #ff9800;
    }

    .dashboard-lower-section {
      display: flex;
      gap: 30px;
      flex-wrap: wrap;
      margin-top: 30px;
    }

    .chart-container {
      flex: 1 1 48%;
      min-width: 320px;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
      padding: 24px;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
    }

    .chart-container h5 {
      margin-bottom: 16px;
      font-weight: 700;
      font-size: 1.2rem;
      color: #222;
    }

    canvas {
      width: 100% !important;
      height: 280px !important;
    }
  </style>

</head>

<body>

  @include('includes.sidebar')
  @include('includes.header')

  <div class="content">

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

    <div class="row">

      <!-- COMPANIES -->
      <div class="col-md-4">
        <div class="project-card card-admin">
          <div class="card-header">
            <h5><span class="icon-container"><i class="bi bi-building-fill"></i></span> Companies</h5>
            <span class="total">{{ $companies }}</span>
          </div>
          <div class="card-footer">
            <div>Projects / Branches</div>
            <div>Employees / Locations</div>
          </div>
        </div>
      </div>

      <!-- OP REGISTRATION -->
      <div class="col-md-4">
        <div class="project-card card-op">
          <div class="card-header">
            <h5><span class="icon-container"><i class="bi bi-ui-checks-grid"></i></span> OP Registrations</h5>
            <span class="total">{{ $op_this_month }}</span>
          </div>
          <div class="card-footer">
            <div>Today: <span class="value green">{{ $op_today }}</span></div>
            <div>This Month: <span class="value green">{{ $op_this_month }}</span></div>
          </div>
        </div>
      </div>

      <!-- RESULT ENTRY -->
      <div class="col-md-4">
        <div class="project-card card-result">
          <div class="card-header">
            <h5><span class="icon-container"><i class="bi bi-file-earmark-medical-fill"></i></span> Result Entry</h5>
            <span class="total">{{ $results_total }}</span>
          </div>
          <div class="card-footer">
            <div>Completed: <span class="value green">{{ $results_completed }}</span></div>
            <div>Pending: <span class="value red">{{ $results_pending }}</span></div>
          </div>
        </div>
      </div>

      <!-- EMPLOYEES -->
      <div class="col-md-4">
        <div class="project-card card-pdf">
          <div class="card-header">
            <h5><span class="icon-container"><i class="bi bi-people-fill"></i></span> No. of Employees</h5>
            <span class="total">{{ $employees_total }}</span>
          </div>
          <div class="card-footer">
            <div>Active: <span class="value green">{{ $employees_active }}</span></div>
            <div>Inactive: <span class="value red">{{ $employees_inactive }}</span></div>
          </div>
        </div>
      </div>

      <!-- WHATSAPP DELIVERY -->
      <div class="col-md-4">
        <div class="project-card card-whatsapp">
          <div class="card-header">
            <h5><span class="icon-container"><i class="bi bi-whatsapp"></i></span> WhatsApp Delivery</h5>
            <span class="total">{{ $whatsapp_delivered + $whatsapp_failed }}</span>
          </div>
          <div class="card-footer">
            <div>Delivered: <span class="value green">{{ $whatsapp_delivered }}</span></div>
            <div>Failed: <span class="value red">{{ $whatsapp_failed }}</span></div>
          </div>
        </div>
      </div>

      <!-- TOTAL TESTS -->
      <div class="col-md-4">
        <div class="project-card card-reports">
          <div class="card-header">
            <h5><span class="icon-container"><i class="bi bi-clipboard2-pulse-fill"></i></span> Total Tests</h5>
            <span class="total">{{ $tests_total }}</span>
          </div>
          <div class="card-footer">
            <div>Available Tests: <span class="value green">{{ $tests_total }}</span></div>
          </div>
        </div>
      </div>

    </div>

    <!-- LOWER SECTION -->
    <div class="dashboard-lower-section">

      <!-- MONTHLY COLLECTION CHART -->
      <div class="chart-container">
        <h5>Monthly Sample Collection</h5>
        <canvas id="sampleChart"></canvas>
      </div>

      <!-- RESULT COMPLETION CHART -->
      <div class="chart-container">
        <h5>Result Completion Analytics</h5>
        <canvas id="resultChart"></canvas>
      </div>

    </div>

  </div>

  @include('includes.footer_links')

  <script>
    new Chart(document.getElementById('sampleChart'), {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
          label: 'Samples',
          data: @json($op_chart_data),
          borderColor: '#6c63ff',
          backgroundColor: 'rgba(108,99,255,0.08)',
          fill: true,
          borderWidth: 3,
          tension: 0.4
        }]
      }
    });

    new Chart(document.getElementById('resultChart'), {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
          {
            label: 'Completed',
            data: @json($completed_chart_data),
            backgroundColor: '#1aa260'
          },
          {
            label: 'Pending',
            data: @json($pending_chart_data),
            backgroundColor: '#f54242'
          }
        ]
      }
    });

  </script>

</body>

</html>