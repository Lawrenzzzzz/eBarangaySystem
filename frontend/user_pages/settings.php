<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/resident/settings.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= $esc($csrf) ?>">
  <title>Barangay Bucana | Settings</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="shell">

  <!-- Top bar -->
  <header class="topnav">
    <a href="index.php" class="brand">
      <span class="avatar sq">B</span>
      <span class="brand-text">
        <strong>Barangay Bucana</strong>
        <small>Resident Portal</small>
      </span>
    </a>

    <nav class="nav" id="main-nav" aria-label="Main">
      <a href="index.php">Home</a>
      <a href="documents.php">Documents</a>
      <a href="blotter.php">Blotter</a>
      <a href="announcements.php">Announcements</a>
      <a href="projects.php">Projects</a>
      <a href="track.php">Track</a>
      <a href="settings.php" class="active" aria-current="page">Settings</a>
    </nav>

    <div class="nav-right">
      <span class="avatar round" aria-label="Your profile">
        <?php if ($userAvatarUrl !== ''): ?>
          <img src="<?= $esc($userAvatarUrl) ?>" alt="<?= $esc($userFullName) ?>">
        <?php else: ?>
          <?= $esc($userInitials) ?>
        <?php endif; ?>
      </span>
      <button class="btn-signout" id="signout" type="button">Sign out</button>
      <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="main-nav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <div class="layout single settings-page">
    <!-- Page title -->
    <section class="greeting">
      <div>
        <p class="date">Settings</p>
        <h1>Account settings</h1>
      </div>
      <p class="summary muted">Manage your profile and password</p>
    </section>

    <main class="col-main">

      <!-- Profile Information -->
      <form id="profile-form" novalidate>
        <section class="card form-card settings-card" id="profile-information">
          <h2>Profile Information</h2>
          <p class="desc">Manage your profile photo and personal contact details.</p>

          <p class="alert" id="profile-alert" role="alert" hidden></p>

          <div class="avatar-row">
            <div class="settings-avatar" id="profile-photo-preview" data-initials="<?= $esc($userInitials) ?>">
              <?php if ($userAvatarUrl !== ''): ?>
                <img src="<?= $esc($userAvatarUrl) ?>" alt="<?= $esc($userFullName) ?>">
              <?php else: ?>
                <span><?= $esc($userInitials) ?></span>
              <?php endif; ?>
            </div>
            <label class="field photo-field">
              <span>Profile photo</span>
              <input type="file" id="profile-photo" name="photo" accept="image/png,image/jpeg,image/webp">
              <small>JPG, PNG, or WebP, up to 2 MB.</small>
            </label>
          </div>

          <div class="field-grid">
            <label class="field">
              <span>Full name</span>
              <input type="text" name="full_name" maxlength="150" value="<?= $esc($userFullName) ?>" placeholder="Enter your full name" required>
            </label>
            <label class="field">
              <span>Email address</span>
              <input type="email" name="email" maxlength="190" value="<?= $esc($userEmail) ?>" placeholder="you@example.com" required>
            </label>
            <label class="field wide">
              <span>Phone number</span>
              <input type="tel" name="phone" maxlength="16" value="<?= $esc($userPhone) ?>" placeholder="09XX XXX XXXX" inputmode="tel">
            </label>
          </div>

          <div class="form-foot">
            <button type="submit" class="btn-primary">Save changes</button>
            <span class="fee-note">Make changes and click Save.</span>
          </div>
        </section>
      </form>

      <!-- Change Password -->
      <form id="password-form" novalidate>
        <section class="card form-card settings-card" id="change-password">
          <h2>Change Password</h2>
          <p class="desc">Keep your account secure by updating the password you use to sign in.</p>

          <p class="alert" id="password-alert" role="alert" hidden></p>

          <div class="field-grid">
            <label class="field wide">
              <span>Current password</span>
              <input type="password" name="current_password" autocomplete="current-password" placeholder="Enter current password" required>
            </label>
            <label class="field">
              <span>New password</span>
              <input type="password" name="new_password" autocomplete="new-password" placeholder="At least 8 characters" required>
            </label>
            <label class="field">
              <span>Confirm new password</span>
              <input type="password" name="confirm_password" autocomplete="new-password" placeholder="Re-enter new password" required>
            </label>
          </div>

          <div class="form-foot">
            <button type="submit" class="btn-primary">Update password</button>
            <span class="fee-note">Use at least one letter and one number.</span>
          </div>
        </section>
      </form>

    </main>
  </div>
</div>

<script src="assets/js/settings.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
