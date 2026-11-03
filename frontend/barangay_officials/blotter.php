<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Blotter - E-Barangay Official</title>
  
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA BLOTTER -->
      <a class="nav-link active" href="blotter.php">
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
          <h5>Blotter</h5>
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
      <!-- STATS CARDS -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Total Cases</div>
            <div class="stats-value">0</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Resolved</div>
            <div class="stats-value">0</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Investigating</div>
            <div class="stats-value">0</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Filed</div>
            <div class="stats-value">0</div>
          </div>
        </div>
      </div>

      <!-- BLOTTER TABLE -->
      <div class="table-card">
        <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
          <h6 class="mb-0 fw-bold">Blotter Log</h6>
          <button class="btn btn-orange btn-sm px-3">
            New Entry
          </button>
        </div>
        
        <div class="table-responsive">
          <table class="blotter-table">
            <thead>
              <tr>
                <th>Case No.</th>
                <th>Date</th>
                <th>Complainant</th>
                <th>Respondent</th>
                <th>Nature</th>
                <th>Purok</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <!-- blank i2 rows -->
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
              <tr><td style="height: 65px;"></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/app.js"></script>
</body>
</html>