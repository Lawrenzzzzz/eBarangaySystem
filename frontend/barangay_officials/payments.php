<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/payments.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payments - E-Barangay Official</title>
  
  <link rel="stylesheet" href="<?= url('assets/official/libs/bootstrap/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="<?= url('assets/official/css/ebarangay.css') ?>">
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
      <a class="nav-link" href="<?= url('official/dashboard') ?>">
        <span class="nav-dot"></span><span>Dashboard</span>
      </a>
      <a class="nav-link" href="<?= url('official/residents') ?>">
        <span class="nav-dot"></span><span>Residents</span>
      </a>
      <a class="nav-link" href="<?= url('official/document-requests') ?>">
        <span class="nav-dot"></span><span>Document Requests</span>
      </a>
      <a class="nav-link" href="<?= url('official/blotter') ?>">
        <span class="nav-dot"></span><span>Blotter</span>
      </a>
      <a class="nav-link" href="<?= url('official/announcements') ?>">
        <span class="nav-dot"></span><span>Announcements</span>
      </a>
      
      <!-- ACTIVE HIGHLIGHT I2 SA PAYMENTS -->
      <a class="nav-link active" href="<?= url('official/payments') ?>">
        <span class="nav-dot"></span><span>Payments</span>
      </a>
      
      <a class="nav-link" href="<?= url('official/reports') ?>">
        <span class="nav-dot"></span><span>Reports</span>
      </a>
      <div class="settings-parent">
        <a class="nav-link" href="<?= url('official/document-fees') ?>">
          <span class="nav-dot"></span><span>Settings</span>
        </a>
        <button class="settings-caret-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#officialSettingsSubmenu" aria-expanded="false" aria-controls="officialSettingsSubmenu" aria-label="Toggle settings options">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>

      <div class="collapse" id="officialSettingsSubmenu">
        <div class="settings-submenu">
          <a class="nav-link" href="<?= url('official/document-fees') ?>"><span class="sub-dot"></span><span>Documents Fees</span></a>
          <a class="nav-link" href="<?= url('official/change-password') ?>"><span class="sub-dot"></span><span>Change Password</span></a>
          <a class="nav-link" href="<?= url('official/settings') ?>#profile-information"><span class="sub-dot"></span><span>Profile Information</span></a>
        </div>
      </div>
    </div>
    
    <div class="user-profile">
      <img src="<?= $esc($officialAvatarUrl) ?>" alt="<?= $esc($officialFullName) ?>" class="avatar">
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
          <h5>Payments</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="#" class="btn btn-success btn-sm px-3">
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
            <div class="stats-value"><?= $esc($paymentStats['collected_label']) ?></div>
            <div class="stats-sub text-muted"><?= (int) $paymentStats['month_count'] ?> this month</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Pending</div>
            <div class="stats-value text-warning"><?= $esc($paymentStats['pending_label']) ?></div>
            <div class="stats-sub text-muted"><?= (int) $paymentStats['pending_count'] ?> awaiting payment</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Free Services</div>
            <div class="stats-value"><?= (int) $paymentStats['free_count'] ?></div>
            <div class="stats-sub text-muted"><?= (int) $paymentStats['free_count'] ?> certificates</div>
          </div>
        </div>
        
        <div class="col-6 col-md-3">
          <div class="stats-card">
            <div class="stats-label">Transactions</div>
            <div class="stats-value"><?= (int) $paymentStats['week_count'] ?></div>
            <div class="stats-sub text-muted">this week</div>
          </div>
        </div>
      </div>

      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form class="search-box" method="get" action="<?= url('official/payments') ?>" role="search">
          <i class="bi bi-search"></i>
          <input type="search" name="q" value="<?= $esc($paymentSearch) ?>" placeholder="Search reference, payee, or document..." aria-label="Search payments">
        </form>
        <span class="text-muted small">
          Showing <?= count($payments) ?> <?= count($payments) === 1 ? 'transaction' : 'transactions' ?>
        </span>
      </div>

      <!-- PAYMENTS TABLE -->
      <div class="table-card">
        <div class="p-4 d-flex justify-content-between align-items-center border-bottom">
          <h6 class="mb-0 fw-bold">Payment Transactions</h6>
          <form method="post" action="<?= url('official/reports') ?>" class="d-inline">
            <input type="hidden" name="action" value="generate">
            <input type="hidden" name="report" value="payments">
            <input type="hidden" name="csrf_token" value="<?= $esc($csrf) ?>">
            <button type="submit" class="btn btn-primary fw-semibold btn-sm">
              <i class="bi bi-file-earmark-text me-1"></i> Generate Report
            </button>
          </form>
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
              <?php if ($payments): ?>
                <?php foreach ($payments as $payment): ?>
                  <tr>
                    <td class="fw-semibold"><?= $esc($payment['reference_no']) ?></td>
                    <td>
                      <div class="fw-semibold"><?= $esc($payment['payee']) ?></div>
                      <div class="small text-muted">
                        <?= $esc($payment['method_label']) ?><?= $payment['request_reference'] !== '' ? ' · Ref ' . $esc($payment['request_reference']) : '' ?>
                      </div>
                    </td>
                    <td><?= $esc($payment['document_name']) ?></td>
                    <td class="fw-semibold"><?= $esc($payment['amount_label']) ?></td>
                    <td><?= $esc(date('M j, Y', strtotime($payment['paid_at']))) ?></td>
                    <td><span class="badge <?= $esc($payment['badge_class']) ?>"><?= $esc($payment['status_label']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    <?= $paymentSearch !== '' ? 'No payments match your search.' : 'No payments recorded yet.' ?>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="<?= url('assets/official/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
  <script src="<?= url('assets/official/js/app.js') ?>"></script>
</body>
</html>