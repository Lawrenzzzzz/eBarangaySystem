<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/resident/blotter.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= $esc($csrf) ?>">
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

        <form id="blotter-form" novalidate>
          <div class="field-grid">
            <label class="field"><span>Filed by</span><input type="text" name="filed_by" maxlength="80" value="<?= $esc($userFullName) ?>" readonly></label>
            <label class="field"><span>Address</span><input type="text" name="address" maxlength="120" value="<?= $esc($userAddress) ?>" readonly></label>
            <label class="field wide"><span>What is the report about</span><input type="text" name="subject" id="blotter-subject" maxlength="160" minlength="5" value="" required placeholder="e.g. Noise complaint"></label>
          </div>

          <label class="field-label" for="details">What happened?</label>
          <label class="field area">
            <textarea id="details" name="details" rows="3" maxlength="1000" minlength="10" required placeholder="Include the date, time, place, and people involved if you know them"></textarea>
          </label>

          <p class="error" id="blotter-error" role="alert" hidden></p>

          <div class="form-foot">
            <button type="submit" class="btn-primary" id="blotter-submit">Submit report</button>
          </div>
        </form>
      </section>
    </main>

    <!-- Side column -->
    <aside class="col-side">
      <section>
        <h2 class="side-title">Your reports</h2>
        <?php if ($blotterReports): ?>
          <?php foreach ($blotterReports as $report): ?>
            <article class="card rep">
              <h3><?= $esc($report['subject']) ?> <span class="badge <?= $esc($report['status_tone']) ?>"><?= $esc($report['status_label']) ?></span></h3>
              <p class="ref">Ref <?= $esc($report['reference_no']) ?> · <?= $esc(date('M j, Y', strtotime($report['created_at']))) ?></p>
              <p><?= $esc($blotterExcerpt($report['details'])) ?></p>
              <a class="link" href="blotter-report.php?ref=<?= urlencode($report['reference_no']) ?>">View full report <span class="arrow">→</span></a>
            </article>
          <?php endforeach; ?>
          <a href="track.php" class="link">View all reports</a>
        <?php else: ?>
          <p class="empty">You have no blotter reports yet.</p>
        <?php endif; ?>
      </section>
    </aside>
  </div>
</div>

<script>
(function () {
  var card = document.getElementById('blotter-card');
  var form = document.getElementById('blotter-form');
  if (!card || !form) return;

  var subjectEl = document.getElementById('blotter-subject');
  var detailsEl = document.getElementById('details');
  var errorEl = document.getElementById('blotter-error');
  var submitBtn = document.getElementById('blotter-submit');

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (char) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
    });
  }

  function showError(message) {
    errorEl.textContent = message;
    errorEl.hidden = false;
  }

  function renderSuccess(report) {
    card.innerHTML =
      '<div class="done">' +
        '<span class="chip">✓</span>' +
        '<h2>Report submitted</h2>' +
        '<p>The barangay has received your report and it is now pending review.</p>' +
        '<p>Reference number: <span class="ref-no">' + escapeHtml(report.reference_no) + '</span></p>' +
        '<div class="form-foot">' +
          '<a href="track.php" class="btn-primary">View my reports</a>' +
          '<a href="blotter.php" class="link">File another report</a>' +
        '</div>' +
      '</div>';
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    errorEl.hidden = true;

    var subject = subjectEl.value.trim();
    var details = detailsEl.value.trim();

    if (subject.length < 5) {
      showError('Enter a subject for your report (at least 5 characters).');
      subjectEl.focus();
      return;
    }
    if (details.length < 10) {
      showError('Describe what happened (at least 10 characters).');
      detailsEl.focus();
      return;
    }

    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting…';

    try {
      var response = await fetch('../../backend/api/blotter-reports.php', {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfMeta ? csrfMeta.content : ''
        },
        credentials: 'same-origin',
        body: JSON.stringify({ subject: subject, details: details })
      });
      var result = await response.json();

      if (!response.ok || !result.ok) {
        throw new Error(result.error || 'Unable to submit your report right now.');
      }

      renderSuccess(result.report);
    } catch (error) {
      showError(error.message || 'Unable to submit your report right now.');
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit report';
    }
  });
})();
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
