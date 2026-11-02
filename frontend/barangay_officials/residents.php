<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Residents - E-Barangay Official</title>
  
  <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="css/ebarangay.css">
</head>
<body>

  <div class="eb-sidebar" id="ebSidebar">
    <div class="brand">
      <div class="brand-logo">M</div>
      <div class="brand-text">
        E-Barangay
        <small>Municipal Admin</small>
      </div>
    </div>
    
    <div class="nav flex-column py-2 flex-grow-1">
      <div class="nav-section">Main</div>
      <a class="nav-link" href="dashboard.php">
        <span class="nav-dot"></span><span>Dashboard</span>
      </a>
      
      <!-- ACTIVE HIGHLIGHT I2 SA RESIDENTS -->
      <a class="nav-link active" href="residents.php">
        <span class="nav-dot"></span><span>Residents</span>
      </a>
      
      <a class="nav-link" href="document-requests.php">
        <span class="nav-dot"></span><span>Document Requests</span>
      </a>
      <a class="nav-link" href="blotter.php">
        <span class="nav-dot"></span><span>Blotter</span>
      </a>
      <a class="nav-link" href="announcements.php">
        <span class="nav-dot"></span><span>Announcements</span>
      </a>
      <a class="nav-link" href="payments.php">
        <span class="nav-dot"></span><span>Payments</span>
      </a>
      <a class="nav-link" href="reports.php">
        <span class="nav-dot"></span><span>Reports</span>
      </a>
      <a class="nav-link" href="settings.php">
        <span class="nav-dot"></span><span>Settings</span>
      </a>
    </div>
    
    <div class="user-profile">
      <img src="img/captain.jpg" alt="Captain" class="avatar">
      <div>
        <div class="name">Capt. Choi Seungcheol Batumbakal</div>
        <div class="role">Barangay Captain of Bucana</div>
      </div>
    </div>
  </div>

  <div class="eb-main">
    <div class="eb-topbar">
      <div class="d-flex align-items-center gap-3">
        <div class="page-title">
          <h5>Residents</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="sys-live"><span class="dot"></span> System Live</span>
      </div>
    </div>

    <div class="eb-content">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div class="search-box">
          <i class="bi bi-search"></i>
          <input type="text" placeholder="Search residents...">
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-light border fw-semibold">
            <i class="bi bi-download me-1"></i> Export
          </button>
          <button class="btn btn-orange fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Add Resident
          </button>
        </div>
      </div>
      
      <div class="table-card">
        <!-- table-scrollbar -->
        <div class="table-responsive table-scrollable">
          <table class="residents-table">
            <thead>
              <tr>
                <th>Resident</th>
                <th>Purok / Sitio</th>
                <th>Household</th>
                <th>Registered</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- rows i2 -->
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>