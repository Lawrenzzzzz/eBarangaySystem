<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments - E-Barangay Official</title>
  
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
      <a class="nav-link" href="residents.php">
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA PAYMENTS -->
      <a class="nav-link active" href="payments.php">
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
          <h5>Payments</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="sys-live"><span class="dot"></span> System Live</span>
        <a href="#" class="btn btn-orange btn-sm px-3">
          <i class="bi bi-plus-lg me-1"></i> New Request
        </a>
      </div>
    </div>

    <div class="eb-content">
      <!-- STATS CARDS -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Total Collected</div>
            <div class="stats-value">₱0</div>
            <div class="stats-sub text-muted">0 this month</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Pending</div>
            <div class="stats-value text-warning">₱0</div>
            <div class="stats-sub text-muted">0 transactions</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Free Services</div>
            <div class="stats-value">0</div>
            <div class="stats-sub text-muted">0 certificates</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Transactions</div>
            <div class="stats-value">0</div>
            <div class="stats-sub text-muted">this week</div>
          </div>
        </div>
      </div>

      <!-- PAYMENTS TABLE -->
      <div class="table-card">
        <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
          <h6 class="mb-0 fw-bold">Payment Transactions</h6>
          <button class="btn btn-light border fw-semibold btn-sm">
            <i class="bi bi-download me-1"></i> Export
          </button>
        </div>
        
        <div class="table-responsive">
          <table class="payments-table">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Payee</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- blank rows i2 -->
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td></tr>
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