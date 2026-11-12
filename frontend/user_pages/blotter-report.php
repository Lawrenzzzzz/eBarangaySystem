<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/resident/blotter-report.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Bucana | Blotter Report</title>
  <link rel="stylesheet" href="<?= url('assets/resident/style.css') ?>?v=2">
</head>
<body>
<div class="shell">

  <!-- Top bar -->
  <header class="topnav">
    <a href="<?= url('dashboard') ?>" class="brand">
      <span class="avatar sq">B</span>
      <span class="brand-text">
        <strong>Barangay Bucana</strong>
        <small>Resident Portal</small>
      </span>
    </a>

    <nav class="nav" id="main-nav" aria-label="Main">
      <a href="<?= url('dashboard') ?>">Home</a>
      <a href="<?= url('documents') ?>">Documents</a>
      <a href="<?= url('blotter') ?>">Blotter</a>
      <a href="<?= url('announcements') ?>">Announcements</a>
      <a href="<?= url('projects') ?>">Projects</a>
      <a href="<?= url('track') ?>" class="active" aria-current="page">Track</a>
      <a href="<?= url('settings') ?>">Settings</a>
      <button class="nav-signout" type="button" data-signout>Sign out</button>
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

  <div class="layout single track-page">
    <!-- Page title -->
    <section class="greeting">
      <div>
        <p class="date">Track / Blotter</p>
        <h1><?= $report ? 'Report details' : 'Report not found' ?></h1>
      </div>
      <p class="summary muted">
        <?= $report ? $esc($report['reference_no']) . ' · Filed ' . $esc(date('M j, Y', strtotime($report['created_at']))) : 'This report is not available' ?>
      </p>
    </section>

    <main class="col-main">

      <?php if (!$report): ?>
        <section class="card form-card">
          <h2>We could not find that report</h2>
          <p class="desc">The reference number may be incorrect, or the report does not belong to your account.</p>
          <div class="form-foot">
            <a href="<?= url('track') ?>" class="btn-primary">Back to my requests</a>
          </div>
        </section>
      <?php else: ?>
        <article class="card track-item">
          <div class="track-head">
            <span class="chip blue">B</span>
            <div>
              <h3><?= $esc($report['subject']) ?> <span class="badge <?= $esc($report['status_tone']) ?>"><?= $esc($report['status_label']) ?></span></h3>
              <p class="ref">Ref <?= $esc($report['reference_no']) ?> · Filed <?= $esc($filedDate) ?></p>
            </div>
          </div>
          <div class="bar full">
            <span class="seg"><i style="width:100%"></i></span>
            <span class="seg"><i style="width:<?= $progress >= 2 ? '100%' : '0' ?>"></i></span>
            <span class="seg"><i style="width:<?= $progress >= 3 ? '100%' : '0' ?>"></i></span>
          </div>
          <div class="steps"><span>Filed</span><span>Reviewed</span><span>Closed</span></div>
          <p class="track-note"><?= $esc(blotter_status_note($report['status'])) ?></p>
        </article>

        <section class="card form-card">
          <h2>What you reported</h2>
          <p class="desc">This is the information recorded when you filed the report.</p>

          <div class="detail-grid">
            <div><span>Filed by</span><strong><?= $esc($report['filed_by']) ?></strong></div>
            <div><span>Address</span><strong><?= $esc($report['address_line']) ?></strong></div>
            <div><span>Status</span><strong><?= $esc($report['status_label']) ?></strong></div>
            <div><span>Last updated</span><strong><?= $esc($updatedDate) ?></strong></div>
          </div>

          <h3 class="detail-title">Description</h3>
          <p class="detail-copy"><?= nl2br($esc($report['details'])) ?></p>

          <?php if ($report['remarks'] !== null && $report['remarks'] !== ''): ?>
            <h3 class="detail-title">Barangay remarks</h3>
            <p class="detail-copy"><?= nl2br($esc($report['remarks'])) ?></p>
          <?php endif; ?>

          <?php if ($report['resolution'] !== null && $report['resolution'] !== ''): ?>
            <h3 class="detail-title">Resolution</h3>
            <p class="detail-copy"><?= nl2br($esc($report['resolution'])) ?></p>
          <?php endif; ?>

          <div class="form-foot">
            <a href="<?= url('track') ?>" class="btn-primary">Back to my requests</a>
            <a href="<?= url('blotter') ?>" class="link">File another report</a>
          </div>
        </section>
      <?php endif; ?>
    </main>
  </div>
</div>

<script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
<script src="<?= url('assets/resident/js/app.js') ?>?v=2"></script>
</body>
</html>
