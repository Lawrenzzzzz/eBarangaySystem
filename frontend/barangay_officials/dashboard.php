<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - E-Barangay Official</title>
  
  <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="css/ebarangay.css">
  <style>
    /* Dashboard layout */
    .table-card { padding: 1.5rem; }
    .table-card .queue-item { padding: 1.15rem 0; border-bottom: 1px solid var(--eb-border); }
    .table-card .queue-item:last-child { border-bottom: none; }
    .quick-actions .btn { justify-content: flex-start; padding: .7rem 1rem; text-align: left; font-size: .83rem; border-radius: 7px; }
    .quick-actions .btn:hover { background: #fffbeb; border-color: var(--eb-sidebar-active); }
  </style>
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
      <a class="nav-link active" href="dashboard.php">
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
        <button class="btn btn-light d-lg-none">
          <i class="bi bi-list"></i>
        </button>
        <div class="page-title">
          <h5>Dashboard</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <span class="sys-live"><span class="dot"></span> System Live</span>
        <a href="document-requests.php" class="btn btn-orange btn-sm px-3">
          <i class="bi bi-plus-lg me-1"></i> New Request
        </a>
      </div>
    </div>

    <div class="eb-content">
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Total Residents</div>
            <div class="stats-value">0</div>
            <div class="stats-sub text-muted">0 this month</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Pending Requests</div>
            <div class="stats-value">0</div>
            <div class="stats-sub text-warning">
              <i class="bi bi-clock"></i> 0 due today
            </div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Resolved Concerns</div>
            <div class="stats-value">0</div>
            <div class="stats-sub text-muted">0% closure rate</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Fees Collected</div>
            <div class="stats-value">₱0</div>
            <div class="stats-sub text-muted">0% this month</div>
          </div>
        </div>
      </div>

      <div class="row g-4 align-items-start">
        <div class="col-lg-8">
          <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0 fw-bold">Document Request Queue</h6>
                <small class="text-muted">Requires action</small>
              </div>
              <small class="text-muted">Updated just now</small>
            </div>
            
            <ul class="list-unstyled mb-0">
              <li class="queue-item" style="height: 55px;"></li>
              <li class="queue-item" style="height: 55px;"></li>
              <li class="queue-item" style="height: 55px;"></li>
              <li class="queue-item" style="height: 55px;"></li>
              <li class="queue-item" style="height: 55px;"></li>
              <li class="queue-item" style="height: 55px;"></li>
              <li class="queue-item" style="height: 55px;"></li>
              <li class="queue-item" style="height: 55px;"></li>
            </ul>
          </div>
        </div>
        
        <div class="col-lg-4">
          <div class="table-card mb-4 quick-actions">
            <h6 class="fw-bold mb-3">Quick Actions</h6>
            <div class="d-grid gap-2">
              
              <button class="btn btn-light border text-start">
                + New Resident
              </button>
              <!-- yung 3 sa baba na pamimilian -->
              <button class="btn btn-light border text-start">
                Stamp Clearance
              </button>
              <button class="btn btn-light border text-start">
                New Document Request
              </button>
              <button class="btn btn-light border text-start">
                Post Notice
              </button>
              
            </div>
          </div>
          
          <div class="table-card">
            <h6 class="fw-bold mb-3">Recent Activity</h6>
            
            <div class="pb-3 mb-3 border-bottom">
              <div class="bg-light rounded" style="height: 10px; width: 75%; margin-bottom: 8px;"></div>
              <div class="bg-light rounded" style="height: 9px; width: 50%;"></div>
            </div>
            <div class="pb-3 mb-3 border-bottom">
              <div class="bg-light rounded" style="height: 10px; width: 60%; margin-bottom: 8px;"></div>
              <div class="bg-light rounded" style="height: 9px; width: 40%;"></div>
            </div>
            <div>
              <div class="bg-light rounded" style="height: 10px; width: 80%; margin-bottom: 8px;"></div>
              <div class="bg-light rounded" style="height: 9px; width: 55%;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>