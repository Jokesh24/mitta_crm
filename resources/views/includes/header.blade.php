<!-- Start Header Section -->
<div class="header d-flex justify-content-between align-items-center px-4 py-4 shadow-sm">
  <!-- Left Section -->
  <div class="d-flex align-items-center gap-3">
    <i class="bi bi-list fs-4" id="toggleBtn"></i>
    <div class="welcome-text">
      <!-- <span>Welcome to, <span class="welcome-text-highlight">Ca</span></span> -->
      <span>
        Welcome,
        <strong>
          {{ session('admin_name') }} - 
          @if(session('team_role') == 1)
            Admin
          @elseif(session('team_role') == 2)
            Lab Technician
          @else
            User
          @endif
        </strong>
      </span>

    </div>
  </div>


  <!-- Right Section -->
  <div class="d-flex align-items-center gap-4 position-relative">




    <i class="bi bi-gear fs-5"></i>
    <img src="{{ asset('public/img/user-icon.png')}}" alt="User Icon" class="user-icon" />
  </div>
</div>
<!-- End Header Section -->


<style>
  /* ---------- HEADER ---------- */
  .header {
    border-bottom: 1px solid #e5e7eb;
    animation: fadeIn 0.6s ease;
    position: relative;
    z-index: 10;
    background: linear-gradient(90deg, #3a6db0 0%, #6bc5e2 100%);
    border-radius: 20px;
    margin-top: 10px;
    margin-right: 12px;
  }
</style>