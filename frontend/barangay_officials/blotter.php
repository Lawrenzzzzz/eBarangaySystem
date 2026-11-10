<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/blotter.php';
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
      <div class="settings-parent">
        <a class="nav-link" href="document-fees.php">
          <span class="nav-dot"></span><span>Settings</span>
        </a>
        <button class="settings-caret-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#officialSettingsSubmenu" aria-expanded="false" aria-controls="officialSettingsSubmenu" aria-label="Toggle settings options">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>

      <div class="collapse" id="officialSettingsSubmenu">
        <div class="settings-submenu">
          <a class="nav-link" href="document-fees.php"><span class="sub-dot"></span><span>Documents Fees</span></a>
          <a class="nav-link" href="change-password.php"><span class="sub-dot"></span><span>Change Password</span></a>
          <a class="nav-link" href="settings.php#profile-information"><span class="sub-dot"></span><span>Profile Information</span></a>
        </div>
      </div>
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
    </div>

    <div class="eb-content">
      <?php if ($blotterNotice !== ''): ?>
        <div class="alert alert-success py-2" role="alert"><?= $esc($blotterNotice) ?></div>
      <?php endif; ?>
      <?php if ($blotterError !== ''): ?>
        <div class="alert alert-danger py-2" role="alert"><?= $esc($blotterError) ?></div>
      <?php endif; ?>

      <!-- STATS CARDS -->
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Total Cases</div>
            <div class="stats-value"><?= (int) $blotterStats['total'] ?></div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Resolved</div>
            <div class="stats-value"><?= (int) $blotterStats['resolved'] ?></div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Investigating</div>
            <div class="stats-value"><?= (int) $blotterStats['investigating'] ?></div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Filed</div>
            <div class="stats-value"><?= (int) $blotterStats['filed'] ?></div>
          </div>
        </div>
      </div>

      <!-- BLOTTER TABLE -->
      <div class="table-card">
        <div class="p-4 d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom">
          <h6 class="mb-0 fw-bold">Blotter Log</h6>
          <form class="search-box" method="get" action="blotter.php" role="search">
            <i class="bi bi-search"></i>
            <input type="search" name="q" value="<?= $esc($blotterSearch) ?>" placeholder="Search case no., complainant, or subject..." aria-label="Search blotter reports">
          </form>
        </div>
        
        <div class="table-responsive">
          <table class="blotter-table">
            <thead>
              <tr>
                <th>Case No.</th>
                <th>Date</th>
                <th>Complainant</th>
                <th>Nature</th>
                <th>Purok</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($blotterReports): ?>
                <?php foreach ($blotterReports as $report): ?>
                  <tr data-search="<?= $esc($report['search_text']) ?>">
                    <td class="fw-semibold"><?= $esc($report['reference_no']) ?></td>
                    <td><?= $esc($report['date_label']) ?></td>
                    <td>
                      <div class="resident-person">
                        <span class="resident-avatar"><?= $esc($report['initial']) ?></span>
                        <div class="fw-semibold"><?= $esc($report['filed_by']) ?></div>
                      </div>
                    </td>
                    <td>
                      <div class="fw-semibold"><?= $esc($report['subject']) ?></div>
                      <div class="small text-muted"><?= $esc($report['excerpt']) ?></div>
                    </td>
                    <td><?= $esc($report['address_line']) ?></td>
                    <td>
                      <?php if ($report['action']): ?>
                        <form method="post" action="blotter.php" class="d-inline">
                          <input type="hidden" name="csrf_token" value="<?= $esc($csrf) ?>">
                          <input type="hidden" name="report_id" value="<?= (int) $report['id'] ?>">
                          <input type="hidden" name="status" value="<?= $esc($report['action']['status']) ?>">
                          <input type="hidden" name="q" value="<?= $esc($blotterSearch) ?>">
                          <button type="submit" class="btn btn-sm fw-semibold <?= $esc($report['action']['class']) ?>">
                            <?= $esc($report['action']['label']) ?>
                          </button>
                        </form>
                      <?php else: ?>
                        <span class="badge <?= $esc($report['badge_class']) ?>"><?= $esc($report['status_label']) ?></span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
              <tr data-blotter-empty<?= $blotterReports ? ' style="display: none;"' : '' ?>>
                <td colspan="6" class="text-center text-muted py-4">
                  <?= $blotterSearch !== '' ? 'No blotter reports match your search.' : 'No blotter reports found.' ?>
                </td>
              </tr>
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