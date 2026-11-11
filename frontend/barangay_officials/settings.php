<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/guard.php';

$officialAttendanceQr = attendance_account_eligible($currentUser)
    ? attendance_payload((int) $currentUser['user_id'])
    : null;
$officialAttendanceBarangay = trim((string) (barangay_profile_record()['barangay_name'] ?? ''))
    ?: 'Barangay San Isidro';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= $esc($csrf) ?>">
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
      
      <!-- SETTINGS WITH SUB-OPTIONS -->
      <div class="settings-parent">
        <a class="nav-link" href="document-fees.php">
          <span class="nav-dot"></span><span>Settings</span>
        </a>
        <button class="settings-caret-btn" type="button" data-bs-toggle="collapse" data-bs-target="#officialSettingsSubmenu" aria-expanded="true" aria-controls="officialSettingsSubmenu" aria-label="Toggle settings options">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>

      <div class="collapse show" id="officialSettingsSubmenu">
        <div class="settings-submenu">
          <a class="nav-link" href="document-fees.php">
            <span class="sub-dot"></span><span>Documents Fees</span>
          </a>
          <a class="nav-link" href="change-password.php">
            <span class="sub-dot"></span><span>Change Password</span>
          </a>

          <a class="nav-link active" href="#profile-information">
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
        <div class="page-title">
          <h5>Settings</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
    </div>

    <div class="eb-content">
      <form id="profile-form" novalidate>
        <div class="row g-4 justify-content-center">

          <!-- PROFILE INFORMATION -->
          <div class="col-lg-8" id="profile-information">
            <div class="settings-card">
              <h6>Profile Information</h6>
              <p class="subtitle">Manage your profile photo and personal contact details.</p>

              <div class="alert py-2 d-none" id="profile-alert" role="alert"></div>

              <!-- ADD PHOTO -->
              <div class="profile-block" id="add-photo">
                <label class="form-label d-block" for="profile-photo">Profile Photo</label>
                <div class="d-flex align-items-center gap-3 mb-3">
                  <img src="<?= $esc($officialAvatarUrl) ?>" alt="<?= $esc($officialFullName) ?>" class="settings-avatar" id="profile-photo-preview">
                  <div class="flex-grow-1">
                    <input type="file" class="form-control" id="profile-photo" name="photo" accept="image/png,image/jpeg,image/webp">
                    <small class="d-block mt-1" style="font-size: .72rem; color: var(--eb-sidebar-muted);">JPG, PNG, or WebP, up to 2 MB.</small>
                  </div>
                </div>
              </div>

              <!-- CHANGE EMAIL -->
              <div class="profile-block" id="change-email">
                <label class="form-label" for="profile-email">Email Address</label>
                <input type="email" class="form-control mb-3" id="profile-email" name="email" value="<?= $esc($currentUser['email'] ?? '') ?>" placeholder="official@barangay.gov.ph">
              </div>

              <!-- EDIT NAME -->
              <div class="profile-block" id="edit-name">
                <label class="form-label" for="profile-name">Full Name</label>
                <input type="text" class="form-control mb-3" id="profile-name" name="full_name" value="<?= $esc($currentUser['full_name'] ?? '') ?>" placeholder="Enter your full name">
              </div>

              <!-- CHANGE PHONE NUMBER -->
              <div class="profile-block <?= $officialAttendanceQr === null ? 'mb-0' : '' ?>" id="change-phone">
                <label class="form-label" for="profile-phone">Phone Number</label>
                <input type="tel" class="form-control mb-3" id="profile-phone" name="phone" value="<?= $esc($currentUser['phone'] ?? '') ?>" placeholder="09XX XXX XXXX">
              </div>

              <?php if ($officialAttendanceQr !== null): ?>
              <!-- ATTENDANCE QR CODE -->
              <div class="profile-block mb-0" id="attendance-qr">
                <label class="form-label d-block" for="att-qr-code">Attendance QR Code</label>
                <div class="d-flex align-items-center gap-3">
                  <div class="att-qr-box" id="att-qr-code"
                       data-payload="<?= $esc($officialAttendanceQr) ?>"
                       data-name="<?= $esc($officialFullName) ?>"
                       data-position="<?= $esc(attendance_position_label($currentUser)) ?>"
                       data-barangay="<?= $esc($officialAttendanceBarangay) ?>"
                       role="img" aria-label="Your attendance QR code"></div>
                  <div class="flex-grow-1">
                    <p class="text-muted mb-2" style="font-size: .8rem;">
                      Present this code to the QR scanner at the barangay hall to record your daily time in and time out.
                    </p>
                    <button class="btn btn-success btn-sm fw-semibold" type="button" id="att-qr-print">
                      <i class="bi bi-printer me-1"></i> Print badge
                    </button>
                  </div>
                </div>
              </div>
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
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="libs/qrcode/qrcode.js"></script>
  <script src="js/app.js"></script>
  <script src="js/profile.js"></script>
  <script src="js/attendance-qr.js"></script>
</body>
</html>