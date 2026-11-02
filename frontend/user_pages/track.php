<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/bootstrap.php';
require_login_page('../login_page/login.php');
require __DIR__ . '/_user.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Bucana | Track</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
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
      <a href="track.php" class="active" aria-current="page">Track</a>
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

  <div class="layout single track-page">
    <!-- Page title -->
    <section class="greeting">
      <div>
        <p class="date">Track</p>
        <h1>My requests</h1>
      </div>
      <p class="summary muted">
        No document requests · No blotter reports
      </p>
    </section>

    <main class="col-main">

      <!-- Search -->
      <section class="card search">
        <label for="track-search">Search by reference number or document</label>
        <input type="search" id="track-search" placeholder="e.g. BI-2024-0871 or clearance" autocomplete="off">
      </section>

      <!-- Document requests -->
      <section class="track-list" id="track-list">
        <p class="empty" id="track-empty">You have no document requests yet.</p>
      </section>

      <!-- Blotter reports -->
      <section id="blotter-reports">
        <h2 class="sub-title">Blotter reports</h2>
        <div class="track-reps">
          <p class="empty">You have no blotter reports yet.</p>
        </div>
      </section>
    </main>
  </div>
</div>

<script>
(function () {
  var input = document.getElementById('track-search');
  var empty = document.getElementById('track-empty');
  var items = document.querySelectorAll('[data-search]');
  if (!input) return;
  input.addEventListener('input', function () {
    var q = input.value.trim().toLowerCase(), docs = 0;
    items.forEach(function (el) {
      var match = !q || el.dataset.search.indexOf(q) !== -1;
      el.hidden = !match;
      if (match && el.classList.contains('track-item')) docs++;
    });
    empty.hidden = docs > 0;
  });
})();
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
