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
  <title>Barangay Bucana | Blotter</title>
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
      <a href="blotter.php" class="active" aria-current="page">Blotter</a>
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

  <div class="layout blotter-page">
    <!-- Page title (spans both columns so both start on the same line) -->
    <section class="greeting">
      <div>
        <p class="date">Blotter</p>
        <h1>File a report</h1>
      </div>
      <p class="summary">Reviewed by the Lupong Tagapamayapa</p>
    </section>
    <!-- Main column -->
    <main class="col-main">

      <section class="card form-card tall" id="blotter-card">
        <h2>File a blotter report</h2>
        <p class="desc">Report a complaint, nuisance, or incident for the barangay blotter record.</p>

        <form>
          <div class="field-grid">
            <label class="field"><span>Filed by</span><input type="text" name="filed_by" maxlength="80" value="<?= $esc($userFullName) ?>"></label>
            <label class="field"><span>Address</span><input type="text" name="address" maxlength="120" value="<?= $esc($userAddress) ?>"></label>
            <label class="field wide"><span>What is the report about</span><input type="text" name="subject" maxlength="80" value="" required placeholder="e.g. Noise complaint"></label>
          </div>

          <label class="field-label" for="details">What happened?</label>
          <label class="field area">
            <textarea id="details" name="details" rows="3" maxlength="1000" required placeholder="Include the date, time, place, and people involved if you know them"></textarea>
          </label>

          <div class="form-foot">
            <button type="submit" class="btn-primary">Submit report</button>
          </div>
        </form>
      </section>
    </main>

    <!-- Side column -->
    <aside class="col-side">
      <section>
        <h2 class="side-title">Your reports</h2>
        <p class="empty">You have no blotter reports yet.</p>
      </section>
    </aside>
  </div>
</div>

<script>
(function () {
  var card = document.getElementById('blotter-card');
  var form = card.querySelector('form');
  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var ref = 'BL-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000);
    card.innerHTML =
      '<div class="done">' +
        '<span class="chip">✓</span>' +
        '<h2>Report submitted</h2>' +
        '<p>The barangay has received your report and it is now pending review.</p>' +
        '<p>Reference number: <span class="ref-no">' + ref + '</span></p>' +
        '<div class="form-foot">' +
          '<a href="blotter.php" class="btn-primary">File another report</a>' +
          '<a href="index.php" class="link">Back to home</a>' +
        '</div>' +
      '</div>';
  });
})();
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
