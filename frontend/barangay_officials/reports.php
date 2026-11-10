<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/official/reports.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - E-Barangay Official</title>
  
  <link rel="stylesheet" href="libs/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <link rel="stylesheet" href="css/ebarangay.css">
  <style>
    .report-sheet-header {
      margin-bottom: 1.5rem;
      text-align: center;
    }

    .report-sheet-header .barangay {
      font-size: 1.4rem;
      font-weight: 800;
      letter-spacing: 0.02em;
    }

    .report-sheet-header .municipality {
      font-size: 0.85rem;
      color: var(--eb-text-muted);
    }

    .report-sheet-header .title {
      margin-top: 0.85rem;
      font-size: 0.95rem;
      font-weight: 700;
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    .report-sheet-header .meta {
      margin-top: 0.25rem;
      font-size: 0.85rem;
      color: var(--eb-text-dark);
    }

    .report-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.8rem;
    }

    .report-table th,
    .report-table td {
      padding: 0.5rem;
      border: 1px solid #cbd5e1;
      vertical-align: top;
    }

    .report-table thead th {
      font-size: 0.7rem;
      font-weight: 700;
      text-align: left;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: #334155;
      background: #f8fafc;
    }

    .report-table tbody tr:nth-child(even) td {
      background: #f8fafc;
    }

    .report-record-card {
      display: flex;
      flex-direction: column;
      height: 100%;
      padding: 1.35rem 1.4rem;
      transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .report-record-card:hover {
      transform: translateY(-3px);
      border-color: #cbd5e1;
      box-shadow: var(--eb-shadow-card-hover);
    }

    .report-record-top {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 0.75rem;
      margin-bottom: 1rem;
    }

    .report-record-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 48px;
      height: 48px;
      font-size: 1.3rem;
      color: #fff;
      background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
      border-radius: 14px;
      box-shadow: 0 8px 16px -8px rgba(217, 119, 6, 0.75);
    }

    .report-record-badge {
      padding: 0.3rem 0.55rem;
      font-size: 0.62rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #15803d;
      background: #dcfce7;
      border-radius: 999px;
    }

    .report-record-title {
      margin: 0 0 0.2rem;
      font-size: 1rem;
      font-weight: 700;
      color: var(--eb-text-dark);
    }

    .report-record-sub {
      margin: 0 0 1rem;
      font-size: 0.78rem;
      color: var(--eb-text-muted);
    }

    .report-record-meta {
      display: grid;
      gap: 0.55rem;
      margin-bottom: 1.25rem;
    }

    .report-record-meta-row {
      display: flex;
      align-items: center;
      gap: 0.55rem;
      font-size: 0.8rem;
      color: var(--eb-text-dark);
    }

    .report-record-meta-row i {
      font-size: 0.9rem;
      color: var(--eb-text-muted);
    }

    .report-record-actions {
      margin-top: auto;
    }

    .report-record-actions .btn {
      border-radius: var(--eb-radius-sm);
    }

    @media print {
      @page {
        size: landscape;
        margin: 12mm;
      }

      .eb-sidebar,
      .eb-topbar,
      .no-print {
        display: none !important;
      }

      .eb-main {
        margin-left: 0 !important;
      }

      .eb-content {
        padding: 0 !important;
      }

      body {
        background: #fff !important;
      }

      .table-card {
        padding: 0 !important;
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
      }

      .report-table thead {
        display: table-header-group;
      }

      .report-table tr {
        break-inside: avoid;
      }
    }
  </style>
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
      <a class="nav-link" href="document-requests.php">
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
      
      <!-- ACTIVE HIGHLIGHT I2 SA REPORTS -->
      <a class="nav-link active" href="reports.php">
        <span class="nav-dot"></span><span>Reports</span>
      </a>
      
      <div class="settings-parent">
        <a class="nav-link" href="document-fees.php">
          <span class="nav-dot"></span><span>Settings</span>
        </a>
        <button class="settings-caret-btn collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#officialSettingsSubmenu" aria-expanded="false" aria-controls="officialSettingsSubmenu" aria-label="Toggle settings options">
          <i class="bi bi-chevron-down"></i>
        </button>
      </div>

      <div class="collapse" id="officialSettingsSubmenu">
        <div class="settings-submenu">
          <a class="nav-link" href="document-fees.php"><span class="sub-dot"></span><span>Documents Fees</span></a>
          <a class="nav-link" href="change-password.php"><span class="sub-dot"></span><span>Change Password</span></a>
          <a class="nav-link" href="settings.php#profile-information"><span class="sub-dot"></span><span>Profile Information</span></a>
        </div>
      </div>
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
          <h5>Reports</h5>
          <small>Municipality of Nasugbu - Barangay Bucana</small>
        </div>
      </div>

      <form class="d-flex align-items-center gap-2 mb-0" method="get" action="reports.php">
        <label for="reportYear" class="form-label mb-0">Year</label>
        <select name="year" id="reportYear" class="form-select form-select-sm" aria-label="Select report year" style="width: 100px;" onchange="this.form.submit()">
          <?php for ($year = $currentYear; $year >= $currentYear - 5; $year--): ?>
            <option value="<?= $year ?>"<?= $year === $reportYear ? ' selected' : '' ?>><?= $year ?></option>
          <?php endfor; ?>
        </select>
      </form>
    </div>

    <div class="eb-content">
      <?php if ($reportActionError !== ''): ?>
        <div class="alert alert-danger py-2" role="alert"><?= $esc($reportActionError) ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['generated'])): ?>
        <div class="alert alert-success py-2 no-print" role="alert">Report generated and saved successfully.</div>
      <?php endif; ?>

      <?php if ($selectedReport !== null): ?>
        <?php
          $reportGeneratedAt = new DateTimeImmutable($selectedReport['generated_at']);
          $reportRows = $selectedReport['data'];
        ?>
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
          <a href="reports.php?year=<?= $reportYear ?>" class="btn btn-light border btn-sm fw-semibold">
            <i class="bi bi-arrow-left me-1"></i> Back
          </a>
          <button
            type="button"
            class="btn btn-primary btn-sm fw-semibold"
            id="download-report-pdf"
            data-barangay="<?= $esc($selectedReport['barangay_name']) ?>"
            data-municipality="<?= $esc($selectedReport['municipality']) ?>"
            data-chairman="<?= $esc($selectedReport['chairman_name']) ?>"
            data-date="<?= $esc($reportGeneratedAt->format('F j, Y')) ?>"
            data-report-id="<?= $selectedReport['id'] ?>"
            data-title="<?= $esc($selectedReport['title']) ?>"
          >
            <i class="bi bi-download me-1"></i> Download PDF
          </button>
        </div>

        <div class="table-card p-4" id="reportSheet">
          <div class="report-sheet-header">
            <div class="barangay"><?= $esc($selectedReport['barangay_name']) ?></div>
            <div class="municipality">Municipality of <?= $esc($selectedReport['municipality']) ?></div>
            <div class="title">Official <?= $esc($selectedReport['title']) ?></div>
            <div class="meta">Barangay Chairman: <?= $esc($selectedReport['chairman_name']) ?></div>
            <div class="meta">Date: <?= $esc($reportGeneratedAt->format('F j, Y')) ?></div>
          </div>

          <div class="table-responsive">
            <?php if ($selectedReport['report_type'] === 'payments'): ?>
              <table class="report-table" id="reportDataTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Reference No</th>
                    <th>Request Ref</th>
                    <th>Payee</th>
                    <th>Document</th>
                    <th>Payment Method</th>
                    <th>Amount</th>
                    <th>Date Paid</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($reportRows): ?>
                    <?php foreach ($reportRows as $index => $payment): ?>
                      <?php
                        $paidAt = '—';
                        $paymentPaidAt = (string) ($payment['paid_at'] ?? '');
                        if ($paymentPaidAt !== '') {
                            $timestamp = strtotime($paymentPaidAt);
                            if ($timestamp !== false) {
                                $paidAt = date('M j, Y', $timestamp);
                            }
                        }
                      ?>
                      <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $esc($payment['reference_no'] ?? '') ?></td>
                        <td><?= ($payment['request_reference'] ?? '') !== '' ? $esc($payment['request_reference']) : '—' ?></td>
                        <td><?= $esc($payment['payee'] ?? '') ?></td>
                        <td><?= ($payment['document_name'] ?? '') !== '' ? $esc($payment['document_name']) : '—' ?></td>
                        <td><?= ($payment['method_label'] ?? '') !== '' ? $esc($payment['method_label']) : '—' ?></td>
                        <td class="fw-semibold"><?= $esc($payment['amount_label'] ?? '') ?></td>
                        <td><?= $esc($paidAt) ?></td>
                        <td><?= ($payment['status_label'] ?? '') !== '' ? $esc($payment['status_label']) : '—' ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="9" class="text-center text-muted py-4">No payments found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            <?php else: ?>
              <table class="report-table" id="reportDataTable">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Resident Code</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Suffix</th>
                    <th>Address</th>
                    <th>Birth Date</th>
                    <th>Sex</th>
                    <th>Civil Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($reportRows): ?>
                    <?php foreach ($reportRows as $index => $resident): ?>
                      <?php
                        $birthDate = '—';
                        $residentBirthDate = (string) ($resident['birth_date'] ?? '');
                        if ($residentBirthDate !== '') {
                            $timestamp = strtotime($residentBirthDate);
                            if ($timestamp !== false) {
                                $birthDate = date('M j, Y', $timestamp);
                            }
                        }
                      ?>
                      <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $esc($resident['resident_code'] ?? '') ?></td>
                        <td><?= ($resident['first_name'] ?? '') !== '' ? $esc($resident['first_name']) : '—' ?></td>
                        <td><?= ($resident['middle_name'] ?? '') !== '' ? $esc($resident['middle_name']) : '—' ?></td>
                        <td><?= ($resident['last_name'] ?? '') !== '' ? $esc($resident['last_name']) : '—' ?></td>
                        <td><?= ($resident['suffix'] ?? '') !== '' ? $esc($resident['suffix']) : '—' ?></td>
                        <td><?= ($resident['address_line'] ?? '') !== '' ? $esc($resident['address_line']) : '—' ?></td>
                        <td><?= $esc($birthDate) ?></td>
                        <td><?= ($resident['sex'] ?? '') !== '' ? $esc(ucfirst((string) $resident['sex'])) : '—' ?></td>
                        <td><?= ($resident['civil_status'] ?? '') !== '' ? $esc(ucfirst((string) $resident['civil_status'])) : '—' ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="10" class="text-center text-muted py-4">No residents found.</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <?php if ($savedReports): ?>
          <div class="row g-3">
            <?php foreach ($savedReports as $savedReport): ?>
              <?php
                $savedGeneratedAt = new DateTimeImmutable($savedReport['generated_at']);
                $reportIcons = [
                    'residents' => 'bi-people-fill',
                    'payments' => 'bi-cash-coin',
                ];
                $savedIcon = $reportIcons[$savedReport['report_type']] ?? 'bi-file-earmark-bar-graph-fill';
                $savedTypeLabel = ucfirst(str_replace('_', ' ', $savedReport['report_type']));
              ?>
              <div class="col-md-6 col-lg-4">
                <div class="report-card report-record-card">
                  <div class="report-record-top">
                    <div class="report-record-icon"><i class="bi <?= $esc($savedIcon) ?>"></i></div>
                    <span class="report-record-badge">Saved</span>
                  </div>

                  <h6 class="report-record-title"><?= $esc($savedReport['title']) ?></h6>
                  <p class="report-record-sub"><?= $esc($savedTypeLabel) ?> report &middot; <?= (int) $savedReport['report_year'] ?></p>

                  <div class="report-record-meta">
                    <div class="report-record-meta-row">
                      <i class="bi bi-calendar3"></i>
                      <span>Generated <?= $esc($savedGeneratedAt->format('M j, Y g:i A')) ?></span>
                    </div>
                    <div class="report-record-meta-row">
                      <i class="bi bi-list-ol"></i>
                      <span><?= (int) $savedReport['record_count'] ?> <?= (int) $savedReport['record_count'] === 1 ? 'record' : 'records' ?></span>
                    </div>
                    <div class="report-record-meta-row">
                      <i class="bi bi-person-badge"></i>
                      <span>By <?= $esc($savedReport['generated_by_name']) ?></span>
                    </div>
                  </div>

                  <div class="report-record-actions">
                    <a class="btn btn-primary fw-semibold w-100" href="reports.php?year=<?= $reportYear ?>&amp;report_id=<?= $savedReport['id'] ?>">
                      <i class="bi bi-eye me-1"></i> View Report
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="report-card text-center py-5">
            <div class="report-icon mx-auto"><i class="bi bi-file-earmark-text"></i></div>
            <div class="report-title mb-2">No reports available for <?= $reportYear ?></div>
            <p class="text-muted small mb-0">Generate a report from the Residents or Payments page to see it here.</p>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const downloadBtn = document.getElementById("download-report-pdf");
      const reportTable = document.getElementById("reportDataTable");

      if (!downloadBtn || !reportTable) {
        return;
      }

      const reportTitle = (downloadBtn.dataset.title || "Report").toUpperCase();

      downloadBtn.addEventListener("click", function () {
        if (!window.jspdf || !window.jspdf.jsPDF) {
          window.print();
          return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: "landscape", unit: "mm", format: "a4" });
        const pageWidth = doc.internal.pageSize.getWidth();

        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.text(downloadBtn.dataset.barangay || "", pageWidth / 2, 15, { align: "center" });

        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
        doc.text("Municipality of " + (downloadBtn.dataset.municipality || ""), pageWidth / 2, 21, { align: "center" });

        doc.setFont("helvetica", "bold");
        doc.setFontSize(11);
        doc.text("OFFICIAL " + reportTitle, pageWidth / 2, 29, { align: "center" });

        doc.setFont("helvetica", "normal");
        doc.setFontSize(9);
        doc.text("Barangay Chairman: " + (downloadBtn.dataset.chairman || ""), pageWidth / 2, 35, { align: "center" });
        doc.text("Date: " + (downloadBtn.dataset.date || ""), pageWidth / 2, 40, { align: "center" });

        doc.autoTable({
          html: "#reportDataTable",
          startY: 46,
          theme: "grid",
          styles: { fontSize: 7, cellPadding: 1.6, overflow: "linebreak", valign: "top" },
          headStyles: { fillColor: [248, 250, 252], textColor: [51, 65, 85], fontStyle: "bold" },
          alternateRowStyles: { fillColor: [248, 250, 252] },
          margin: { left: 10, right: 10 },
        });

        doc.save(
          (downloadBtn.dataset.title || "report").toLowerCase().replace(/[^a-z0-9]+/g, "-") +
            "-" +
            (downloadBtn.dataset.reportId || "export") +
            "-" +
            new Date().toISOString().slice(0, 10) +
            ".pdf"
        );
      });
    });
  </script>
  <script src="js/app.js"></script>
</body>
</html>