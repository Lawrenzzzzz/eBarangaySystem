<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/residents.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Residents - E-Barangay Official</title>
  
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA RESIDENTS -->
      <a class="nav-link active" href="residents.php">
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
        <div class="page-title">
          <h5>Residents</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
    </div>

    <div class="eb-content">
      <?php if ($residentActionNotice !== ''): ?>
        <div class="alert alert-success py-2" role="alert"><?= $esc($residentActionNotice) ?></div>
      <?php endif; ?>
      <?php if ($residentActionError !== ''): ?>
        <div class="alert alert-danger py-2" role="alert"><?= $esc($residentActionError) ?></div>
      <?php endif; ?>

      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form class="search-box" method="get" action="residents.php" role="search">
          <i class="bi bi-search"></i>
          <input type="search" name="q" value="<?= $esc($residentSearch) ?>" placeholder="Search name, code, email, or address..." aria-label="Search residents">
        </form>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-light border fw-semibold">
            <i class="bi bi-download me-1"></i> Export
          </button>
          <button class="btn btn-orange fw-semibold">
            <i class="bi bi-plus-lg me-1"></i> Add Resident
          </button>
        </div>
      </div>
      
      <div class="table-card">
        <!-- table-scrollbar -->
        <div class="table-responsive table-scrollable">
          <table class="residents-table">
            <thead>
              <tr>
                <th>Resident</th>
                <th>Contact</th>
                <th>Household</th>
                <th>Registered</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($residents): ?>
                <?php foreach ($residents as $resident): ?>
                  <?php
                    $canDelete = (int) $resident['user_id'] !== (int) current_user_id() && $resident['role_code'] === 'resident';
                    $residentSearchIndex = mb_strtolower(implode(' ', array_filter([
                      $resident['full_name'],
                      $resident['resident_code'],
                      $resident['email'],
                      $resident['phone'],
                      (string) ($resident['household_number'] ?? ''),
                      $resident['address_line'],
                    ], static fn ($value): bool => $value !== '')));
                  ?>
                  <tr data-search="<?= $esc($residentSearchIndex) ?>">
                    <td>
                      <div class="resident-person">
                        <span class="resident-avatar"><?= $esc($resident['initial']) ?></span>
                        <div>
                          <div class="fw-semibold"><?= $esc($resident['full_name']) ?></div>
                          <div class="resident-meta">
                            <?= $esc($resident['resident_code']) ?><?= $resident['email'] !== '' ? ' · ' . $esc($resident['email']) : '' ?>
                          </div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <?= $resident['phone'] !== '' ? $esc($resident['phone']) : '<span class="text-muted">No contact number</span>' ?>
                    </td>
                    <td>
                      <?php if ($resident['household_number'] !== null && $resident['household_number'] !== ''): ?>
                        <?= $esc($resident['household_number']) ?>
                      <?php else: ?>
                        <span class="text-muted">—</span>
                      <?php endif; ?>
                    </td>
                    <td><?= $esc(date('M j, Y', strtotime($resident['created_at']))) ?></td>
                    <td>
                      <div class="d-flex align-items-center gap-2 flex-wrap">
                        <?php if ($canDelete): ?>
                          <form method="post" action="residents.php" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="csrf_token" value="<?= $esc($csrf) ?>">
                            <input type="hidden" name="resident_id" value="<?= $resident['id'] ?>">
                            <input type="hidden" name="q" value="<?= $esc($residentSearch) ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm fw-semibold">
                              Delete
                            </button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
              <tr data-resident-empty<?= $residents ? ' style="display: none;"' : '' ?>>
                <td colspan="5" class="text-center text-muted py-4">
                  <?= $residentSearch !== '' ? 'No residents match your search.' : 'No residents found.' ?>
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