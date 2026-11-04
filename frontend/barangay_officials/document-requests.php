<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/document-requests.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document Requests - E-Barangay Official</title>
  
  <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="css/ebarangay.css">
</head>
<body>

  <div class="eb-sidebar" id="ebSidebar">
    <div class="brand">
      <div class="brand-logo">M</div>
      <div class="brand-text">
        E-Barangay
        <small>Municipal Admin</small>
      </div>
    </div>
    
    <div class="nav flex-column py-2 flex-grow-1">
      <div class="nav-section">Main</div>
      <a class="nav-link" href="dashboard.php">
        <span class="nav-dot"></span><span>Dashboard</span>
      </a>
      <a class="nav-link" href="residents.php">
        <span class="nav-dot"></span><span>Residents</span>
      </a>
      
      <!-- ACTIVE HIGHLIGHT I2 SA DOCUMENT REQUESTS -->
      <a class="nav-link active" href="document-requests.php">
        <span class="nav-dot"></span><span>Document Requests</span>
      </a>
      
      <a class="nav-link" href="blotter.php">
        <span class="nav-dot"></span><span>Blotter</span>
      </a>
      <a class="nav-link" href="announcements.php">
        <span class="nav-dot"></span><span>Announcements</span>
      </a>
      <a class="nav-link" href="payments.php">
        <span class="nav-dot"></span><span>Payments</span>
      </a>
      <a class="nav-link" href="reports.php">
        <span class="nav-dot"></span><span>Reports</span>
      </a>
      <a class="nav-link" href="settings.php">
        <span class="nav-dot"></span><span>Settings</span>
      </a>
    </div>
    
    <div class="user-profile">
      <img src="img/captain.jpg" alt="<?= $esc($officialFullName) ?>" class="avatar">
      <div>
        <div class="name"><?= $esc($officialFullName) ?></div>
        <div class="role"><?= $esc($officialRoleName) ?></div>
      </div>
    </div>

    <div class="px-3 pb-3">
      <button class="btn btn-outline-light btn-sm w-100" id="official-signout" type="button">
        <i class="bi bi-box-arrow-right me-1"></i> Sign out
      </button>
    </div>
  </div>

  <div class="eb-main">
    <div class="eb-topbar">
      <div class="d-flex align-items-center gap-3">
        <div class="page-title">
          <h5>Document Requests</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>
    </div>

    <div class="eb-content">
      <?php if ($documentRequestNotice !== ''): ?>
        <div class="alert alert-success py-2" role="alert"><?= $esc($documentRequestNotice) ?></div>
      <?php endif; ?>
      <?php if ($documentRequestError !== ''): ?>
        <div class="alert alert-danger py-2" role="alert"><?= $esc($documentRequestError) ?></div>
      <?php endif; ?>

      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <form class="search-box" method="get" action="document-requests.php" role="search">
          <i class="bi bi-search"></i>
          <input type="hidden" name="status" value="<?= $esc($documentRequestTab) ?>">
          <input type="search" name="q" value="<?= $esc($documentRequestSearch) ?>" placeholder="Search reference, resident, or document..." aria-label="Search document requests">
        </form>
        <span class="text-muted small">
          <?= $documentRequestStats['pending'] ?> pending · <?= $documentRequestStats['processing'] ?> processing · <?= $documentRequestStats['ready'] ?> ready · <?= $documentRequestStats['rejected'] ?> rejected
        </span>
      </div>

      <!-- TABS -->
      <div class="eb-tabs mb-4">
        <?php foreach ($documentRequestTabs as $tabKey => $tab): ?>
          <a class="eb-tab<?= $documentRequestTab === $tabKey ? ' active' : '' ?>"
             href="<?= $esc($documentRequestTabUrl($tabKey)) ?>"
             <?= $documentRequestTab === $tabKey ? 'aria-current="page"' : '' ?>>
            <?= $esc($tab['label']) ?> <span class="opacity-75">(<?= (int) $tab['count'] ?>)</span>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- LIST -->
      <div class="table-card">
        <?php if ($documentRequests): ?>
          <div class="table-scrollable">
            <ul class="queue-list mb-0">
              <?php foreach ($documentRequests as $request): ?>
                <li class="queue-item" data-search="<?= $esc($request['search_text']) ?>">
                  <span class="resident-avatar"><?= $esc($request['initial']) ?></span>

                  <div class="flex-grow-1" style="min-width: 0;">
                    <div class="d-flex flex-wrap align-items-center gap-2">
                      <span class="fw-semibold"><?= $esc($request['full_name']) ?></span>
                      <span class="badge <?= $esc($request['badge_class']) ?>"><?= $esc($request['status_label']) ?></span>
                    </div>
                    <div class="resident-meta">
                      <?= $esc($request['document_name']) ?> · Ref <?= $esc($request['reference_no']) ?> · <?= $esc($request['resident_code']) ?>
                    </div>
                    <div class="small text-muted mt-1 text-truncate" title="<?= $esc($request['purpose']) ?>">
                      <?= $esc($request['purpose']) ?>
                    </div>
                  </div>

                  <div class="text-end small flex-shrink-0 d-flex flex-column align-items-end gap-2">
                    <div>
                      <div class="fw-semibold"><?= $esc($request['fee_label']) ?></div>
                      <div class="text-muted"><?= $esc(date('M j, Y', strtotime($request['created_at']))) ?></div>
                    </div>

                    <?php if ($request['actions']): ?>
                      <div class="d-flex flex-wrap justify-content-end gap-2">
                        <?php foreach ($request['actions'] as $action): ?>
                          <?php if ($action['status'] === 'released' && $request['requires_payment']): ?>
                            <button type="button"
                                    class="btn btn-sm fw-semibold <?= $esc($action['class']) ?>"
                                    data-claim-open="claim-dialog-<?= (int) $request['id'] ?>">
                              <?= $esc($action['label']) ?>
                            </button>
                          <?php else: ?>
                            <?php
                              $isClaim = $action['status'] === 'released';
                              $confirm = $action['status'] === 'rejected'
                                  ? 'Reject this document request?'
                                  : ($isClaim ? 'Mark this document as claimed?' : '');
                            ?>
                            <form method="post" action="document-requests.php" class="d-inline"
                              <?= $confirm !== '' ? 'onsubmit="return confirm(\'' . $esc($confirm) . '\');"' : '' ?>>
                              <input type="hidden" name="csrf_token" value="<?= $esc($csrf) ?>">
                              <input type="hidden" name="action" value="<?= $isClaim ? 'claim' : 'status' ?>">
                              <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
                              <input type="hidden" name="status" value="<?= $esc($action['status']) ?>">
                              <input type="hidden" name="tab" value="<?= $esc($documentRequestTab) ?>">
                              <input type="hidden" name="q" value="<?= $esc($documentRequestSearch) ?>">
                              <button type="submit" class="btn btn-sm fw-semibold <?= $esc($action['class']) ?>">
                                <?= $esc($action['label']) ?>
                              </button>
                            </form>
                          <?php endif; ?>
                        <?php endforeach; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php else: ?>
          <p class="text-center text-muted py-4 mb-0">
            <?= $documentRequestSearch !== '' ? 'No document requests match your search.' : ($documentRequestArchived ? 'No archived requests yet.' : ($documentRequestTab === 'all' ? 'No document requests yet.' : 'No requests with this status.')) ?>
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php foreach ($documentRequests as $request): ?>
    <?php if ($request['status'] === 'ready' && $request['requires_payment']): ?>
      <dialog class="claim-dialog" id="claim-dialog-<?= (int) $request['id'] ?>">
        <form method="post" action="document-requests.php" class="claim-form">
          <input type="hidden" name="csrf_token" value="<?= $esc($csrf) ?>">
          <input type="hidden" name="action" value="claim">
          <input type="hidden" name="request_id" value="<?= (int) $request['id'] ?>">
          <input type="hidden" name="tab" value="<?= $esc($documentRequestTab) ?>">
          <input type="hidden" name="q" value="<?= $esc($documentRequestSearch) ?>">

          <div class="claim-head">
            <div>
              <h6>Payment receipt</h6>
              <small>Ref <?= $esc($request['reference_no']) ?> · <?= $esc($request['document_name']) ?></small>
            </div>
            <button type="button" class="btn-close" data-claim-close aria-label="Close"></button>
          </div>

          <div class="claim-body">
            <div class="receipt-line"><span>Payee</span><strong><?= $esc($officialFullName) ?></strong></div>
            <div class="receipt-line"><span>Amount</span><strong><?= $esc($request['fee_label']) ?></strong></div>
            <div class="receipt-line"><span>Date</span><strong><?= $esc($documentRequestReceiptDate) ?></strong></div>
            <div class="receipt-line"><span>Status</span><strong>Received</strong></div>

            <div class="mt-3">
              <label class="form-label" for="payment-method-<?= (int) $request['id'] ?>">Type of payment</label>
              <select class="form-select" name="payment_method" id="payment-method-<?= (int) $request['id'] ?>">
                <option value="cash">Cash</option>
                <option value="online">Online payment</option>
              </select>
            </div>

            <div class="mt-3">
              <label class="form-label" for="reference-no-<?= (int) $request['id'] ?>">Reference no.</label>
              <input class="form-control" type="text" name="reference_no" id="reference-no-<?= (int) $request['id'] ?>"
                     value="<?= $esc($request['cash_reference']) ?>"
                     data-cash-reference="<?= $esc($request['cash_reference']) ?>"
                     maxlength="80" readonly>
              <div class="form-text">Cash reference is generated automatically. Online payments require the provider's reference number.</div>
            </div>
          </div>

          <div class="claim-foot">
            <button type="button" class="btn btn-light border" data-claim-close>Cancel</button>
            <button type="submit" class="btn btn-orange fw-semibold">Submit &amp; archive</button>
          </div>
        </form>
      </dialog>
    <?php endif; ?>
  <?php endforeach; ?>

  <?php if ($documentRequestArchived): ?>
    <a href="document-requests.php" class="btn btn-orange archive-fab"><i class="bi bi-arrow-left"></i> Back to requests</a>
  <?php else: ?>
    <a href="document-requests.php?status=archived" class="btn btn-orange archive-fab"><i class="bi bi-archive"></i> Archive (<?= (int) $documentRequestStats['archived'] ?>)</a>
  <?php endif; ?>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="js/app.js"></script>
  <script>
  (function () {
    var dialogs = document.querySelectorAll('.claim-dialog');
    if (!dialogs.length) return;

    function syncReference(dialog) {
      var method = dialog.querySelector('[name="payment_method"]');
      var reference = dialog.querySelector('[name="reference_no"]');
      if (!method || !reference) return;

      if (method.value === 'cash') {
        reference.readOnly = true;
        reference.value = reference.dataset.cashReference || '';
      } else {
        reference.readOnly = false;
        reference.value = '';
        reference.focus();
      }
    }

    dialogs.forEach(function (dialog) {
      var method = dialog.querySelector('[name="payment_method"]');
      var form = dialog.querySelector('form');
      var reference = dialog.querySelector('[name="reference_no"]');

      if (method) {
        method.addEventListener('change', function () { syncReference(dialog); });
      }

      dialog.querySelectorAll('[data-claim-close]').forEach(function (button) {
        button.addEventListener('click', function () { dialog.close(); });
      });

      if (form) {
        form.addEventListener('submit', function (event) {
          if (method && method.value === 'online' && (!reference || reference.value.trim().length < 3)) {
            event.preventDefault();
            if (reference) reference.focus();
            return;
          }
          var submit = form.querySelector('button[type="submit"]');
          if (submit) {
            submit.disabled = true;
            submit.textContent = 'Saving…';
          }
        });
      }
    });

    document.querySelectorAll('[data-claim-open]').forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        var dialog = document.getElementById(trigger.dataset.claimOpen);
        if (!dialog) return;
        var method = dialog.querySelector('[name="payment_method"]');
        if (method) method.value = 'cash';
        syncReference(dialog);
        dialog.showModal();
      });
    });
  })();
  </script>
</body>
</html>