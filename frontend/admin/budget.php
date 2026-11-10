<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/guard.php';
$currentYear = (int) date('Y');
$initialBudget = get_budget($currentYear);
$initialDisbursed = (float) $initialBudget['disbursed_total'];
$initialRemaining = max((float) $initialBudget['total_budget'] - $initialDisbursed, 0);
$initialUtilization = (float) $initialBudget['total_budget'] > 0
    ? ($initialDisbursed / (float) $initialBudget['total_budget']) * 100
    : 0;
$sectorColors = [
    'Infrastructure' => '#198754',
    'Healthcare' => '#dc3545',
    'Education' => '#ffc107',
    'Social Services' => '#0d6efd',
    'Disaster and P&O' => '#6c757d',
];
$formatMoney = static fn (float $amount): string => '₱' . number_format($amount, 0);
$formatShort = static function (float $amount): string {
    if ($amount >= 1000000) {
        return '₱' . rtrim(rtrim(number_format($amount / 1000000, 1), '0'), '.') . 'M';
    }

    return '₱' . number_format($amount, 0);
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Budget - <?= $esc($settingsBarangayName) ?></title>
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
            <div><h6 class="mb-0 fw-bold"><?= $esc($settingsBarangayName) ?></h6><small class="text-muted">Office of the Chairman</small></div>
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
                <strong class="d-block"><?= $esc($settingsChairmanName) ?></strong>
                <small class="text-muted">Chairman - Term <?= $esc($settingsTerm) ?></small>
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
                <button class="btn btn-outline-secondary btn-sm px-3" type="button" data-bs-toggle="modal" data-bs-target="#divideBudgetModal">Divide Budget</button>
                <button class="btn btn-primary-green btn-sm px-3" type="button" data-bs-toggle="modal" data-bs-target="#addBudgetModal">Add Budget</button>
            </div>
        </div>

        <!-- BUDGET SPECIFIC CONTENT -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Total Budget</div>
                    <div class="kpi-value" id="total-budget-value"><?= $formatMoney((float) $initialBudget['total_budget']) ?></div>
                    <div class="kpi-sub" id="total-budget-sub">FY <?= $currentYear ?> enacted</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Disbursed</div>
                    <div class="kpi-value" id="disbursed-value"><?= $formatMoney($initialDisbursed) ?></div>
                    <div class="kpi-sub" id="disbursed-sub"><?= number_format($initialUtilization, 1) ?>% utilization</div>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card h-100"><div class="card-body">
                    <div class="kpi-title">Remaining</div>
                    <div class="kpi-value text-warning" id="remaining-value"><?= $formatMoney($initialRemaining) ?></div>
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
                        <?php foreach (budget_sectors() as $sector):
                            $sectorAllocated = (float) ($initialBudget['allocations'][$sector] ?? 0);
                            $sectorDisbursed = (float) ($initialBudget['disbursed_allocations'][$sector] ?? 0);
                            $sectorRate = $sectorAllocated > 0 ? ($sectorDisbursed / $sectorAllocated) * 100 : 0;
                        ?>
                        <div class="d-flex justify-content-between mb-3">
                            <span><i class="bi bi-circle-fill small" style="color: <?= $sectorColors[$sector] ?? '#6c757d' ?>"></i> <?= htmlspecialchars($sector) ?></span>
                            <span class="text-muted" data-sector-breakdown="<?= htmlspecialchars($sector) ?>"><?= $formatShort($sectorAllocated) ?> (<?= number_format($sectorRate, 0) ?>% disb.)</span>
                        </div>
                        <?php endforeach; ?>
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

<!-- DIVIDE BUDGET MODAL -->
<div class="modal fade" id="divideBudgetModal" tabindex="-1" aria-labelledby="divideBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="divideBudgetModalLabel">Divide Budget</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning py-2 px-3 small d-none" id="divide-lock-notice">
                    <i class="bi bi-lock-fill me-1"></i>This year's allocation has already been saved and is locked. It can no longer be edited.
                </div>
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="kpi-title mb-0">Total Budget</div>
                        <div class="fs-4 fw-bold" id="divide-total-budget">₱0</div>
                    </div>
                    <div class="text-end">
                        <div class="kpi-title mb-0">Remaining</div>
                        <div class="fs-4 fw-bold text-warning" id="divide-remaining-budget">₱0</div>
                    </div>
                </div>
                <div class="d-flex justify-content-between small text-muted mb-3">
                    <span>Allocated: <strong id="divide-allocated-budget">₱0</strong></span>
                    <span id="divide-status"></span>
                </div>
                <hr>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label mb-1">Infrastructure</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control sector-allocation" data-sector="Infrastructure" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Healthcare</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control sector-allocation" data-sector="Healthcare" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Education</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control sector-allocation" data-sector="Education" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Social Services</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control sector-allocation" data-sector="Social Services" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label mb-1">Disaster and P&amp;O</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control sector-allocation" data-sector="Disaster and P&amp;O" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary-green btn-sm px-3" id="divide-save-budget">Save Allocation</button>
            </div>
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
    const budgets = { [currentYear]: <?= (float) $initialBudget['total_budget'] ?> };
    let savedAllocations = <?= json_encode($initialBudget['allocations'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    let savedDisbursements = <?= json_encode($initialBudget['disbursed_allocations'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    let allocationLocked = <?= !empty($initialBudget['allocation_locked']) ? 'true' : 'false' ?>;
    const disbursed = <?= (float) $initialBudget['disbursed_total'] ?>;
    const sectorColorMap = <?= json_encode($sectorColors, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 });
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

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

    const formatShort = (amount) => {
        if (amount >= 1000000) {
            return `₱${(amount / 1000000).toFixed(1).replace(/\.0$/, '')}M`;
        }
        return `₱${Math.round(amount).toLocaleString('en-PH')}`;
    };

    const renderSectorBreakdown = () => {
        document.querySelectorAll('[data-sector-breakdown]').forEach((element) => {
            const sector = element.dataset.sectorBreakdown;
            const allocated = Number(savedAllocations[sector]) || 0;
            const spent = Number(savedDisbursements[sector]) || 0;
            const rate = allocated > 0 ? (spent / allocated) * 100 : 0;
            element.textContent = `${formatShort(allocated)} (${rate.toFixed(0)}% disb.)`;
        });
    };

    const chartCanvas = document.getElementById('budgetHorizontalBarChart');
    let budgetChart = null;

    const renderChart = () => {
        if (!chartCanvas) {
            return;
        }

        const labels = Object.keys(savedAllocations);
        const allocated = labels.map((sector) => (Number(savedAllocations[sector]) || 0) / 1000000);
        const spent = labels.map((sector) => (Number(savedDisbursements[sector]) || 0) / 1000000);
        const sectorColors = labels.map((label, index) => sectorColorMap[label] || dashboardPieColors[index % dashboardPieColors.length]);
        const allocatedColors = sectorColors;
        const disbursedColors = sectorColors.map((color) => dashboardColorWithAlpha(color, 0.45));

        if (budgetChart) {
            budgetChart.data.labels = labels;
            budgetChart.data.datasets[0].data = allocated;
            budgetChart.data.datasets[0].backgroundColor = allocatedColors;
            budgetChart.data.datasets[1].data = spent;
            budgetChart.data.datasets[1].backgroundColor = disbursedColors;
            budgetChart.update();
            return;
        }

        budgetChart = new Chart(chartCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Allocated', data: allocated, backgroundColor: allocatedColors, borderRadius: 4 },
                    { label: 'Disbursed', data: spent, backgroundColor: disbursedColors, borderRadius: 4 },
                ],
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { generateLabels: (chart) => chart.data.datasets.map((dataset, index) => ({ text: dataset.label, fillStyle: index === 0 ? dashboardPieColors[0] : dashboardColorWithAlpha(dashboardPieColors[0], 0.45), strokeStyle: 'transparent', lineWidth: 0, hidden: !chart.isDatasetVisible(index), datasetIndex: index })) } } }, scales: { x: { beginAtZero: true, grid: { display: false } }, y: { grid: { display: false } } } },
        });
    };

    render(currentYear);
    renderSectorBreakdown();
    renderChart();
    yearFilter.addEventListener('change', () => render(Number(yearFilter.value)));

    modal.addEventListener('show.bs.modal', () => {
        yearInput.value = currentYear;
        currentDisplay.textContent = peso.format(budgets[currentYear] || 0);
        amountInput.value = '';
        amountInput.focus();
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const amount = Number(amountInput.value);
        if (!Number.isFinite(amount) || amount <= 0) {
            amountInput.setCustomValidity('Enter an amount greater than zero.');
            amountInput.reportValidity();
            return;
        }
        amountInput.setCustomValidity('');

        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        try {
            const response = await fetch('../../backend/api/budget.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-Token': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ year: currentYear, amount }),
            });
            const result = await response.json();
            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'Unable to add the budget.');
            }

            budgets[currentYear] = Number(result.budget.total_budget) || 0;
            yearFilter.value = currentYear;
            render(currentYear);
            bootstrap.Modal.getInstance(modal).hide();
        } catch (error) {
            amountInput.setCustomValidity(error.message);
            amountInput.reportValidity();
        } finally {
            submitButton.disabled = false;
        }
    });

    amountInput.addEventListener('input', () => amountInput.setCustomValidity(''));

    const divideModal = document.getElementById('divideBudgetModal');
    const divideTotalValue = document.getElementById('divide-total-budget');
    const divideRemainingValue = document.getElementById('divide-remaining-budget');
    const divideAllocatedValue = document.getElementById('divide-allocated-budget');
    const divideStatus = document.getElementById('divide-status');
    const divideLockNotice = document.getElementById('divide-lock-notice');
    const divideSaveButton = document.getElementById('divide-save-budget');
    const allocationInputs = Array.from(divideModal.querySelectorAll('.sector-allocation'));

    let divideTotal = 0;
    let divideTimer = null;
    let divideRequest = 0;
    let divideOverAllocated = false;

    const collectAllocations = () => {
        const allocations = {};
        allocationInputs.forEach((input) => {
            allocations[input.dataset.sector] = Number(input.value) || 0;
        });
        return allocations;
    };

    const applyDivisionTotals = (allocated, remaining, overAllocated) => {
        divideAllocatedValue.textContent = peso.format(allocated);
        divideRemainingValue.textContent = peso.format(remaining);
        divideOverAllocated = overAllocated;
        divideRemainingValue.className = `fs-4 fw-bold ${overAllocated ? 'text-danger' : 'text-warning'}`;
        divideStatus.textContent = overAllocated ? 'Amount exceeds the total budget.' : '';
        divideStatus.className = overAllocated ? 'text-danger' : 'text-muted';
        divideSaveButton.disabled = overAllocated || allocationLocked;
    };

    const applyAllocationLock = () => {
        allocationInputs.forEach((input) => {
            input.disabled = allocationLocked;
        });
        divideLockNotice.classList.toggle('d-none', !allocationLocked);
        divideSaveButton.disabled = allocationLocked || divideOverAllocated;
    };

    const previewDivision = () => {
        const allocated = Object.values(collectAllocations()).reduce((sum, amount) => sum + amount, 0);
        applyDivisionTotals(allocated, divideTotal - allocated, allocated > divideTotal);
    };

    const calculateDivision = async () => {
        const requestId = ++divideRequest;
        divideStatus.textContent = 'Calculating...';
        divideStatus.className = 'text-muted';

        try {
            const response = await fetch('../../backend/api/budget-division.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-Token': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    year: currentYear,
                    allocations: collectAllocations(),
                }),
            });
            const result = await response.json();
            if (requestId !== divideRequest) {
                return;
            }
            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'Unable to compute the allocation.');
            }

            divideTotal = Number(result.total_budget) || 0;
            divideTotalValue.textContent = peso.format(divideTotal);
            allocationLocked = Boolean(result.allocation_locked);
            applyDivisionTotals(result.allocated, result.remaining, result.over_allocated);
            applyAllocationLock();
        } catch (error) {
            if (requestId !== divideRequest) {
                return;
            }
            divideStatus.textContent = error.message;
            divideStatus.className = 'text-danger';
            divideSaveButton.disabled = true;
        }
    };

    const scheduleDivision = () => {
        previewDivision();
        clearTimeout(divideTimer);
        divideTimer = setTimeout(calculateDivision, 250);
    };

    allocationInputs.forEach((input) => input.addEventListener('input', scheduleDivision));

    divideModal.addEventListener('show.bs.modal', () => {
        divideTotal = budgets[currentYear] || 0;
        divideTotalValue.textContent = peso.format(divideTotal);
        divideRemainingValue.textContent = peso.format(divideTotal);
        divideAllocatedValue.textContent = peso.format(0);
        divideRemainingValue.className = 'fs-4 fw-bold text-warning';
        divideStatus.textContent = '';
        divideStatus.className = 'text-muted';
        divideOverAllocated = false;
        allocationInputs.forEach((input) => {
            const amount = Number(savedAllocations[input.dataset.sector]) || 0;
            input.value = amount > 0 ? String(amount) : '';
        });
        applyAllocationLock();
        previewDivision();
        if (allocationLocked) {
            divideStatus.textContent = 'Saved allocation is locked.';
            divideStatus.className = 'text-muted';
            return;
        }
        calculateDivision();
    });

    divideSaveButton.addEventListener('click', async () => {
        if (allocationLocked || divideOverAllocated) {
            return;
        }

        divideSaveButton.disabled = true;
        divideStatus.textContent = 'Saving...';
        divideStatus.className = 'text-muted';

        try {
            const response = await fetch('../../backend/api/budget-division.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-Token': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    year: currentYear,
                    allocations: collectAllocations(),
                    save: true,
                }),
            });
            const result = await response.json();
            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'Unable to save the allocation.');
            }

            savedAllocations = result.allocations || collectAllocations();
            divideTotal = Number(result.total_budget) || divideTotal;
            allocationLocked = Boolean(result.allocation_locked);
            budgets[currentYear] = divideTotal;
            yearFilter.value = currentYear;
            divideTotalValue.textContent = peso.format(divideTotal);
            applyDivisionTotals(result.allocated, result.remaining, result.over_allocated);
            applyAllocationLock();
            render(currentYear);
            renderSectorBreakdown();
            renderChart();

            if (!result.over_allocated) {
                bootstrap.Modal.getInstance(divideModal).hide();
            }
        } catch (error) {
            divideStatus.textContent = error.message;
            divideStatus.className = 'text-danger';
            applyAllocationLock();
        }
    });
});
</script>
</body>
</html>