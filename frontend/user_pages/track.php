<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/resident/track.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Bucana | Track</title>
  <link rel="stylesheet" href="<?= url('assets/resident/style.css') ?>">
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
        <p class="date">Track</p>
        <h1>My requests</h1>
      </div>
      <p class="summary muted">
        <?= $documentRequestCount ?> document request<?= $documentRequestCount === 1 ? '' : 's' ?> · <?= $blotterReportCount ?> blotter report<?= $blotterReportCount === 1 ? '' : 's' ?>
      </p>
    </section>

    <main class="col-main">

      <!-- Search -->
      <section class="card search">
        <label for="track-search">Search by reference number, document, or report</label>
        <input type="search" id="track-search" placeholder="e.g. BI-2026-0871, clearance, or noise" autocomplete="off">
      </section>

      <!-- Document requests -->
      <section class="track-list" id="track-list">
        <?php if ($documentRequests): ?>
          <?php foreach ($documentRequests as $request): ?>
            <?php
              $progress = (int) $request['progress'];
              $note = $request['remarks'] !== null && $request['remarks'] !== ''
                  ? $request['remarks']
                  : ($requestNotes[$request['status']] ?? 'Your request is being handled by barangay staff.');
              $searchText = strtolower($request['reference_no'] . ' ' . $request['document_name'] . ' ' . $request['status'] . ' ' . $request['status_label']);
            ?>
            <article class="card track-item" data-search="<?= $esc($searchText) ?>">
              <div class="track-head">
                <span class="chip <?= $esc($chipClasses[$request['document_type']] ?? 'blue') ?>"><?= $esc($chipLetters[$request['document_type']] ?? strtoupper(substr($request['document_type'], 0, 1))) ?></span>
                <div>
                  <h3><?= $esc($request['document_name']) ?> <span class="badge <?= $esc($request['status_tone']) ?>"><?= $esc($request['status_label']) ?></span></h3>
                  <p class="ref">Ref <?= $esc($request['reference_no']) ?> · Submitted <?= $esc(date('M j, Y', strtotime($request['created_at']))) ?></p>
                </div>
              </div>
              <div class="bar full">
                <span class="seg"><i style="width:<?= $progress >= 1 ? '100%' : '0' ?>"></i></span>
                <span class="seg"><i style="width:<?= $progress >= 2 ? '100%' : '0' ?>"></i></span>
                <span class="seg"><i style="width:<?= $progress >= 3 ? '100%' : '0' ?>"></i></span>
              </div>
              <div class="steps"><span>Submitted</span><span>Processing</span><span>Ready</span></div>
              <p class="track-note"><?= $esc($note) ?></p>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
        <p class="empty" id="track-empty"<?= $documentRequests ? ' hidden' : '' ?>>
          <?= $documentRequests ? 'No requests match your search.' : 'You have no document requests yet.' ?>
        </p>
      </section>

      <!-- Blotter reports -->
      <section id="blotter-reports">
        <h2 class="sub-title">Blotter reports</h2>
        <div class="track-reps">
          <?php foreach ($blotterReports as $report): ?>
            <?php $blotterSearchText = strtolower($report['reference_no'] . ' ' . $report['subject'] . ' ' . $report['details'] . ' ' . $report['status'] . ' ' . $report['status_label']); ?>
            <article class="card rep" data-search="<?= $esc($blotterSearchText) ?>">
              <h3><?= $esc($report['subject']) ?> <span class="badge <?= $esc($report['status_tone']) ?>"><?= $esc($report['status_label']) ?></span></h3>
              <p class="ref">Ref <?= $esc($report['reference_no']) ?> · Filed <?= $esc(date('M j, Y', strtotime($report['created_at']))) ?></p>
              <p><?= $esc($blotterExcerpt($report['details'])) ?></p>
              <a class="link" href="<?= url('blotter-report') ?>?ref=<?= urlencode($report['reference_no']) ?>">View full report <span class="arrow">→</span></a>
            </article>
          <?php endforeach; ?>
        </div>
        <p class="empty" id="blotter-empty"<?= $blotterReports ? ' hidden' : '' ?>>
          <?= $blotterReports ? 'No blotter reports match your search.' : 'You have no blotter reports yet.' ?>
        </p>
      </section>
    </main>
  </div>
</div>

<script>
(function () {
  var input = document.getElementById('track-search');
  var documentItems = document.querySelectorAll('.track-item[data-search]');
  var blotterItems = document.querySelectorAll('.track-reps .rep[data-search]');
  var documentEmpty = document.getElementById('track-empty');
  var blotterEmpty = document.getElementById('blotter-empty');
  if (!input) return;

  input.addEventListener('input', function () {
    var q = input.value.trim().toLowerCase();
    var documents = 0;
    var reports = 0;

    documentItems.forEach(function (el) {
      var match = !q || el.dataset.search.indexOf(q) !== -1;
      el.hidden = !match;
      if (match) documents++;
    });

    blotterItems.forEach(function (el) {
      var match = !q || el.dataset.search.indexOf(q) !== -1;
      el.hidden = !match;
      if (match) reports++;
    });

    if (documentEmpty) {
      documentEmpty.hidden = documentItems.length === 0 ? false : (documents > 0 || q === '');
    }
    if (blotterEmpty) {
      blotterEmpty.hidden = blotterItems.length === 0 ? false : (reports > 0 || q === '');
    }
  });
})();
</script>
<script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
<script src="<?= url('assets/resident/js/app.js') ?>"></script>
</body>
</html>
