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
  <title>Barangay Bucana | Resident Portal</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="shell">

  <!-- Top bar -->
  <header class="topnav" id="home">
    <a href="index.php" class="brand">
      <span class="avatar sq">B</span>
      <span class="brand-text">
        <strong>Barangay Bucana</strong>
        <small>Resident Portal</small>
      </span>
    </a>

    <nav class="nav" id="main-nav" aria-label="Main">
      <a href="index.php" class="active" aria-current="page">Home</a>
      <a href="documents.php">Documents</a>
      <a href="blotter.php">Blotter</a>
      <a href="announcements.php">Announcements</a>
      <a href="track.php">Track</a>
    </nav>

    <div class="nav-right">
      <span class="status"><i class="dot"></i> Logged in</span>
      <span class="avatar round" aria-label="Your profile"><?= $esc($userInitials) ?></span>
      <button class="nav-toggle" id="nav-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="main-nav">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <div class="layout home">
    <!-- Main column -->
    <main class="col-main">
      <section class="greeting">
        <div>
          <p class="date"><?= $esc(date('l, F j')) ?></p>
          <h1>Good day, <?= $esc($userFirstName) ?></h1>
        </div>
        <p class="summary">Your requests will appear here once you submit one.</p>
      </section>

      <section class="stats" aria-label="Summary">
        <div class="card stat">
          <span class="label">Active requests</span>
          <strong>0</strong>
          <small class="good">No active requests</small>
        </div>
        <div class="card stat">
          <span class="label">Processing</span>
          <strong>0</strong>
          <small class="warn">Nothing in progress</small>
        </div>
        <div class="card stat">
          <span class="label">Blotter filed</span>
          <strong>0</strong>
          <small class="">No reports filed</small>
        </div>
        <div class="card stat">
          <span class="label">Completed</span>
          <strong>0</strong>
          <small class="">No completed requests</small>
        </div>
      </section>

      <section id="documents">
        <div class="section-head">
          <h2>Request a document</h2>
          <span class="hint">Pick one to start</span>
        </div>
        <div class="doc-grid">
          <article class="card doc">
            <span class="chip blue">C</span>
            <h3>Barangay Clearance</h3>
            <p>₱50 · 2 business days</p>
            <a href="documents.php?type=clearance" class="link">Start request <svg class="arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
          </article>
          <article class="card doc">
            <span class="chip green">I</span>
            <h3>Certificate of Indigency</h3>
            <p>Free · 1 business day</p>
            <a href="documents.php?type=indigency" class="link">Start request <svg class="arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
          </article>
          <article class="card doc">
            <span class="chip amber">R</span>
            <h3>Certificate of Residency</h3>
            <p>₱30 · same day</p>
            <a href="documents.php?type=residency" class="link">Start request <svg class="arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></a>
          </article>
        </div>
      </section>

      <section id="track">
        <div class="section-head">
          <h2>Your requests</h2>
          <a href="track.php" class="link">View all</a>
        </div>
        <div class="req-list">
          <p class="empty">You have no document requests yet. Start one from the Documents page.</p>
        </div>
      </section>
    </main>

    <!-- Side column -->
    <aside class="col-side">
      <section id="announcements">
        <h2 class="side-title">Announcements</h2>
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
      </section>

      <section id="blotter">
        <h2 class="side-title">File a blotter</h2>
        <div class="card blotter">
          <p>Report a complaint, nuisance, or incident for the barangay blotter record.</p>
          <div class="mini-stats">
            <div><strong class="blue">240</strong><span>filed this year</span></div>
            <div><strong class="good">92%</strong><span>resolved</span></div>
          </div>
          <a href="blotter.php" class="btn">File a new report</a>
        </div>
      </section>

      <section>
        <h2 class="side-title">Your details</h2>
        <dl class="card details">
          <div>
            <dt><span class="chip sm blue">E</span>Email</dt>
            <dd><?= $esc($userEmail !== '' ? $userEmail : 'Not provided') ?></dd>
          </div>
          <div>
            <dt><span class="chip sm blue">ID</span>Resident ID</dt>
            <dd><?= $esc($userResidentCode !== '' ? $userResidentCode : 'Not assigned') ?></dd>
          </div>
          <div>
            <dt><span class="chip sm green">H</span>Household</dt>
            <dd><?= $esc($userHousehold !== '' ? $userHousehold : 'Not provided') ?></dd>
          </div>
          <div>
            <dt><span class="chip sm amber">A</span>Address</dt>
            <dd><?= $esc($userAddress !== '' ? $userAddress : 'Not provided') ?></dd>
          </div>
        </dl>
      </section>
    </aside>
  </div>
</div>

<script src="assets/js/app.js"></script>
</body>
</html>
