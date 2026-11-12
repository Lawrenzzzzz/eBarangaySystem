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
  
  <link rel="stylesheet" href="<?= url('assets/official/libs/bootstrap/css/bootstrap.min.css') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="<?= url('assets/official/css/ebarangay.css') ?>">
  <style>
    /* Dashboard layout */
    .table-card { padding: 1.5rem; }
    .table-card .queue-item { padding: 1.15rem 0; border-bottom: 1px solid var(--eb-border); }
    .table-card .queue-item:last-child { border-bottom: none; }

    .activity-feed { display: flex; flex-direction: column; }
    .activity-item + .activity-item { border-top: 1px solid var(--eb-border); }
    .activity-link { display: flex; align-items: flex-start; gap: .8rem; margin: 0 -.6rem; padding: .75rem .6rem; border-radius: 10px; text-decoration: none; transition: background-color .18s ease; }
    .activity-link:hover { background: var(--eb-surface-muted); }
    .activity-icon { display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; width: 36px; height: 36px; border-radius: 50%; font-size: .95rem; }
    .activity-icon.kind-document { color: #0d6efd; background: #e7f1ff; }
    .activity-icon.kind-blotter { color: #dc3545; background: #fdecec; }
    .activity-icon.kind-payment { color: #198754; background: #e7f6ee; }
    .activity-icon.kind-announcement { color: #d97706; background: #fef3c7; }
    .activity-body { flex: 1; min-width: 0; padding-top: .15rem; }
    .activity-title { font-size: .82rem; font-weight: 600; color: var(--eb-text-dark); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .activity-meta { margin-top: .12rem; font-size: .76rem; color: var(--eb-text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .activity-time { flex-shrink: 0; padding-top: .2rem; font-size: .7rem; color: var(--eb-text-muted); white-space: nowrap; }
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
      <a class="nav-link active" href="<?= url('official/dashboard') ?>">
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
      <div class="d-flex align-items-center justify-content-between gap-3 w-100">
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-light d-lg-none" id="ebSidebarToggle" type="button" aria-label="Toggle menu">
            <i class="bi bi-list"></i>
          </button>
          <div class="page-title">
            <h5>Dashboard</h5>
            <small>Municipality of Nasugbu - Barangay Bucana</small>
          </div>
        </div>
        <button type="button" class="btn btn-light position-relative d-lg-none" data-bs-toggle="modal" data-bs-target="#recentActivityModal" aria-label="Recent activity">
          <i class="bi bi-bell"></i>
          <?php if (!empty($recentActivity)): ?>
            <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
          <?php endif; ?>
        </button>
      </div>
    </div>

    <div class="eb-content">
      <div class="row g-3 mb-4 eb-stats-scroll">
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

                    <a href="<?= url('official/document-requests') ?>?q=<?= urlencode($request['reference_no']) ?>" class="btn btn-sm btn-primary fw-semibold">
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
        
        <div class="col-lg-4 d-none d-lg-block">
          <div class="table-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h6 class="fw-bold mb-0">Recent Activity</h6>
                <small class="text-muted">Latest updates across the barangay</small>
              </div>
            </div>
            
            <?php if ($recentActivity): ?>
              <ul class="activity-feed list-unstyled mb-0">
                <?php foreach ($recentActivity as $activity): ?>
                  <li class="activity-item">
                    <a href="<?= $esc($activity['url']) ?>" class="activity-link">
                      <span class="activity-icon kind-<?= $esc($activity['kind']) ?>">
                        <i class="bi <?= $esc($activity['icon']) ?>"></i>
                      </span>
                      <div class="activity-body">
                        <div class="activity-title"><?= $esc($activity['title']) ?></div>
                        <div class="activity-meta"><?= $esc($activity['meta']) ?></div>
                      </div>
                      <span class="activity-time"><?= $esc($relativeTime($activity['at'])) ?></span>
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

  <div class="modal fade" id="recentActivityModal" tabindex="-1" aria-labelledby="recentActivityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="recentActivityModalLabel">Recent Activity</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if ($recentActivity): ?>
            <ul class="activity-feed list-unstyled mb-0">
              <?php foreach ($recentActivity as $activity): ?>
                <li class="activity-item">
                  <a href="<?= $esc($activity['url']) ?>" class="activity-link">
                    <span class="activity-icon kind-<?= $esc($activity['kind']) ?>">
                      <i class="bi <?= $esc($activity['icon']) ?>"></i>
                    </span>
                    <div class="activity-body">
                      <div class="activity-title"><?= $esc($activity['title']) ?></div>
                      <div class="activity-meta"><?= $esc($activity['meta']) ?></div>
                    </div>
                    <span class="activity-time"><?= $esc($relativeTime($activity['at'])) ?></span>
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

  <!-- Bootstrap JS -->
  <script src="<?= url('assets/official/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
  <script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
  <script src="<?= url('assets/official/js/app.js') ?>"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var modalEl = document.getElementById('recentActivityModal');
      if (!modalEl) return;
      var backdrop = null;

      function openModal() {
        if (window.bootstrap && bootstrap.Modal) {
          bootstrap.Modal.getOrCreateInstance(modalEl).show();
          return;
        }
        modalEl.style.display = 'block';
        modalEl.classList.add('show');
        modalEl.removeAttribute('aria-hidden');
        modalEl.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
        if (!backdrop) {
          backdrop = document.createElement('div');
          backdrop.className = 'modal-backdrop fade show';
          document.body.appendChild(backdrop);
          backdrop.addEventListener('click', closeModal);
        }
      }

      function closeModal() {
        if (window.bootstrap && bootstrap.Modal) {
          var inst = bootstrap.Modal.getInstance(modalEl);
          if (inst) { inst.hide(); return; }
        }
        modalEl.style.display = 'none';
        modalEl.classList.remove('show');
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        if (backdrop) { backdrop.remove(); backdrop = null; }
      }

      document.querySelectorAll('[data-bs-target="#recentActivityModal"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          openModal();
        });
      });
      modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          closeModal();
        });
      });
      modalEl.addEventListener('click', function (e) {
        if (e.target === modalEl) closeModal();
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modalEl.classList.contains('show')) closeModal();
      });
    });
  </script>
</body>
</html>