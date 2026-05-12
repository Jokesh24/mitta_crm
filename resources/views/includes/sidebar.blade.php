<div class="overlay" id="overlay"></div>

<!-- Start Sidebar Section -->
<div class="sidebar">

@php
    $userType = Session::get('user_type'); // admin | team
    $teamRole = Session::get('team_role'); // 1 = Admin, 2 = Lab Tech
@endphp


  <!-- LOGO -->
  <div class="sidebar-logo">
    <a href="index.php">
      <img src="{{ asset('public/img/logo.webp')}}" alt="Logo">
    </a>
  </div>

  <div class="sidebar-content">

    <!-- Dashboard -->
    <a href="{{ route('dashboard') }}" class="nav-link" id="dashboard-link">
      <i class="bi bi-grid-3x3-gap"></i>
      <span>Dashboard</span>
    </a>

    @if($teamRole == 1)
    <!-- MASTER DATA -->
    <a href="#submenu-master" class="nav-link dropdown-toggle" data-submenu="submenu-master" id="master-link">
      <i class="bi bi-folder"></i>
      <span>Master Data</span>
    </a>

    <div class="submenu" id="submenu-master">

      <a href="{{ route('manage-units') }}" class="nav-link" id="units-link">
        <span>Units</span>
      </a>

      <a href="{{ route('manage-tests') }}" class="nav-link" id="tests-link">
        <span>Parameter's</span>
      </a>

      <a href="{{ route('manage-test-new') }}" class="nav-link" id="tests-link">
        <span>Test's</span>
      </a>

      <a href="{{ route('manage-package') }}" class="nav-link" id="package-link">
        <span>Packages</span>
      </a>

      <a href="{{ route('manage-locations') }}" class="nav-link" id="location-link">
        <span>Locations</span>
      </a>

      <a href="{{ route('manage-branch') }}" class="nav-link" id="branch-link">
        <span>Branches</span>
      </a>

      <a href="{{ route('manage-company') }}" class="nav-link" id="company-link">
        <span>Companies</span>
      </a>
    </div>
    @endif

    <!-- OP Registration -->
    <a href="#submenu-op" class="nav-link dropdown-toggle" data-submenu="submenu-op" id="op-link">
      <i class="bi bi-person-plus"></i>
      <span>OP Registration</span>
    </a>

    <div class="submenu" id="submenu-op">
      <a href="{{ route('add-op') }}" class="nav-link" id="op-add-link"><span>Add OP</span></a>
      <a href="{{ route('manage-op') }}" class="nav-link" id="op-manage-link"><span>Manage OP</span></a>
    </div>

    <!-- RESULT ENTRY -->
    <a href="#submenu-result" class="nav-link dropdown-toggle" data-submenu="submenu-result" id="result-link">
      <i class="bi bi-journal-text"></i>
      <span>Result Entry</span>
    </a>

    <div class="submenu" id="submenu-result">
      <a href="{{ route('manage-pending_ops') }}" class="nav-link" id="result-add-link"><span>Pending Op's</span></a>
      <!-- <a href="{{ route('add-result') }}" class="nav-link" id="result-add-link"><span>Add Result</span></a> -->
      <a href="{{ route('manage-result') }}" class="nav-link" id="result-manage-link"><span>Manage Results</span></a>
    </div>

    @if($teamRole == 1)
    <!-- TEAM -->
    <a href="#submenu-team" class="nav-link dropdown-toggle" data-submenu="submenu-team" id="team-main-link">
      <i class="bi bi-people"></i>
      <span>Team</span>
    </a>

    <div class="submenu" id="submenu-team">
      <a href="{{ route('add-team') }}" class="nav-link" id="team-add-link"><span>Add Member</span></a>
      <a href="{{ route('manage-team') }}" class="nav-link" id="team-manage-link"><span>Manage Team</span></a>
    </div>
    @endif

    <!-- REPORTS -->
    <!-- <a href="#submenu-reports" class="nav-link dropdown-toggle" data-submenu="submenu-reports" id="reports-link">
      <i class="bi bi-bar-chart"></i>
      <span>Reports</span>
    </a>

    <div class="submenu" id="submenu-reports">
      <a href="reports_add.php" class="nav-link" id="report-add-link"><span>Add Report</span></a>
      <a href="reports.php" class="nav-link" id="report-manage-link"><span>Manage Reports</span></a>
    </div> -->

    <!-- CHANGE PASSWORD -->
    <a href="{{ route('change-password') }}" class="nav-link" id="change-password-link">
      <i class="bi bi-key"></i>
      <span>Change Password</span>
    </a>

    <!-- LOGOUT -->
    <a href="{{ route('logout') }}" class="nav-link" id="logout-link">
      <i class="bi bi-box-arrow-right"></i>
      <span>Logout</span>
    </a>

  </div>
