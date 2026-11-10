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
      <a href="announcements.php" class="active" aria-current="page">Announcements</a>
      <a href="track.php">Track</a>
      <a href="settings.php">Settings</a>
    </nav>

    <div class="nav-right">
      <span class="status"><i class="dot"></i> Logged in</span>
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

<script src="assets/js/app.js"></script>
</body>
</html>
