<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/residents.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= $esc($csrf) ?>">
  <title>Residents - E-Barangay Official</title>
  
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA RESIDENTS -->
      <a class="nav-link active" href="<?= url('official/residents') ?>">
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
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-light d-lg-none" id="ebSidebarToggle" type="button" aria-label="Toggle menu">
          <i class="bi bi-list"></i>
        </button>
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
        <form class="search-box" method="get" action="<?= url('official/residents') ?>" role="search">
          <i class="bi bi-search"></i>
          <input type="search" name="q" value="<?= $esc($residentSearch) ?>" placeholder="Search name, code, email, or address..." aria-label="Search residents">
        </form>
        <div class="d-flex align-items-center gap-2">
          <form method="post" action="<?= url('official/reports') ?>" class="d-inline">
            <input type="hidden" name="action" value="generate">
            <input type="hidden" name="report" value="residents">
            <input type="hidden" name="csrf_token" value="<?= $esc($csrf) ?>">
            <button type="submit" class="btn btn-primary fw-semibold">
              <i class="bi bi-file-earmark-text me-1"></i> Generate Report
            </button>
          </form>
          <button class="btn btn-success fw-semibold" type="button" data-bs-toggle="modal" data-bs-target="#addResidentModal">
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
                          <form method="post" action="<?= url('official/residents') ?>" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="csrf_token" value="<?= $esc($csrf) ?>">
                            <input type="hidden" name="resident_id" value="<?= $resident['id'] ?>">
                            <input type="hidden" name="q" value="<?= $esc($residentSearch) ?>">
                            <button type="submit" class="btn btn-danger btn-sm fw-semibold">
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

  <div class="modal fade" id="addResidentModal" tabindex="-1" aria-labelledby="addResidentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <form id="add-resident-form" novalidate>
          <div class="modal-header align-items-start">
            <div>
              <h5 class="modal-title" id="addResidentModalLabel">Add resident</h5>
              <div class="small text-muted mt-1">
                Resident code: <span class="fw-bold text-dark" id="add-resident-code">Loading&hellip;</span>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-danger py-2 d-none" id="add-resident-error" role="alert"></div>
            <div class="alert alert-success py-2 d-none" id="add-resident-success" role="alert"></div>
            <div class="row g-3">
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-first-name">First name</label>
                <input class="form-control" id="ar-first-name" name="first_name" autocomplete="given-name" maxlength="80" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-middle-name">Middle name</label>
                <input class="form-control" id="ar-middle-name" name="middle_name" autocomplete="additional-name" maxlength="80" placeholder="Optional">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-last-name">Last name</label>
                <input class="form-control" id="ar-last-name" name="last_name" autocomplete="family-name" maxlength="80" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-suffix">Suffix</label>
                <input class="form-control" id="ar-suffix" name="suffix" autocomplete="honorific-suffix" maxlength="20" placeholder="e.g. Jr., III">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-birth-date">Date of birth</label>
                <input class="form-control" id="ar-birth-date" name="birth_date" type="text" autocomplete="bday" inputmode="numeric" maxlength="10" placeholder="MM/DD/YYYY" required>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-sex">Sex</label>
                <select class="form-select" id="ar-sex" name="sex" required>
                  <option value="">Select</option>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-civil-status">Civil status</label>
                <select class="form-select" id="ar-civil-status" name="civil_status" required>
                  <option value="">Select</option>
                  <option value="single">Single</option>
                  <option value="married">Married</option>
                  <option value="widowed">Widowed</option>
                  <option value="separated">Separated</option>
                  <option value="annulled">Annulled</option>
                </select>
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-phone">Phone number</label>
                <input class="form-control" id="ar-phone" name="phone" type="tel" autocomplete="tel" placeholder="09XX XXX XXXX">
              </div>
              <div class="col-12 col-md-4">
                <label class="form-label" for="ar-household">Household number</label>
                <input class="form-control" id="ar-household" name="household_number" maxlength="40" placeholder="Optional">
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label" for="ar-email">Email address</label>
                <input class="form-control" id="ar-email" name="email" type="email" autocomplete="email" maxlength="190" required>
              </div>
              <div class="col-12 col-md-6">
                <label class="form-label" for="ar-address">Complete address</label>
                <input class="form-control" id="ar-address" name="address_line" autocomplete="street-address" maxlength="255" placeholder="House no., street, purok" required>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-success fw-semibold" id="add-resident-submit">
              <i class="bi bi-person-plus me-1"></i> Save resident
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