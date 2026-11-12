<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/document-fees.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= $esc($csrf) ?>">
  <title>Document Fees - E-Barangay Official</title>
  
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
      <a class="nav-link" href="<?= url('official/payments') ?>">
        <span class="nav-dot"></span><span>Payments</span>
      </a>
      <a class="nav-link" href="<?= url('official/reports') ?>">
        <span class="nav-dot"></span><span>Reports</span>
      </a>
      
      <!-- SETTINGS WITH SUB-OPTIONS -->
      <div class="settings-parent">
        <a class="nav-link" href="<?= url('official/document-fees') ?>">
          <span class="nav-dot"></span><span>Settings</span>
        </a>
        <button class="settings-caret-btn" type="button" data-bs-toggle="collapse" data-bs-target="#officialSettingsSubmenu" aria-expanded="true" aria-controls="officialSettingsSubmenu" aria-label="Toggle settings options">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>

      <div class="collapse show" id="officialSettingsSubmenu">
        <div class="settings-submenu">
          <a class="nav-link active" href="<?= url('official/document-fees') ?>">
            <span class="sub-dot"></span><span>Documents Fees</span>
          </a>
          <a class="nav-link" href="<?= url('official/change-password') ?>">
            <span class="sub-dot"></span><span>Change Password</span>
          </a>

          <a class="nav-link" href="<?= url('official/settings') ?>#profile-information">
            <span class="sub-dot"></span><span>Profile Information</span>
          </a>
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
        <button class="btn btn-light d-lg-none" id="ebSidebarToggle" type="button" aria-label="Toggle menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="page-title">
          <h5>Document Fees</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
    </div>

    <div class="eb-content">
      <div class="mb-4" style="max-width: 720px;">
        <p class="text-muted mb-0" style="font-size: .85rem;">
          Set the standard fees collected for each barangay-issued document. These amounts are used when processing document requests and payments.
        </p>
      </div>

      <form id="document-fees-form" novalidate>
        <div class="row g-4 justify-content-center">

          <!-- DOCUMENT FEES -->
          <div class="col-lg-6" id="documents-fees">
            <div class="settings-card">
              <h6>Document Fees</h6>
              <p class="subtitle">Standard fees for barangay-issued documents.</p>

              <div class="alert py-2 d-none" id="document-fees-alert" role="alert"></div>

              <?php if ($documentFees === []): ?>
                <p class="text-muted mb-0" style="font-size: .85rem;">No document types are configured yet.</p>
              <?php else: ?>
                <?php $lastIndex = count($documentFees) - 1; ?>
                <?php foreach ($documentFees as $index => $document): ?>
                  <div class="d-flex justify-content-between align-items-center <?= $index === $lastIndex ? 'mb-0' : 'mb-3' ?>">
                    <label class="mb-0" style="font-size: .88rem;" for="fee-<?= $esc($document['code']) ?>"><?= $esc($document['name']) ?></label>
                    <input
                      type="number"
                      class="form-control"
                      id="fee-<?= $esc($document['code']) ?>"
                      data-fee-code="<?= $esc($document['code']) ?>"
                      style="width: 120px;"
                      min="0"
                      step="0.01"
                      placeholder="0.00"
                      value="<?= number_format((float) $document['fee'], 2, '.', '') ?>"
                    >
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>

        </div>

        <!-- SAVE BAR -->
        <div class="save-bar">
          <span class="text-muted" style="font-size: .82rem;">
            <i class="bi bi-info-circle me-1"></i> Make changes and click Save.
          </span>
          <div class="d-flex gap-2">
            <button class="btn btn-light border" type="reset">
              <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
            </button>
            <button class="btn btn-success fw-semibold" type="submit">
              <i class="bi bi-check-lg me-1"></i> Save Changes
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="<?= url('assets/official/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
  <script src="<?= url('assets/official/js/app.js') ?>"></script>
  <script src="<?= url('assets/official/js/document-fees.js') ?>"></script>
</body>
</html>
