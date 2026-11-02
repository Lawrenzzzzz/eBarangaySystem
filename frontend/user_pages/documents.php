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
  <title>Barangay Bucana | Documents</title>

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
      <a href="documents.php" class="active" aria-current="page">Documents</a>
      <a href="blotter.php">Blotter</a>
      <a href="announcements.php">Announcements</a>
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

  <div class="layout docs">
    <!-- Page title (spans both columns so both start on the same line) -->
    <section class="greeting">
      <div>
        <p class="date">Documents</p>
        <h1>Request a document</h1>
      </div>
      <p class="summary">Claim at the barangay hall once ready</p>
    </section>
    <!-- Main column -->
    <main class="col-main">

      <div>
        <!-- Pick a document -->
        <div class="doc-picker">
          <a href="documents.php?type=clearance" class="card pick selected" aria-current="true" data-type="clearance" data-title="Barangay Clearance" data-desc="Proof of good standing, usually required for work and permits." data-fee="₱50 · 2 business days">
            <span class="chip blue">C</span>
            <h3>Barangay Clearance</h3>
            <p>₱50 · 2 business days</p>
          </a>
          <a href="documents.php?type=indigency" class="card pick" data-type="indigency" data-title="Certificate of Indigency" data-desc="Confirms your household qualifies for free assistance and fee waivers." data-fee="Free · 1 business day">
            <span class="chip green">I</span>
            <h3>Certificate of Indigency</h3>
            <p>Free · 1 business day</p>
          </a>
          <a href="documents.php?type=residency" class="card pick" data-type="residency" data-title="Certificate of Residency" data-desc="Confirms that you live in the barangay." data-fee="₱30 · same day">
            <span class="chip amber">R</span>
            <h3>Certificate of Residency</h3>
            <p>₱30 · same day</p>
          </a>
        </div>

        <!-- Request form -->
        <section class="card form-card" id="doc-card">
          <h2 id="doc-title">Barangay Clearance</h2>
          <p class="desc" id="doc-desc">Proof of good standing, usually required for work and permits.</p>

          <form>
            <div class="field-grid">
              <label class="field"><span>Full name</span><input type="text" name="full_name" maxlength="80" value="<?= $esc($userFullName) ?>"></label>
              <label class="field"><span>Resident ID</span><input type="text" name="resident_id" maxlength="80" value="<?= $esc($userResidentCode) ?>"></label>
              <label class="field"><span>Address</span><input type="text" name="address" maxlength="80" value="<?= $esc($userAddress) ?>"></label>
              <label class="field"><span>Household</span><input type="text" name="household" maxlength="80" value="<?= $esc($userHousehold) ?>"></label>
            </div>

            <label class="field-label" for="purpose">Purpose of request</label>
            <label class="field area">
              <textarea id="purpose" name="purpose" rows="3" maxlength="300" required placeholder="e.g. Employment requirement, scholarship application, hospital assistance"></textarea>
            </label>

            <div class="form-foot">
              <button type="submit" class="btn-primary">Submit request</button>
              <span class="fee-note" id="doc-fee">₱50 · 2 business days</span>
            </div>
          </form>
        </section>
      </div>
    </main>

    <!-- Side column -->
    <aside class="col-side">
      <section class="card bring">
        <h2>What to bring</h2>
        <ul>
          <li><i class="blue"></i>One valid government-issued ID</li>
          <li><i class="green"></i>Your reference number from this portal</li>
          <li><i class="amber"></i>Exact payment, if the document has a fee</li>
        </ul>
        <small>Barangay hall is open Monday to Friday, 8AM–5PM. Certificates of indigency are always free of charge.</small>
      </section>
    </aside>
  </div>
</div>

<script>
(function () {
  var picks = document.querySelectorAll('.doc-picker .pick');
  var params = new URLSearchParams(window.location.search);
  var type = params.get('type') || 'clearance';
  var active = document.querySelector('.doc-picker .pick[data-type="' + type + '"]');
  if (!active) active = picks[0];

  picks.forEach(function (p) { p.classList.remove('selected'); p.removeAttribute('aria-current'); });
  active.classList.add('selected');
  active.setAttribute('aria-current', 'true');

  document.getElementById('doc-title').textContent = active.dataset.title;
  document.getElementById('doc-desc').textContent = active.dataset.desc;
  document.getElementById('doc-fee').textContent = active.dataset.fee;
  document.title = 'Barangay Bucana | Documents';

  document.querySelector('#doc-card form').addEventListener('submit', function (e) {
    e.preventDefault();
    var ref = 'BI-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000);
    document.getElementById('doc-card').innerHTML =
      '<div class="done">' +
        '<span class="chip">✓</span>' +
        '<h2>Request submitted</h2>' +
        '<p>Your ' + active.dataset.title + ' request is now pending review.</p>' +
        '<p>Reference number: <span class="ref-no">' + ref + '</span></p>' +
        '<p>Bring this number and a valid ID when you claim it at the barangay hall.</p>' +
        '<div class="form-foot">' +
          '<a href="track.php" class="btn-primary">Track my request</a>' +
          '<a href="documents.php" class="link">Request another document</a>' +
        '</div>' +
      '</div>';
  });
})();
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