</div>
<!-- End Sidebar Section -->

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* -------------------------------
     AUTO-CLOSE OTHER SUBMENUS
  ------------------------------- */
  const dropdowns = document.querySelectorAll(".dropdown-toggle");

  dropdowns.forEach(dropdown => {
    dropdown.addEventListener("click", function(e) {
      e.preventDefault();
      const submenu = this.nextElementSibling;
      if (!submenu) return;

      const isActive = this.classList.contains("active");

      /* Close all other open submenus */
      document.querySelectorAll(".dropdown-toggle.active").forEach(active => {
        if (active !== this) {
          active.classList.remove("active");
          const otherMenu = active.nextElementSibling;
          otherMenu.style.maxHeight = "0px";
          otherMenu.classList.remove("show");
        }
      });

      /* Toggle the clicked submenu */
      if (!isActive) {
        submenu.classList.add("show");
        submenu.style.maxHeight = submenu.scrollHeight + "px";
        this.classList.add("active");
      } else {
        submenu.style.maxHeight = "0px";
        submenu.classList.remove("show");
        this.classList.remove("active");
      }
    });
  });


  const currentPage = window.location.pathname.split('/').pop() || "index.php";

  const pageLinks = {

    /* DASHBOARD */
    "index.php": "dashboard-link",

    /* MASTER */
    "units.php": "units-link",
    "add_test.php": "tests-link",
    "location.php": "location-link",
    "branch.php": "branch-link",
    "company.php": "company-link",

    /* OP */
    "add_op.php": "op-add-link",
    "manage_op.php": "op-manage-link",

    /* RESULTS */
    "result_entry_add.php": "result-add-link",
    "result_entry.php": "result-manage-link",

    /* TEAM */
    "add_team.php": "team-add-link",
    "manage_team.php": "team-manage-link",

    /* REPORTS */
    "reports_add.php": "report-add-link",
    "reports.php": "report-manage-link",

    /* PASSWORD + LOGOUT */
    "change_password.php": "change-password-link",
    "login.php": "logout-link"
  };

  const dropdownLinks = {

    /* MASTER */
    "units.php": "master-link",
    "add_test.php": "master-link",
    "location.php": "master-link",
    "branch.php": "master-link",
    "company.php": "master-link",

    /* OP */
    "add_op.php": "op-link",
    "manage_op.php": "op-link",

    /* RESULTS */
    "result_entry_add.php": "result-link",
    "result_entry.php": "result-link",

    /* TEAM */
    "add_team.php": "team-main-link",
    "manage_team.php": "team-main-link",

    /* REPORTS */
    "reports_add.php": "reports-link",
    "reports.php": "reports-link"
  };

  /* Highlight active link */
  if (pageLinks[currentPage]) {
    document.getElementById(pageLinks[currentPage]).classList.add("active");
  }

  /* Open correct submenu */
  if (dropdownLinks[currentPage]) {
    let parent = document.getElementById(dropdownLinks[currentPage]);
    parent.classList.add("active");

    let submenu = parent.nextElementSibling;
    submenu.classList.add("show");
    submenu.style.maxHeight = submenu.scrollHeight + "px";
  }

});
</script>
