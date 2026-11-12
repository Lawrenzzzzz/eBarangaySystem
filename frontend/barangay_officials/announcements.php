<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/announcements.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= $esc($csrf) ?>">
  <title>Announcements - E-Barangay Official</title>
  
  <link rel="stylesheet" href="<?= url('assets/official/libs/bootstrap/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="<?= url('assets/official/css/ebarangay.css') ?>">
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA ANNOUNCEMENTS -->
      <a class="nav-link active" href="<?= url('official/announcements') ?>">
        <span class="nav-dot"></span><span>Announcements</span>
      </a>
      
      <a class="nav-link" href="<?= url('official/payments') ?>">
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
        <button class="btn btn-light d-lg-none" id="ebSidebarToggle" type="button" aria-label="Toggle menu">
          <i class="bi bi-list"></i>
        </button>
        <div class="page-title">
          <h5>Announcements</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
    </div>

    <div class="eb-content">
      <!-- HEADER ROW -->
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <span class="text-muted fw-semibold" id="announcement-count" style="font-size: .85rem;">
          Showing <?= count($announcements) ?> <?= count($announcements) === 1 ? 'announcement' : 'announcements' ?>
        </span>
        <button class="btn btn-success fw-semibold" type="button" data-bs-target="#newAnnouncementModal">
          <i class="bi bi-plus-lg me-1"></i> New Announcement
        </button>
      </div>

      <!-- ANNOUNCEMENTS LIST -->
      <div id="list" class="table-scrollable pr-2">
        <?php if ($announcements): ?>
          <?php foreach ($announcements as $announcement): ?>
            <article class="announcement-card" style="border-left-color: <?= $esc($announcement['accent_color']) ?>;">
              <div class="d-flex justify-content-between align-items-start mb-2 gap-3">
                <div>
                  <span class="badge <?= $esc($announcement['badge_class']) ?>"><?= $esc($announcement['type_label']) ?></span>
                  <h6 class="mt-2 mb-1 fw-bold"><?= $esc($announcement['title']) ?></h6>
                </div>
                <small class="text-muted text-nowrap">
                  Posted <?= $esc(date('M j, Y', strtotime($announcement['created_at']))) ?>
                </small>
              </div>
              <p class="mb-0 text-muted"><?= nl2br($esc($announcement['content'])) ?></p>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-center text-muted py-4 mb-0">No announcements posted yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="modal fade" id="newAnnouncementModal" tabindex="-1" aria-labelledby="newAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="new-announcement-form" novalidate>
          <div class="modal-header align-items-start">
            <div>
              <h5 class="modal-title" id="newAnnouncementModalLabel">New announcement</h5>
              <div class="small text-muted mt-1">Choose a type, then add a title and content.</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger py-2 d-none" id="new-announcement-error" role="alert"></div>
            <div class="mb-3">
              <label class="form-label" for="na-type">Type</label>
              <select class="form-select" id="na-type" name="type" required>
                <option value="">Select a type</option>
                <option value="Health">Health</option>
                <option value="Education">Education</option>
                <option value="Infrastructure">Infrastructure</option>
                <option value="Safety">Safety</option>
                <option value="Notice">Notice</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label" for="na-title">Title of the announcement</label>
              <input class="form-control" id="na-title" name="title" maxlength="120" placeholder="Enter the announcement title" required>
            </div>
            <div>
              <label class="form-label" for="na-content">Content</label>
              <textarea class="form-control" id="na-content" name="content" rows="5" maxlength="2000" placeholder="Write the announcement details" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success fw-semibold">
              <i class="bi bi-megaphone me-1"></i> Post announcement
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="<?= url('assets/official/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
  <script src="<?= url('assets/official/js/app.js') ?>"></script>
</body>
</html>