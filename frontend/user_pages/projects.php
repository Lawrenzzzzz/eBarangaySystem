<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/resident/projects.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Barangay Bucana | Projects</title>
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
      <a href="projects.php" class="active" aria-current="page">Projects</a>
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

  <div class="layout single projects-page">
    <!-- Page title -->
    <section class="greeting">
      <div>
        <p class="date">Projects</p>
        <h1>Barangay projects</h1>
      </div>
      <p class="summary muted"><?= $projectStats['total'] ?> <?= $projectStats['total'] === 1 ? 'project' : 'projects' ?> in the barangay</p>
    </section>

    <main class="col-main">

      <!-- Summary -->
      <section class="stats" aria-label="Project summary">
        <div class="card stat">
          <span class="label">Total projects</span>
          <strong><?= $projectStats['total'] ?></strong>
          <small class=""><?= $projectStats['total'] > 0 ? 'Recorded projects' : 'No projects yet' ?></small>
        </div>
        <div class="card stat">
          <span class="label">Ongoing</span>
          <strong><?= $projectStats['ongoing'] ?></strong>
          <small class="warn"><?= $projectStats['ongoing'] > 0 ? 'In progress' : 'Nothing in progress' ?></small>
        </div>
        <div class="card stat">
          <span class="label">Completed</span>
          <strong><?= $projectStats['completed'] ?></strong>
          <small class="good"><?= $projectStats['completed'] > 0 ? 'Finished projects' : 'No completed projects' ?></small>
        </div>
        <div class="card stat">
          <span class="label">Planned</span>
          <strong><?= $projectStats['planned'] ?></strong>
          <small class=""><?= $projectStats['planned'] > 0 ? 'Upcoming projects' : 'Nothing planned' ?></small>
        </div>
      </section>

      <!-- Search and filters -->
      <section class="card project-toolbar">
        <div class="project-search">
          <svg class="project-search-icon" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m20 20-3.2-3.2"></path>
          </svg>
          <input type="search" id="project-search" placeholder="Search by project name, sector, or status" aria-label="Search projects" autocomplete="off">
        </div>
        <div class="project-filters" role="group" aria-label="Filter projects by status">
          <button type="button" class="filter-chip is-active" data-filter="all" aria-pressed="true">All <span><?= $projectStats['total'] ?></span></button>
          <button type="button" class="filter-chip" data-filter="ongoing" aria-pressed="false">Ongoing <span><?= $projectStats['ongoing'] ?></span></button>
          <button type="button" class="filter-chip" data-filter="completed" aria-pressed="false">Completed <span><?= $projectStats['completed'] ?></span></button>
          <button type="button" class="filter-chip" data-filter="planned" aria-pressed="false">Planned <span><?= $projectStats['planned'] ?></span></button>
        </div>
      </section>

      <!-- Project list -->
      <section class="project-grid" id="project-grid">
        <?php foreach ($projects as $project): ?>
          <?php
            $report = $project['report'] ?? null;
            $statusTone = $statusTones[$project['status']] ?? 'info';
            $sectorTone = $sectorTones[$project['sector']] ?? 'blue';
            $progress = max(0, min(100, (int) $project['progress']));
            $searchText = strtolower(
                $project['title'] . ' ' . $project['sector'] . ' ' . $project['description'] . ' ' .
                $project['status'] . ' ' . $project['status_label']
            );
            $reportFields = [];
            if (is_array($report)) {
                foreach ([
                    'location' => 'Location',
                    'contractor' => 'Contractor',
                    'fund_source' => 'Fund source',
                    'category' => 'Category',
                ] as $field => $fieldLabel) {
                    if (!empty($report[$field])) {
                        $reportFields[$fieldLabel] = (string) $report[$field];
                    }
                }
            }
          ?>
          <article class="card project-card" data-status="<?= $esc($project['status']) ?>" data-search="<?= $esc($searchText) ?>">
            <div class="project-top">
              <span class="sector-tag <?= $esc($sectorTone) ?>"><i></i><?= $esc($project['sector']) ?></span>
              <span class="badge <?= $esc($statusTone) ?>"><?= $esc($project['status_label']) ?></span>
            </div>

            <h3 class="project-title"><?= $esc($project['title']) ?></h3>
            <p class="project-desc"><?= $project['description'] !== '' ? nl2br($esc($project['description'])) : 'No description provided.' ?></p>

            <div class="project-info">
              <div class="project-info-item">
                <span>Budget</span>
                <strong><?= $esc($project['budget_display']) ?></strong>
              </div>
              <div class="project-info-item">
                <span>Timeline</span>
                <strong><?= $esc($project['timeline_display']) ?></strong>
              </div>
            </div>

            <div class="project-progress">
              <div class="project-progress-head">
                <span>Progress</span>
                <strong><?= $progress ?>%</strong>
              </div>
              <div class="progress-track" role="progressbar" aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Project progress">
                <span class="progress-fill" style="width: <?= $progress ?>%"></span>
              </div>
            </div>

            <?php if ($reportFields || !empty($report['objectives'])): ?>
              <details class="project-report">
                <summary>View project details</summary>
                <dl class="project-report-grid">
                  <?php foreach ($reportFields as $fieldLabel => $fieldValue): ?>
                    <div>
                      <dt><?= $esc($fieldLabel) ?></dt>
                      <dd><?= $esc($fieldValue) ?></dd>
                    </div>
                  <?php endforeach; ?>
                  <?php if (!empty($report['objectives'])): ?>
                    <div class="wide">
                      <dt>Objectives</dt>
                      <dd><?= nl2br($esc($report['objectives'])) ?></dd>
                    </div>
                  <?php endif; ?>
                </dl>
              </details>
            <?php endif; ?>

            <div class="project-foot">
              <span>Updated <?= $esc($project['updated_display']) ?></span>
            </div>
          </article>
        <?php endforeach; ?>

        <p class="card project-empty" id="project-empty"<?= $projects ? ' hidden' : '' ?>>
          <?= $projects ? 'No projects match your search or filter.' : 'No projects have been posted yet.' ?>
        </p>
      </section>
    </main>
  </div>
</div>

<script>
(function () {
  var input = document.getElementById('project-search');
  var items = Array.prototype.slice.call(document.querySelectorAll('.project-card[data-search]'));
  var empty = document.getElementById('project-empty');
  var filters = Array.prototype.slice.call(document.querySelectorAll('.filter-chip'));
  if (!input || !empty) return;

  var activeFilter = 'all';

  var applyFilters = function () {
    var query = input.value.trim().toLowerCase();
    var visible = 0;

    items.forEach(function (item) {
      var matchesSearch = query === '' || item.dataset.search.indexOf(query) !== -1;
      var matchesStatus = activeFilter === 'all' || item.dataset.status === activeFilter;
      var match = matchesSearch && matchesStatus;
      item.hidden = !match;
      if (match) visible++;
    });

    if (items.length === 0) {
      empty.hidden = false;
      empty.textContent = 'No projects have been posted yet.';
    } else if (visible === 0) {
      empty.hidden = false;
      empty.textContent = 'No projects match your search or filter.';
    } else {
      empty.hidden = true;
    }
  };

  filters.forEach(function (button) {
    button.addEventListener('click', function () {
      activeFilter = button.dataset.filter || 'all';
      filters.forEach(function (chip) {
        chip.classList.toggle('is-active', chip === button);
        chip.setAttribute('aria-pressed', chip === button ? 'true' : 'false');
      });
      applyFilters();
    });
  });

  input.addEventListener('input', applyFilters);
})();
</script>
<script src="assets/js/app.js"></script>
</body>
</html>
