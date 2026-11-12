<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/resident/announcements.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Bucana | Announcements</title>
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
      <a href="<?= url('announcements') ?>" class="active" aria-current="page">Announcements</a>
      <a href="<?= url('projects') ?>">Projects</a>
      <a href="<?= url('track') ?>">Track</a>
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

  <div class="layout single">
    <!-- Page title -->
    <section class="greeting">
      <div>
        <p class="date">Announcements</p>
        <h1>Barangay advisories</h1>
      </div>
      <p class="summary muted"><?= count($announcements) ?> <?= count($announcements) === 1 ? 'notice' : 'notices' ?> posted</p>
    </section>

    <main class="col-main">
      <div class="notice-grid">
        <?php if ($announcements): ?>
          <?php foreach ($announcements as $announcement): ?>
            <article class="card note">
              <span class="cat <?= $esc($announcement['type_tone']) ?>"><?= $esc($announcement['type_label']) ?></span>
              <h3><?= $esc($announcement['title']) ?></h3>
              <p><?= nl2br($esc($announcement['content'])) ?></p>
              <small>Posted <?= $esc(date('M j', strtotime($announcement['created_at']))) ?></small>
            </article>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="empty">No announcements posted yet.</p>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>

<script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
<script src="<?= url('assets/resident/js/app.js') ?>?v=2"></script>
</body>
</html>
