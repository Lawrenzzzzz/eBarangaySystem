<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/guard.php';
$currentYear = (int) date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget - Brgy. Bucana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="wrapper">
    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <div class="logo-circle">M</div>
            <div><h6 class="mb-0 fw-bold">Brgy. Bucana</h6><small class="text-muted">Office of the Chairman</small></div>
        </div>
        <ul class="nav flex-column mt-3">
            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-grid"></i> Dashboard</a></li>
            <!-- ACTIVE CLASS MOVED HERE -->
            <li class="nav-item"><a class="nav-link active" href="budget.php"><i class="bi bi-wallet2"></i> Budget</a></li>
            <li class="nav-item"><a class="nav-link" href="projects.php"><i class="bi bi-kanban"></i> Projects</a></li>
            <li class="nav-item"><a class="nav-link" href="residents.php"><i class="bi bi-people"></i> Residents</a></li>
            <li class="nav-item"><a class="nav-link" href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a></li>
            <li class="nav-item"><a class="nav-link" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
        </ul>
        <div class="sidebar-footer">
            <div class="user-card">
                <small class="text-muted d-block mb-1">SIGNED IN AS</small>
                <strong class="d-block">Capt. Choi Seungcheol Batumbakal</strong>
                <small class="text-muted">Chairman - Term 2022-2025</small>
            </div>
            <button class="btn btn-outline btn-sm w-100 mt-2" id="admin-signout" type="button">Sign out</button>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div id="content">
        <div class="page-header">
            <div>
                <h3 class="fw-bold mb-1">Budget</h3>
                <p class="text-muted mb-0">Sector allocations and disbursements - FY <?= $currentYear ?></p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <select class="form-select form-select-sm" id="budget-year-filter" style="width: 100px;">
                    <?php for ($year = $currentYear - 2; $year <= $currentYear; $year++): ?>
                        <option value="<?= $year ?>"<?= $year === $currentYear ? ' selected' : '' ?>><?= $year ?></option>
                    <?php endfor; ?>
                </select>
                <button class="btn btn-outline-secondary btn-sm px-3" type="button">Divide Budget</button>
                <button class="btn btn-primary-green btn-sm px-3" type="button" data-bs-toggle="modal" data-bs-target="#addBudgetModal">Add Budget</button>
            </div>
        </div>

        <!-- BUDGET SPECIFIC CONTENT -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Total Budget</div>
                    <div class="kpi-value" id="total-budget-value">₱24,800,000</div>
                    <div class="kpi-sub" id="total-budget-sub">FY <?= $currentYear ?> enacted</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Disbursed</div>
                    <div class="kpi-value" id="disbursed-value">₱0</div>
                    <div class="kpi-sub" id="disbursed-sub">0.0% utilization</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Remaining</div>
                    <div class="kpi-value text-warning" id="remaining-value">₱0</div>
                    <div class="kpi-sub">Unspent allocation</div>
                </div></div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-7">
                <div class="card h-100">
                    <div class="card-header">Allocated vs. Disbursed</div>
                    <div class="card-body">
                        <div style="height: 300px;"><canvas id="budgetHorizontalBarChart"></canvas></div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card h-100">
                    <div class="card-header">Sector Breakdown</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-success small"></i> Infrastructure</span>
                            <span class="text-muted">₱7.8M (91% disb.)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-danger small"></i> Healthcare</span>
                            <span class="text-muted">₱5.5M (89% disb.)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-warning small"></i> Education</span>
                            <span class="text-muted">₱4.4M (62% disb.)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill text-primary small"></i> Social Services</span>
                            <span class="text-muted">₱3.8M (76% disb.)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ADD BUDGET MODAL -->
<div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="add-budget-form">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addBudgetModalLabel">Add Budget</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add-budget-year" class="form-label">Year</label>
                        <input type="text" class="form-control" id="add-budget-year" value="<?= $currentYear ?>" readonly>
                        <div class="form-text">Budget can only be added for the current year.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Budget</label>
                        <div class="form-control-plaintext fw-semibold" id="add-budget-current">₱0</div>
                    </div>
                    <div class="mb-3">
                        <label for="add-budget-amount" class="form-label">Amount to Add</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="add-budget-amount" min="1" step="1" placeholder="0" required>
                        </div>
                        <div class="form-text">Added on top of the current budget. Budget cannot be reduced.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-green btn-sm px-3">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/charts.js"></script>
<script src="js/main.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const currentYear = <?= $currentYear ?>;
    const budgets = { [currentYear]: 24800000 };
    const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 });

    const sectorDisbursed = [7.1, 4.9, 2.7, 2.9, 1.8];
    const disbursed = Math.round(sectorDisbursed.reduce((sum, value) => sum + value, 0) * 1000000);

    const yearFilter = document.getElementById('budget-year-filter');
    const totalValue = document.getElementById('total-budget-value');
    const totalSub = document.getElementById('total-budget-sub');
    const disbursedValue = document.getElementById('disbursed-value');
    const disbursedSub = document.getElementById('disbursed-sub');
    const remainingValue = document.getElementById('remaining-value');

    const modal = document.getElementById('addBudgetModal');
    const form = document.getElementById('add-budget-form');
    const yearInput = document.getElementById('add-budget-year');
    const currentDisplay = document.getElementById('add-budget-current');
    const amountInput = document.getElementById('add-budget-amount');

    const render = (year) => {
        const total = budgets[year] || 0;
        const utilization = total > 0 ? (disbursed / total) * 100 : 0;
        const remaining = Math.max(total - disbursed, 0);

        totalValue.textContent = peso.format(total);
        totalSub.textContent = `FY ${year} enacted`;
        disbursedValue.textContent = peso.format(disbursed);
        disbursedSub.textContent = `${utilization.toFixed(1)}% utilization`;
        remainingValue.textContent = peso.format(remaining);
    };

    render(currentYear);
    yearFilter.addEventListener('change', () => render(Number(yearFilter.value)));

    modal.addEventListener('show.bs.modal', () => {
        yearInput.value = currentYear;
        currentDisplay.textContent = peso.format(budgets[currentYear] || 0);
        amountInput.value = '';
        amountInput.focus();
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const amount = Number(amountInput.value);
        if (!Number.isFinite(amount) || amount <= 0) {
            amountInput.setCustomValidity('Enter an amount greater than zero.');
            amountInput.reportValidity();
            return;
        }
        amountInput.setCustomValidity('');
        budgets[currentYear] = (budgets[currentYear] || 0) + amount;
        yearFilter.value = currentYear;
        render(currentYear);
        bootstrap.Modal.getInstance(modal).hide();
    });

    amountInput.addEventListener('input', () => amountInput.setCustomValidity(''));
});
</script>
</body>
</html>