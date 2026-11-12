<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/reports.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?= $esc($settingsBarangayName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= url('assets/admin/css/style.css') ?>">
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
            color: var(--text-muted);
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
            color: var(--text-dark);
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

        .report-table tr.report-section-row td {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #ffffff;
            background: var(--primary-green) !important;
            border-color: var(--primary-green);
        }

        .report-table.report-field-table tbody tr:not(.report-section-row) td:first-child {
            font-weight: 700;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .table-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03), 0 8px 20px -14px rgba(15, 23, 42, 0.22);
            overflow: hidden;
        }

        .table-card > .p-4 {
            padding: 1.1rem 1.5rem !important;
        }

        .report-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            padding: 1.35rem 1.4rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.03), 0 8px 20px -14px rgba(15, 23, 42, 0.22);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .report-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04), 0 14px 28px -16px rgba(15, 23, 42, 0.28);
        }

        .report-record-card {
            transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .report-record-card:hover {
            transform: translateY(-3px);
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.04), 0 14px 28px -16px rgba(15, 23, 42, 0.28);
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
            color: var(--text-dark);
        }

        .report-record-sub {
            margin: 0 0 1rem;
            font-size: 0.78rem;
            color: var(--text-muted);
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
            color: var(--text-dark);
        }

        .report-record-meta-row i {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        .report-record-actions {
            margin-top: auto;
        }

        .report-record-actions .btn {
            border-radius: 8px;
        }

        .report-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            margin-bottom: 0.9rem;
            font-size: 1.05rem;
            color: #f59e0b;
            background: #fef3c7;
            border-radius: 10px;
        }

        .report-title {
            margin-bottom: 1.25rem;
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.4;
            color: var(--text-dark);
        }

        @media print {
            @page {
                size: landscape;
                margin: 12mm;
            }

            #sidebar,
            .no-print {
                display: none !important;
            }

            #content {
                margin-left: 0 !important;
                width: 100% !important;
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
<div class="wrapper">
    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">M</div>
            <div><h6 class="mb-0 fw-bold"><?= $esc($settingsBarangayName) ?></h6><small class="text-muted">Office of the Chairman</small></div>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="<?= url('admin/dashboard') ?>"><i class="bi bi-grid"></i> Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('admin/budget') ?>"><i class="bi bi-wallet2"></i> Budget</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('admin/projects') ?>"><i class="bi bi-kanban"></i> Projects</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('admin/residents') ?>"><i class="bi bi-people"></i> Residents</a></li>
            <!-- ACTIVE CLASS MOVED HERE -->
            <li class="nav-item"><a class="nav-link active" href="<?= url('admin/reports') ?>"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('admin/attendance') ?>"><i class="bi bi-calendar-check"></i> Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="<?= url('admin/settings') ?>"><i class="bi bi-gear"></i> Settings</a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="user-card">
                <small class="text-muted d-block mb-1">SIGNED IN AS</small>
                <strong class="d-block"><?= $esc($settingsChairmanName) ?></strong>
                <small class="text-muted">Chairman - Term <?= $esc($settingsTerm) ?></small>
            </div>
            <button class="btn btn-outline btn-sm w-100 mt-2" id="admin-signout" type="button">Sign out</button>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div id="content">
        <div class="page-header">
            <div class="page-header-title d-flex align-items-center gap-3">
                <button class="btn btn-light border d-lg-none" id="adminSidebarToggle" type="button" aria-label="Toggle menu">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h3 class="fw-bold mb-1">Reports</h3>
                    <p class="text-muted mb-0 page-header-subtitle">Generate and export official barangay records</p>
                </div>
            </div>
            <form class="page-header-filter d-flex align-items-center gap-2 mb-0 flex-wrap" method="get" action="<?= url('admin/reports') ?>">
                <label for="reportYear" class="form-label mb-0">Year</label>
                <select name="year" id="reportYear" class="form-select form-select-sm page-header-year" aria-label="Select report year" style="width: 110px;" onchange="this.form.submit()">
                    <option value=""<?= $reportYear === null ? ' selected' : '' ?>>All</option>
                    <?php foreach ($reportYears as $yearOption): ?>
                        <option value="<?= $yearOption ?>"<?= $yearOption === $reportYear ? ' selected' : '' ?>><?= $yearOption ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>

        <!-- REPORTS SPECIFIC CONTENT -->
        <?php if ($selectedReport !== null): ?>
            <?php
                $reportGeneratedAt = new DateTimeImmutable($selectedReport['generated_at']);
                $reportRows = $selectedReport['data'];
            ?>
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 no-print">
                <a href="<?= url('admin/reports') ?><?= $reportYear !== null ? '?year=' . $reportYear : '' ?>" class="btn btn-light border btn-sm fw-semibold">
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
                    <?php elseif ($selectedReport['report_type'] === 'residents'): ?>
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
                    <?php elseif ($selectedReport['report_type'] === 'projects'): ?>
                        <?php
                            $projectReport = is_array($reportRows) && isset($reportRows[0]) && is_array($reportRows[0]) ? $reportRows[0] : [];
                            $projectText = static function (string $key, string $fallback = '—') use ($projectReport): string {
                                $value = $projectReport[$key] ?? '';
                                if (!is_scalar($value) || trim((string) $value) === '') {
                                    return $fallback;
                                }
                                return nl2br(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'));
                            };
                            $projectBudgetValue = $projectReport['total_budget'] ?? null;
                            $projectBudget = is_numeric($projectBudgetValue) ? '₱' . number_format((float) $projectBudgetValue, 2) : '—';
                        ?>
                        <table class="report-table report-field-table" id="reportDataTable">
                            <thead>
                                <tr>
                                    <th style="width: 28%;">Field</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="report-section-row"><td colspan="2">I. Project Information</td></tr>
                                <tr><td>Project Title</td><td class="fw-semibold"><?= $projectText('project_title') ?></td></tr>
                                <tr><td>Sector</td><td><?= $projectText('sector') ?></td></tr>
                                <tr><td>Project Location</td><td><?= $projectText('location') ?></td></tr>
                                <tr><td>Project Category</td><td><?= $projectText('category') ?></td></tr>
                                <tr><td>Project Contractor</td><td><?= $projectText('contractor') ?></td></tr>
                                <tr><td>Project Start Date</td><td><?= $projectText('start_date') ?></td></tr>
                                <tr><td>Target Completion Date</td><td><?= $projectText('target_date') ?></td></tr>

                                <tr class="report-section-row"><td colspan="2">II. Project Description</td></tr>
                                <tr><td>Description</td><td><?= $projectText('description') ?></td></tr>

                                <tr class="report-section-row"><td colspan="2">III. Project Objectives</td></tr>
                                <tr><td>Objectives</td><td><?= $projectText('objectives') ?></td></tr>

                                <tr class="report-section-row"><td colspan="2">IV. Project Budget and Funding</td></tr>
                                <tr><td>Total Approved Budget</td><td class="fw-semibold"><?= $esc($projectBudget) ?></td></tr>
                                <tr><td>Source of Funds</td><td><?= $projectText('fund_source') ?></td></tr>

                                <tr class="report-section-row"><td colspan="2">V. Project Challenges and Issues</td></tr>
                                <tr><td>Challenges and Issues Encountered</td><td><?= $projectText('challenges') ?></td></tr>
                                <tr><td>Actions Taken</td><td><?= $projectText('actions') ?></td></tr>

                                <tr class="report-section-row"><td colspan="2">VI. Recommendations</td></tr>
                                <tr><td>Recommendations</td><td><?= $projectText('recommendations') ?></td></tr>

                                <tr class="report-section-row"><td colspan="2">VII. Certification and Approval</td></tr>
                                <tr><td>Prepared by</td><td><?= $projectText('prepared_by') ?></td></tr>
                                <tr><td>Reviewed by</td><td><?= $projectText('reviewed_by') ?></td></tr>
                                <tr><td>Approved by</td><td><?= $projectText('approved_by') ?></td></tr>
                                <tr><td>Date</td><td><?= $projectText('report_date') ?></td></tr>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <?php if ($reportRows): ?>
                            <?php $firstRow = reset($reportRows); ?>
                            <table class="report-table" id="reportDataTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <?php foreach (array_keys(is_array($firstRow) ? $firstRow : []) as $column): ?>
                                            <th><?= $esc(ucwords(str_replace('_', ' ', (string) $column))) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reportRows as $index => $row): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <?php foreach ((array) $row as $value): ?>
                                                <td><?= is_scalar($value) ? $esc($value) : '—' ?></td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center text-muted py-4 mb-0">No records found.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php if ($savedReports): ?>
                    <?php foreach ($savedReports as $savedReport): ?>
                        <?php
                            $savedGeneratedAt = new DateTimeImmutable($savedReport['generated_at']);
                            $reportIcons = [
                                'residents' => 'bi-people-fill',
                                'payments' => 'bi-cash-coin',
                                'budget' => 'bi-wallet2',
                                'projects' => 'bi-kanban',
                                'blotter' => 'bi-shield-exclamation',
                                'documents' => 'bi-file-earmark-text',
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
                                    <a class="btn btn-primary fw-semibold w-100" href="<?= url('admin/reports') ?>?report_id=<?= (int) $savedReport['id'] ?>">
                                        <i class="bi bi-eye me-1"></i> View Report
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="report-card text-center py-5">
                            <div class="report-icon mx-auto"><i class="bi bi-file-earmark-text"></i></div>
                            <div class="report-title mb-2">No reports available<?= $reportYear !== null ? ' for ' . $reportYear : '' ?></div>
                            <p class="text-muted small mb-0">Generate a report from the Residents, Payments, or Projects page to see it here.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.2/dist/jspdf.plugin.autotable.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const downloadBtn = document.getElementById("download-report-pdf");
        const reportTable = document.getElementById("reportDataTable");

        if (!downloadBtn || !reportTable) {
            return;
        }

        const sectionGreen = [31, 77, 58];

        const pdfText = (value) => String(value)
            .replace(/₱/g, "PHP ")
            .replace(/\u00a0/g, " ")
            .replace(/[ \t]+/g, " ")
            .trim();

        const cellText = (element) => {
            const clone = element.cloneNode(true);
            clone.querySelectorAll("br").forEach((br) => br.replaceWith("\n"));
            return pdfText(clone.textContent)
                .split("\n")
                .map((line) => line.trim())
                .filter((line) => line !== "")
                .join("\n");
        };

        const headRows = Array.from(reportTable.querySelectorAll("thead tr")).map((tr) =>
            Array.from(tr.querySelectorAll("th")).map((th) => pdfText(th.textContent))
        );

        const bodyRows = Array.from(reportTable.querySelectorAll("tbody tr")).map((tr) => {
            if (tr.classList.contains("report-section-row")) {
                return [{
                    content: pdfText(tr.textContent),
                    colSpan: 2,
                    styles: {
                        fillColor: sectionGreen,
                        textColor: [255, 255, 255],
                        fontStyle: "bold",
                        fontSize: 8,
                    },
                }];
            }
            return Array.from(tr.querySelectorAll("td")).map((td) => cellText(td));
        });

        downloadBtn.addEventListener("click", function () {
            if (!window.jspdf || !window.jspdf.jsPDF) {
                window.print();
                return;
            }

            const isFieldTable = reportTable.classList.contains("report-field-table");
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({
                orientation: isFieldTable ? "portrait" : "landscape",
                unit: "mm",
                format: "a4",
            });

            if (typeof doc.autoTable !== "function") {
                window.print();
                return;
            }

            const pageWidth = doc.internal.pageSize.getWidth();

            doc.setFont("helvetica", "bold");
            doc.setFontSize(16);
            doc.text(pdfText(downloadBtn.dataset.barangay || ""), pageWidth / 2, 15, { align: "center" });

            doc.setFont("helvetica", "normal");
            doc.setFontSize(10);
            doc.text("Municipality of " + pdfText(downloadBtn.dataset.municipality || ""), pageWidth / 2, 21, { align: "center" });

            doc.setFont("helvetica", "bold");
            doc.setFontSize(11);
            doc.text("OFFICIAL " + pdfText(downloadBtn.dataset.title || "REPORT"), pageWidth / 2, 29, { align: "center" });

            doc.setFont("helvetica", "normal");
            doc.setFontSize(9);
            doc.text("Barangay Chairman: " + pdfText(downloadBtn.dataset.chairman || ""), pageWidth / 2, 35, { align: "center" });
            doc.text("Date: " + pdfText(downloadBtn.dataset.date || ""), pageWidth / 2, 40, { align: "center" });

            doc.autoTable({
                head: headRows.length > 0 ? headRows : undefined,
                body: bodyRows,
                startY: 46,
                theme: "grid",
                styles: { fontSize: 7.5, cellPadding: 1.8, overflow: "linebreak", valign: "top" },
                headStyles: { fillColor: [248, 250, 252], textColor: [51, 65, 85], fontStyle: "bold" },
                alternateRowStyles: { fillColor: [248, 250, 252] },
                margin: { left: 10, right: 10 },
                columnStyles: isFieldTable ? { 0: { cellWidth: 55, fontStyle: "bold" } } : undefined,
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
<script>window.EB_BASE = <?= json_encode(url('')) ?>;</script>
<script src="<?= url('assets/admin/js/main.js') ?>"></script>
</body>
</html>
