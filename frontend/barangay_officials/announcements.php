<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Announcements - E-Barangay Official</title>
  
  <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="css/ebarangay.css">
  <style>
    /* Announcements layout */
    #list { padding-right: .35rem; }
    .announcement-card:last-child { margin-bottom: 0; }
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA ANNOUNCEMENTS -->
      <a class="nav-link active" href="announcements.php">
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
      <img src="img/captain.jpg" alt="<?= $esc($officialFullName) ?>" class="avatar">
      <div>
        <div class="name"><?= $esc($officialFullName) ?></div>
        <div class="role"><?= $esc($officialRoleName) ?></div>
      </div>
    </div>

    <div class="px-3 pb-3">
      <button class="btn btn-outline-light btn-sm w-100" id="official-signout" type="button">
        <i class="bi bi-box-arrow-right me-1"></i> Sign out
      </button>
    </div>
  </div>

  <div class="eb-main">
    <div class="eb-topbar">
      <div class="d-flex align-items-center gap-3">
        <div class="page-title">
          <h5>Announcements</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="#" class="btn btn-orange btn-sm px-3">
          <i class="bi bi-plus-lg me-1"></i> New Request
        </a>
      </div>
    </div>

    <div class="eb-content">
      <!-- HEADER ROW -->
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <span class="text-muted fw-semibold" style="font-size: .85rem;">
          Showing 0 announcements
        </span>
        <button class="btn btn-orange fw-semibold">
          <i class="bi bi-plus-lg me-1"></i> New Announcement
        </button>
      </div>

      <!-- ANNOUNCEMENTS LIST -->
      <div id="list" class="table-scrollable pr-2">
        
        <!-- Box 1 -->
        <div class="announcement-card">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="bg-light rounded" style="height: 20px; width: 40%;"></div>
            <div class="bg-light rounded" style="height: 20px; width: 10%;"></div>
          </div>
          <div class="bg-light rounded mb-3" style="height: 14px; width: 80%;"></div>
          <div class="bg-light rounded" style="height: 14px; width: 60%;"></div>
        </div>

        <!-- Box 2 -->
        <div class="announcement-card">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="bg-light rounded" style="height: 20px; width: 50%;"></div>
            <div class="bg-light rounded" style="height: 20px; width: 10%;"></div>
          </div>
          <div class="bg-light rounded mb-3" style="height: 14px; width: 90%;"></div>
          <div class="bg-light rounded" style="height: 14px; width: 70%;"></div>
        </div>

        <!-- Box 3 -->
        <div class="announcement-card">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="bg-light rounded" style="height: 20px; width: 35%;"></div>
            <div class="bg-light rounded" style="height: 20px; width: 10%;"></div>
          </div>
          <div class="bg-light rounded mb-3" style="height: 14px; width: 85%;"></div>
          <div class="bg-light rounded" style="height: 14px; width: 50%;"></div>
        </div>
        
        <!-- Box 4 -->
        <div class="announcement-card">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="bg-light rounded" style="height: 20px; width: 45%;"></div>
            <div class="bg-light rounded" style="height: 20px; width: 10%;"></div>
          </div>
          <div class="bg-light rounded mb-3" style="height: 14px; width: 75%;"></div>
          <div class="bg-light rounded" style="height: 14px; width: 55%;"></div>
        </div>
        
        <!-- Box 5  -->
        <div class="announcement-card">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="bg-light rounded" style="height: 20px; width: 55%;"></div>
            <div class="bg-light rounded" style="height: 20px; width: 10%;"></div>
          </div>
          <div class="bg-light rounded mb-3" style="height: 14px; width: 95%;"></div>
          <div class="bg-light rounded" style="height: 14px; width: 65%;"></div>
        </div>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/app.js"></script>
</body>
</html>