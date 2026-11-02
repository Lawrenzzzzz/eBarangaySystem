<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Requests - E-Barangay Official</title>
  
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA DOCUMENT REQUESTS -->
      <a class="nav-link active" href="document-requests.php">
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
          <h5>Document Requests</h5>
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
      <!-- TABS -->
      <div class="eb-tabs mb-4">
        <button class="eb-tab active">All</button>
        <button class="eb-tab">Pending</button>
        <button class="eb-tab">Approved</button>
        <button class="eb-tab">Rejected</button>
      </div>

      <!-- LIST -->
      <div class="table-card">
        <!-- table-scrollable -->
        <div class="table-scrollable">
          <ul class="queue-list mb-0">
            <!-- blank rows i2 -->
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
            <li class="queue-item" style="height: 65px;"></li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>