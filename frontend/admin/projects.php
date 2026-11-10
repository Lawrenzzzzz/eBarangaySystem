<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/guard.php';
$projects = list_projects();
$currentYear = (int) date('Y');
$budgetSummary = get_budget($currentYear);
$sectorBudgetSources = [
    'Infrastructure' => 'Infrastructure',
    'Education' => 'Education',
    'Health' => 'Healthcare',
    'Social Services' => 'Social Services',
    'Disaster & P&O' => 'Disaster and P&O',
];
$sectorAllocations = [];
foreach ($sectorBudgetSources as $projectSector => $budgetSector) {
    $sectorAllocations[$projectSector] = [
        'allocated' => (float) ($budgetSummary['allocations'][$budgetSector] ?? 0),
        'disbursed' => (float) ($budgetSummary['disbursed_allocations'][$budgetSector] ?? 0),
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title>Projects - <?= $esc($settingsBarangayName) ?></title>
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
            <li class="nav-item"><a class="nav-link" href="budget.php"><i class="bi bi-wallet2"></i> Budget</a></li>
            <!-- ACTIVE CLASS MOVED HERE -->
            <li class="nav-item"><a class="nav-link active" href="projects.php"><i class="bi bi-kanban"></i> Projects</a></li>
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
                <h3 class="fw-bold mb-1">Projects</h3>
                <p class="text-muted mb-0">Barangay infrastructure and community projects</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="input-group input-group-sm" style="width: 240px;">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="search" class="form-control" id="project-search" placeholder="Search projects..." aria-label="Search projects">
                </div>
                <button class="btn btn-primary-green btn-sm px-3" type="button" data-bs-toggle="modal" data-bs-target="#createProjectModal">Create Project</button>
            </div>
        </div>

        <!-- PROJECTS SPECIFIC CONTENT -->
        <div class="row g-3 mb-4" id="project-grid"></div>

        <p class="text-muted small d-none" id="project-empty">No projects match your search.</p>
    </div>
</div>

<!-- CREATE PROJECT MODAL -->
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-labelledby="createProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="create-project-form">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="createProjectModalLabel">Create Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="project-name" class="form-label">Project Name</label>
                        <input type="text" class="form-control" id="project-name" required>
                    </div>
                    <div class="mb-3">
                        <label for="project-sector" class="form-label">Sector</label>
                        <select class="form-select" id="project-sector" required>
                            <option value="" selected disabled>Select a sector</option>
                            <option value="Infrastructure">Infrastructure</option>
                            <option value="Education">Education</option>
                            <option value="Health">Health</option>
                            <option value="Social Services">Social Services</option>
                            <option value="Disaster &amp; P&amp;O">Disaster &amp; P&amp;O</option>
                        </select>
                    </div>
                    <div class="mb-3 d-none" id="project-sector-budget">
                        <div class="border rounded-3 bg-light p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted text-uppercase fw-semibold">Sector Allocation - FY <?= $currentYear ?></span>
                                <strong id="project-sector-allocated">₱0</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <span class="small text-muted">Remaining for this sector</span>
                                <span class="fs-5 fw-bold text-success" id="project-sector-remaining">₱0</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="project-description" class="form-label">Description</label>
                        <textarea class="form-control" id="project-description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="project-budget" class="form-label">Budget</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="project-budget" min="0" step="1" placeholder="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-green btn-sm px-3">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- PROJECT IMPLEMENTATION AND ACCOMPLISHMENT REPORT MODAL -->
<div class="modal fade" id="projectReportModal" tabindex="-1" aria-labelledby="projectReportModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form id="project-report-form">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="projectReportModalLabel">Project Implementation and Accomplishment Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <div class="text-center mb-4">
                        <div class="fw-semibold">REPUBLIC OF THE PHILIPPINES</div>
                        <div class="text-muted small">PROVINCE OF <span id="report-province-label">[PROVINCE]</span> · MUNICIPALITY OF <span id="report-municipality-label">[MUNICIPALITY]</span> · BARANGAY <span id="report-barangay-label">[BARANGAY NAME]</span></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="report-province" class="form-label">Province</label>
                            <input type="text" class="form-control" id="report-province" value="<?= $esc($settingsProfile['province']) ?>" placeholder="Province" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-municipality" class="form-label">Municipality</label>
                            <input type="text" class="form-control" id="report-municipality" value="<?= $esc($settingsProfile['municipality']) ?>" placeholder="Municipality" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-barangay" class="form-label">Barangay</label>
                            <input type="text" class="form-control" id="report-barangay" value="<?= $esc($settingsProfile['barangay_name']) ?>" required>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">I. Project Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="report-title" class="form-label">Project Title</label>
                            <input type="text" class="form-control" id="report-title" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="report-location" class="form-label">Project Location</label>
                            <input type="text" class="form-control" id="report-location" placeholder="Street / Purok / Sitio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="report-category" class="form-label">Project Category</label>
                            <input type="text" class="form-control" id="report-category" value="Infrastructure Development" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="report-contractor" class="form-label">Project Contractor</label>
                            <input type="text" class="form-control" id="report-contractor" placeholder="Contractor / Barangay Administration" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-start" class="form-label">Project Start Date</label>
                            <input type="date" class="form-control" id="report-start" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-target" class="form-label">Target Completion Date</label>
                            <input type="date" class="form-control" id="report-target" required>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">II. Project Description</h6>
                    <div class="mb-4">
                        <label for="report-description" class="form-label">Description</label>
                        <textarea class="form-control" id="report-description" rows="3" placeholder="Brief description of the project, its purpose, and the problem it aims to address." required></textarea>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">III. Project Objectives</h6>
                    <div class="mb-4">
                        <label for="report-objectives" class="form-label">Objectives</label>
                        <textarea class="form-control" id="report-objectives" rows="3" placeholder="Give three objectives" required></textarea>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">IV. Project Budget and Funding</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="report-total-budget" class="form-label">Total Approved Budget</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="report-total-budget" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="report-fund-source" class="form-label">Source of Funds</label>
                            <select class="form-select" id="report-fund-source" required>
                                <option value="" selected disabled>Select source of funds</option>
                                <option value="Barangay Fund">Barangay Fund</option>
                                <option value="Municipal Fund">Municipal Fund</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">V. Project Challenges and Issues</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="report-challenges" class="form-label">Challenges and Issues Encountered</label>
                            <textarea class="form-control" id="report-challenges" rows="3" placeholder="List any problems encountered during implementation."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label for="report-actions" class="form-label">Actions Taken</label>
                            <textarea class="form-control" id="report-actions" rows="3" placeholder="Describe the solutions or actions implemented."></textarea>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">VI. Recommendations</h6>
                    <div class="mb-4">
                        <label for="report-recommendations" class="form-label">Recommendations</label>
                        <textarea class="form-control" id="report-recommendations" rows="3" placeholder="Recommendations for maintaining the project and ensuring sustainability." required></textarea>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 mb-3">VII. Certification and Approval</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="report-prepared-by" class="form-label">Prepared by</label>
                            <input type="text" class="form-control" id="report-prepared-by" placeholder="Project Coordinator / Barangay Secretary" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-reviewed-by" class="form-label">Reviewed by</label>
                            <input type="text" class="form-control" id="report-reviewed-by" placeholder="Barangay Treasurer" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-approved-by" class="form-label">Approved by</label>
                            <input type="text" class="form-control" id="report-approved-by" placeholder="Punong Barangay" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-date" class="form-label">Date</label>
                            <input type="text" class="form-control" id="report-date" placeholder="MM/DD/YYYY" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-green btn-sm px-3">Submit Report &amp; Start Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
window.__initialProjects = <?= json_encode($projects, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
window.__sectorAllocations = <?= json_encode($sectorAllocations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 });
    const grid = document.getElementById('project-grid');
    const search = document.getElementById('project-search');
    const empty = document.getElementById('project-empty');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    const modal = document.getElementById('createProjectModal');
    const form = document.getElementById('create-project-form');
    const nameInput = document.getElementById('project-name');
    const sectorInput = document.getElementById('project-sector');
    const descInput = document.getElementById('project-description');
    const budgetInput = document.getElementById('project-budget');

    const reportModalEl = document.getElementById('projectReportModal');
    const reportForm = document.getElementById('project-report-form');
    const reportTitleInput = document.getElementById('report-title');
    const reportCategoryInput = document.getElementById('report-category');
    const reportBudgetInput = document.getElementById('report-total-budget');
    const reportStartInput = document.getElementById('report-start');
    const reportTargetInput = document.getElementById('report-target');
    const reportProvinceInput = document.getElementById('report-province');
    const reportMunicipalityInput = document.getElementById('report-municipality');
    const reportBarangayInput = document.getElementById('report-barangay');

    const sectorBudgetPanel = document.getElementById('project-sector-budget');
    const sectorAllocatedEl = document.getElementById('project-sector-allocated');
    const sectorRemainingEl = document.getElementById('project-sector-remaining');

    let projects = Array.isArray(window.__initialProjects) ? window.__initialProjects : [];
    let pendingProject = null;
    const sectorAllocations = window.__sectorAllocations && typeof window.__sectorAllocations === 'object' ? window.__sectorAllocations : {};

    const syncSectorAllocation = () => {
        const allocation = sectorAllocations[sectorInput.value];
        if (!allocation) {
            sectorBudgetPanel.classList.add('d-none');
            return;
        }

        const committed = projects
            .filter((project) => project.sector === sectorInput.value)
            .reduce((total, project) => total + Number(project.budget || 0), 0);
        const remaining = Number(allocation.allocated || 0) - committed;

        sectorAllocatedEl.textContent = peso.format(Number(allocation.allocated || 0));
        sectorRemainingEl.textContent = peso.format(remaining);
        sectorRemainingEl.classList.toggle('text-danger', remaining < 0);
        sectorRemainingEl.classList.toggle('text-success', remaining >= 0);
        sectorBudgetPanel.classList.remove('d-none');
    };

    sectorInput.addEventListener('change', syncSectorAllocation);

    const escapeHtml = (value) => String(value).replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));

    const fieldValue = (id) => {
        const element = document.getElementById(id);
        return element ? element.value.trim() : '';
    };

    const filterProjects = () => {
        const term = search.value.trim().toLowerCase();
        let visible = 0;
        grid.querySelectorAll('.project-item').forEach((item) => {
            const haystack = `${item.dataset.search || ''} ${item.textContent}`.toLowerCase();
            const match = term === '' || haystack.includes(term);
            item.classList.toggle('d-none', !match);
            if (match) {
                visible++;
            }
        });
        empty.classList.toggle('d-none', visible !== 0);
    };

    const buildCard = (project) => {
        const column = document.createElement('div');
        column.className = 'col-md-4 project-item';
        column.dataset.id = String(project.id);
        column.dataset.status = project.status;
        column.dataset.sector = project.sector;
        column.dataset.budget = String(project.budget);
        column.dataset.search = `${project.title} ${project.sector} ${project.description}`.toLowerCase();

        const actionButton = project.action_label
            ? `<button type="button" class="btn btn-primary-green btn-sm mt-3 align-self-start project-status-btn">${escapeHtml(project.action_label)}</button>`
            : '';

        column.innerHTML = `
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0">${escapeHtml(project.title)}</h5>
                        <span class="status-pill ${project.status_class}">${escapeHtml(project.status_label)}</span>
                    </div>
                    <div class="small text-muted mb-2"><i class="bi bi-tag"></i> ${escapeHtml(project.sector)}</div>
                    <p class="text-muted small mb-3">${escapeHtml(project.description) || 'No description provided.'}</p>
                    <div class="d-flex justify-content-between small mb-1"><span>Budget</span><strong>${peso.format(project.budget)}</strong></div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: ${project.progress}%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span class="project-timeline">${escapeHtml(project.timeline)}</span>
                        <span>${project.progress}%</span>
                    </div>
                    ${actionButton}
                </div>
            </div>`;

        return column;
    };

    const renderGrid = () => {
        grid.innerHTML = '';
        projects.forEach((project) => grid.appendChild(buildCard(project)));
        filterProjects();
        syncSectorAllocation();
    };

    const findProject = (id) => projects.find((project) => String(project.id) === String(id)) || null;

    const replaceProject = (updated) => {
        projects = projects.map((project) => (String(project.id) === String(updated.id) ? updated : project));
    };

    const postProject = async (payload) => {
        const response = await fetch('../../backend/api/projects.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-Token': csrfToken,
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
        });
        const result = await response.json();
        if (!response.ok || !result.ok) {
            throw new Error(result.error || 'Unable to save the project.');
        }
        return result.project;
    };

    const syncReportHeader = () => {
        document.getElementById('report-province-label').textContent = reportProvinceInput.value.trim() || '[PROVINCE]';
        document.getElementById('report-municipality-label').textContent = reportMunicipalityInput.value.trim() || '[MUNICIPALITY]';
        document.getElementById('report-barangay-label').textContent = reportBarangayInput.value.trim() || '[BARANGAY NAME]';
    };

    const toDateInputValue = (value) => {
        const text = String(value || '').trim();
        const slash = /^(\d{1,2})\/(\d{1,2})\/(\d{4})$/.exec(text);
        if (slash) {
            return `${slash[3]}-${slash[1].padStart(2, '0')}-${slash[2].padStart(2, '0')}`;
        }
        return /^\d{4}-\d{2}-\d{2}$/.test(text) ? text : '';
    };

    const fromDateInputValue = (value) => {
        const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value || '').trim());
        return match ? `${match[2]}/${match[3]}/${match[1]}` : '';
    };

    const reportCategoryBySector = {
        'Infrastructure': 'Infrastructure Development',
        'Education': 'Education',
        'Health': 'Health',
        'Social Services': 'Social Services',
        'Disaster & P&O': 'Disaster Preparedness and Order',
    };

    const openProjectReport = (project) => {
        reportForm.reset();
        reportTitleInput.value = project.title;
        reportCategoryInput.value = reportCategoryBySector[project.sector] || project.sector;
        reportBudgetInput.value = project.budget || '';
        reportStartInput.value = toDateInputValue(project.start_date);
        reportTargetInput.value = toDateInputValue(project.target_date);
        reportTargetInput.min = reportStartInput.value;
        syncReportHeader();
        bootstrap.Modal.getOrCreateInstance(reportModalEl).show();
    };

    reportProvinceInput.addEventListener('input', syncReportHeader);
    reportMunicipalityInput.addEventListener('input', syncReportHeader);
    reportBarangayInput.addEventListener('input', syncReportHeader);

    reportStartInput.addEventListener('input', () => {
        reportStartInput.setCustomValidity('');
        reportTargetInput.min = reportStartInput.value;
        if (reportTargetInput.value !== '' && reportTargetInput.value < reportStartInput.value) {
            reportTargetInput.setCustomValidity('Target completion date cannot be earlier than the project start date.');
        } else {
            reportTargetInput.setCustomValidity('');
        }
    });

    reportTargetInput.addEventListener('input', () => {
        reportTargetInput.setCustomValidity('');
    });

    reportModalEl.addEventListener('hidden.bs.modal', () => {
        pendingProject = null;
    });

    reportForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!pendingProject) {
            return;
        }

        if (!reportForm.reportValidity()) {
            return;
        }

        const submitButton = reportForm.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        try {
            const updated = await postProject({
                id: pendingProject.id,
                status: 'ongoing',
                start_date: fromDateInputValue(reportStartInput.value),
                target_date: fromDateInputValue(reportTargetInput.value),
                report: {
                    province: fieldValue('report-province'),
                    municipality: fieldValue('report-municipality'),
                    barangay: fieldValue('report-barangay'),
                    location: fieldValue('report-location'),
                    category: fieldValue('report-category'),
                    contractor: fieldValue('report-contractor'),
                    description: fieldValue('report-description'),
                    objectives: fieldValue('report-objectives'),
                    total_budget: fieldValue('report-total-budget'),
                    fund_source: fieldValue('report-fund-source'),
                    challenges: fieldValue('report-challenges'),
                    actions: fieldValue('report-actions'),
                    recommendations: fieldValue('report-recommendations'),
                    prepared_by: fieldValue('report-prepared-by'),
                    reviewed_by: fieldValue('report-reviewed-by'),
                    approved_by: fieldValue('report-approved-by'),
                    date: fieldValue('report-date'),
                },
            });
            replaceProject(updated);
            pendingProject = null;
            bootstrap.Modal.getOrCreateInstance(reportModalEl).hide();
            renderGrid();
        } catch (error) {
            window.alert(error.message);
        } finally {
            submitButton.disabled = false;
        }
    });

    grid.addEventListener('click', async (event) => {
        const button = event.target.closest('.project-status-btn');
        if (!button) {
            return;
        }

        const item = button.closest('.project-item');
        const project = findProject(item.dataset.id);
        if (!project) {
            return;
        }

        if (project.status === 'planned') {
            pendingProject = project;
            openProjectReport(project);
            return;
        }

        const nextStatus = project.status === 'planned' ? 'ongoing' : 'completed';
        button.disabled = true;
        try {
            const updated = await postProject({ id: project.id, status: nextStatus });
            replaceProject(updated);
            renderGrid();
        } catch (error) {
            window.alert(error.message);
            button.disabled = false;
        }
    });

    search.addEventListener('input', filterProjects);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        try {
            const project = await postProject({
                title: nameInput.value.trim(),
                sector: sectorInput.value,
                description: descInput.value.trim(),
                budget: Number(budgetInput.value) || 0,
            });
            projects.unshift(project);
            form.reset();
            bootstrap.Modal.getInstance(modal).hide();
            renderGrid();
        } catch (error) {
            window.alert(error.message);
        } finally {
            submitButton.disabled = false;
        }
    });

    renderGrid();
});
</script>
</body>
</html>
