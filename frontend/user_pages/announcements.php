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
    </nav>

    <div class="nav-right">
      <span class="status"><i class="dot"></i> Logged in</span>
      <span class="avatar round" aria-label="Your profile"><?= $esc($userInitials) ?></span>
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
      <p class="summary muted">3 notices posted</p>
    </section>

    <main class="col-main">
      <div class="notice-grid">
        <article class="card note">
          <span class="cat blue">Health</span>
          <h3>Free Hepatitis B vaccination</h3>
          <p>Community health post, 9AM–12NN, 18–20 June. Bring a valid ID.</p>
          <small>Posted 12 Jun</small>
        </article>
        <article class="card note">
          <span class="cat green">Safety</span>
          <h3>Streetlight repair, Purok 4</h3>
          <p>Two poles along Rizal St. scheduled for 16 June. Report outages via blotter.</p>
          <small>Posted 10 Jun</small>
        </article>
        <article class="card note">
          <span class="cat amber">Notice</span>
          <h3>Garbage collection moved to Thursday</h3>
          <p>Collection along Mabini and Bonifacio St. shifts to Thursday mornings starting 20 June.</p>
          <small>Posted 8 Jun</small>
        </article>
      </div>
    </main>
  </div>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>
