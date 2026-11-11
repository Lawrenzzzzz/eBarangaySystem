<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/resident/documents.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="<?= $esc($csrf) ?>">
  <title>Barangay Bucana | Documents</title>
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
      <a href="projects.php">Projects</a>
      <a href="track.php">Track</a>
      <a href="settings.php">Settings</a>
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
          <?php foreach ($documentTypes as $documentType): ?>
            <?php
              $code = $documentType['code'];
              $isSelected = $selectedType !== null && $selectedType['code'] === $code;
            ?>
            <a href="documents.php?type=<?= $esc($code) ?>"
               class="card pick<?= $isSelected ? ' selected' : '' ?>"
               <?= $isSelected ? 'aria-current="true"' : '' ?>
               data-type="<?= $esc($code) ?>"
               data-title="<?= $esc($documentType['name']) ?>"
               data-desc="<?= $esc($documentType['description']) ?>"
               data-fee="<?= $esc($documentType['summary']) ?>">
              <span class="chip <?= $esc($chipClasses[$code] ?? 'blue') ?>"><?= $esc($chipLetters[$code] ?? strtoupper(substr($code, 0, 1))) ?></span>
              <h3><?= $esc($documentType['name']) ?></h3>
              <p><?= $esc($documentType['summary']) ?></p>
            </a>
          <?php endforeach; ?>
        </div>

        <!-- Request form -->
        <section class="card form-card" id="doc-card">
          <h2 id="doc-title"><?= $esc($selectedType['name'] ?? 'Document request') ?></h2>
          <p class="desc" id="doc-desc"><?= $esc($selectedType['description'] ?? 'Choose a document to request.') ?></p>

          <form id="doc-form" novalidate>
            <input type="hidden" name="document_type" id="document-type" value="<?= $esc($selectedType['code'] ?? '') ?>">
            <div class="field-grid">
              <label class="field"><span>Full name</span><input type="text" name="full_name" maxlength="80" value="<?= $esc($userFullName) ?>" readonly></label>
              <label class="field"><span>Resident ID</span><input type="text" name="resident_id" maxlength="80" value="<?= $esc($userResidentCode) ?>" readonly></label>
              <label class="field"><span>Address</span><input type="text" name="address" maxlength="80" value="<?= $esc($userAddress) ?>" readonly></label>
              <label class="field"><span>Household</span><input type="text" name="household" maxlength="80" value="<?= $esc($userHousehold) ?>" readonly></label>
            </div>

            <label class="field-label" for="purpose">Purpose of request</label>
            <label class="field area">
              <textarea id="purpose" name="purpose" rows="3" maxlength="300" required placeholder="e.g. Employment requirement, scholarship application, hospital assistance"></textarea>
            </label>

            <p class="error" id="doc-error" role="alert" hidden></p>

            <div class="form-foot">
              <button type="submit" class="btn-primary" id="doc-submit">Submit request</button>
              <span class="fee-note" id="doc-fee"><?= $esc($selectedType['summary'] ?? '') ?></span>
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
  var form = document.getElementById('doc-form');
  if (!picks.length || !form) return;

  var params = new URLSearchParams(window.location.search);
  var type = params.get('type') || picks[0].dataset.type;
  var active = document.querySelector('.doc-picker .pick[data-type="' + type + '"]');
  if (!active) active = picks[0];

  var titleEl = document.getElementById('doc-title');
  var descEl = document.getElementById('doc-desc');
  var feeEl = document.getElementById('doc-fee');
  var typeInput = document.getElementById('document-type');
  var errorEl = document.getElementById('doc-error');
  var submitBtn = document.getElementById('doc-submit');
  var purposeEl = document.getElementById('purpose');

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (char) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char];
    });
  }

  function selectPick(pick) {
    active = pick;
    picks.forEach(function (p) { p.classList.remove('selected'); p.removeAttribute('aria-current'); });
    pick.classList.add('selected');
    pick.setAttribute('aria-current', 'true');
    titleEl.textContent = pick.dataset.title;
    descEl.textContent = pick.dataset.desc;
    feeEl.textContent = pick.dataset.fee;
    if (typeInput) typeInput.value = pick.dataset.type;
  }

  picks.forEach(function (pick) {
    pick.addEventListener('click', function (event) {
      event.preventDefault();
      selectPick(pick);
      var url = new URL(window.location.href);
      url.searchParams.set('type', pick.dataset.type);
      history.replaceState(null, '', url);
    });
  });

  selectPick(active);

  function showError(message) {
    errorEl.textContent = message;
    errorEl.hidden = false;
  }

  function renderSuccess(request) {
    document.getElementById('doc-card').innerHTML =
      '<div class="done">' +
        '<span class="chip">✓</span>' +
        '<h2>Request submitted</h2>' +
        '<p>Your ' + escapeHtml(request.document_name) + ' request is now pending review.</p>' +
        '<p>Reference number: <span class="ref-no">' + escapeHtml(request.reference_no) + '</span></p>' +
        '<p>Bring this number and a valid ID when you claim it at the barangay hall.</p>' +
        '<div class="form-foot">' +
          '<a href="track.php" class="btn-primary">Track my request</a>' +
          '<a href="documents.php" class="link">Request another document</a>' +
        '</div>' +
      '</div>';
  }

  form.addEventListener('submit', async function (event) {
    event.preventDefault();
    errorEl.hidden = true;

    var purpose = purposeEl.value.trim();
    if (purpose.length < 5) {
      showError('Describe the purpose of your request (at least 5 characters).');
      purposeEl.focus();
      return;
    }

    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting…';

    try {
      var response = await fetch('../../backend/api/document-requests.php', {
        method: 'POST',
        headers: {
          Accept: 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-Token': csrfMeta ? csrfMeta.content : ''
        },
        credentials: 'same-origin',
        body: JSON.stringify({
          document_type: typeInput.value,
          purpose: purpose
        })
      });
      var result = await response.json();

      if (!response.ok || !result.ok) {
        throw new Error(result.error || 'Unable to submit your request right now.');
      }

      renderSuccess(result.request);
    } catch (error) {
      showError(error.message || 'Unable to submit your request right now.');
      submitBtn.disabled = false;
      submitBtn.textContent = 'Submit request';
    }
  });
})();
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
