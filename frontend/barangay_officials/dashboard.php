<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/dashboard.php';
?>
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
        <button class="btn btn-light d-lg-none">
          <i class="bi bi-list"></i>
        </button>
        <div class="page-title">
          <h5>Dashboard</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
    </div>

    <div class="eb-content">
      <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Total Residents</div>
            <div class="stats-value"><?= number_format($residentStats['total']) ?></div>
            <div class="stats-sub text-muted"><?= (int) $residentStats['new_this_month'] ?> this month</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Pending Requests</div>
            <div class="stats-value"><?= number_format($documentRequestStats['pending']) ?></div>
            <div class="stats-sub <?= $documentRequestStats['pending'] > 0 ? 'text-warning' : 'text-muted' ?>">
              <i class="bi bi-clock"></i>
              <?= $documentRequestStats['pending'] > 0 ? (int) $documentRequestStats['pending'] . ' awaiting review' : 'All caught up' ?>
            </div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Resolved Concerns</div>
            <div class="stats-value"><?= number_format($blotterStats['resolved']) ?></div>
            <div class="stats-sub text-muted">
              <?= $blotterStats['total'] > 0 ? $blotterResolvedRate . '% closure rate' : 'No reports yet' ?>
            </div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Fees Collected</div>
            <div class="stats-value"><?= $esc($paymentStats['collected_label']) ?></div>
            <div class="stats-sub text-muted"><?= (int) $paymentStats['month_count'] ?> payments this month</div>
          </div>
        </div>
      </div>

      <div class="row g-4 align-items-start">
        <div class="col-lg-8">
          <div class="table-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <h6 class="mb-0 fw-bold">Document Request Queue</h6>
                <small class="text-muted"><?= $documentRequestQueueCount > 0 ? $documentRequestQueueCount . ' request' . ($documentRequestQueueCount === 1 ? '' : 's') . ' require action' : 'No pending action' ?></small>
              </div>
              <small class="text-muted">Updated <?= $esc(date('M j, g:i A')) ?></small>
            </div>
            
            <?php if ($documentRequestQueue): ?>
              <ul class="queue-list mb-0">
                <?php foreach ($documentRequestQueue as $request): ?>
                  <li class="queue-item">
                    <span class="resident-avatar"><?= $esc($request['initial']) ?></span>

                    <div class="flex-grow-1" style="min-width: 0;">
                      <div class="d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-semibold"><?= $esc($request['full_name']) ?></span>
                        <span class="badge <?= $esc($request['badge_class']) ?>"><?= $esc($request['status_label']) ?></span>
                      </div>
                      <div class="resident-meta">
                        <?= $esc($request['document_name']) ?> · Ref <?= $esc($request['reference_no']) ?>
                      </div>
                    </div>

                    <div class="text-end small flex-shrink-0">
                      <div class="fw-semibold"><?= $esc($request['fee_label']) ?></div>
                      <div class="text-muted"><?= $esc(date('M j, Y', strtotime($request['created_at']))) ?></div>
                    </div>

                    <a href="document-requests.php?q=<?= urlencode($request['reference_no']) ?>" class="btn btn-sm btn-light border fw-semibold">
                      Review
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-center text-muted py-4 mb-0">No document requests need action right now.</p>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="col-lg-4">
          <div class="table-card mb-4 quick-actions">
            <h6 class="fw-bold mb-3">Quick Actions</h6>
            <div class="d-grid gap-2">
              
              <a class="btn btn-light border text-start" href="residents.php">
                <i class="bi bi-person-plus me-1"></i> New Resident
              </a>
              <a class="btn btn-light border text-start" href="document-requests.php?status=ready">
                <i class="bi bi-patch-check me-1"></i> Stamp Clearance
              </a>
              <a class="btn btn-light border text-start" href="document-requests.php">
                <i class="bi bi-file-earmark-plus me-1"></i> New Document Request
              </a>
              <a class="btn btn-light border text-start" href="announcements.php">
                <i class="bi bi-megaphone me-1"></i> Post Notice
              </a>
              
            </div>
          </div>
          
          <div class="table-card">
            <h6 class="fw-bold mb-3">Recent Activity</h6>
            
            <?php if ($recentActivity): ?>
              <ul class="list-unstyled mb-0">
                <?php foreach ($recentActivity as $activity): ?>
                  <li class="pb-3 mb-3 border-bottom">
                    <a href="<?= $esc($activity['url']) ?>" class="text-decoration-none d-flex gap-2">
                      <i class="bi <?= $esc($activity['icon']) ?> text-muted mt-1"></i>
                      <div style="min-width: 0;">
                        <div class="small fw-semibold text-dark text-truncate"><?= $esc($activity['title']) ?></div>
                        <div class="small text-muted text-truncate"><?= $esc($activity['meta']) ?></div>
                        <div class="text-muted" style="font-size: .72rem;"><?= $esc($relativeTime($activity['at'])) ?></div>
                      </div>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-muted small mb-0">No activity recorded yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/app.js"></script>
</body>
</html>