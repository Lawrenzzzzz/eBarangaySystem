<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Settings - E-Barangay Official</title>
  
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
      <a class="nav-link" href="payments.php">
        <span class="nav-dot"></span><span>Payments</span>
      </a>
      <a class="nav-link" href="reports.php">
        <span class="nav-dot"></span><span>Reports</span>
      </a>
      
      <!-- ACTIVE HIGHLIGHT I2 SA SETTINGS (wow last na) -->
      <a class="nav-link active" href="settings.php">
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
          <h5>Settings</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-success btn-sm px-3">
          <i class="bi bi-check-lg me-1"></i> Save All
        </button>
      </div>
    </div>

    <div class="eb-content">
      <div class="row g-4">
        
        <!-- BARANGAY INFORMATION -->
        <div class="col-lg-7">
          <div class="settings-card">
            <h6>Barangay Information</h6>
            <p class="subtitle">Official barangay details displayed on documents and announcements.</p>

            <div class="mb-3">
              <label class="form-label">Barangay Name</label>
              <input type="text" class="form-control" placeholder="">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Municipality</label>
              <input type="text" class="form-control" placeholder="">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Province</label>
              <input type="text" class="form-control" placeholder="">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Barangay Captain</label>
              <input type="text" class="form-control" placeholder="">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Contact Number</label>
              <input type="text" class="form-control" placeholder="">
            </div>
            
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" placeholder="">
            </div>
            
            <div class="mb-0">
              <label class="form-label">Office Address</label>
              <input type="text" class="form-control" placeholder="">
            </div>
          </div>
        </div>

        <!-- DOCUMENT FEES -->
        <div class="col-lg-5">
          <div class="settings-card">
            <h6>Document Fees</h6>
            <p class="subtitle">Standard fees for barangay-issued documents.</p>

            <div class="d-flex justify-content-between align-items-center mb-3">
              <span style="font-size: .88rem;">Barangay Clearance</span>
              <input type="text" class="form-control" style="width: 120px;" value="₱0" readonly>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span style="font-size: .88rem;">Certificate of Residency</span>
              <input type="text" class="form-control" style="width: 120px;" value="₱0" readonly>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span style="font-size: .88rem;">Indigency Certificate</span>
              <input type="text" class="form-control" style="width: 120px;" value="₱0" readonly>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span style="font-size: .88rem;">Barangay ID</span>
              <input type="text" class="form-control" style="width: 120px;" value="₱0" readonly>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span style="font-size: .88rem;">Business Permit</span>
              <input type="text" class="form-control" style="width: 120px;" value="₱0" readonly>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-0">
              <span style="font-size: .88rem;">Barangay ID (Senior)</span>
              <input type="text" class="form-control" style="width: 120px;" value="₱0" readonly>
            </div>
          </div>
        </div>

      </div>

      <!-- SAVE BAR -->
      <div class="save-bar">
        <span class="text-muted" style="font-size: .82rem;">
          <i class="bi bi-info-circle me-1"></i> Make changes and click Save.
        </span>
        <div class="d-flex gap-2">
          <button class="btn btn-light border">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
          </button>
          <button class="btn btn-success fw-semibold">
            <i class="bi bi-check-lg me-1"></i> Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/app.js"></script>
</body>
</html>