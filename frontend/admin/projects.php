<?php
declare(strict_types=1);
require_once dirname(__DIR__, 2) . '/backend/controllers/admin/guard.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - Brgy. Bucana</title>
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
        <div class="row g-3 mb-4" id="project-grid">
            <div class="col-md-4 project-item" data-status="ongoing" data-sector="Infrastructure" data-budget="1250000" data-start="Jun 2026" data-target="Oct 2026" data-search="drainage rehabilitation infrastructure purok 2">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0">Drainage Rehabilitation</h5>
                            <span class="status-pill status-pending">Ongoing</span>
                        </div>
                        <div class="small text-muted mb-2"><i class="bi bi-tag"></i> Infrastructure</div>
                        <p class="text-muted small mb-3">Concrete drainage upgrade along Purok 2 main road to reduce flooding during the rainy season.</p>
                        <div class="d-flex justify-content-between small mb-1"><span>Budget</span><strong>₱1,250,000</strong></div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 65%"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span class="project-timeline">Jun 2026 - Oct 2026</span>
                            <span>65%</span>
                        </div>
                        <button type="button" class="btn btn-primary-green btn-sm mt-3 align-self-start project-status-btn">Complete</button>
                    </div>
                </div>
            </div>
            <div class="col-md-4 project-item" data-status="completed" data-sector="Health" data-budget="820000" data-start="Jan 2026" data-target="Mar 2026" data-search="health center medical equipment healthcare">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0">Health Center Equipment</h5>
                            <span class="status-pill status-completed">Completed</span>
                        </div>
                        <div class="small text-muted mb-2"><i class="bi bi-tag"></i> Health</div>
                        <p class="text-muted small mb-3">Procurement of basic medical equipment and supplies for the barangay health center.</p>
                        <div class="d-flex justify-content-between small mb-1"><span>Budget</span><strong>₱820,000</strong></div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span class="project-timeline">Jan 2026 - Mar 2026</span>
                            <span>100%</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 project-item" data-status="planned" data-sector="Infrastructure" data-budget="2100000" data-start="Nov 2026" data-target="Feb 2027" data-search="street lighting solar infrastructure purok 5">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="fw-bold mb-0">Solar Street Lighting</h5>
                            <span class="status-pill status-released">Planned</span>
                        </div>
                        <div class="small text-muted mb-2"><i class="bi bi-tag"></i> Infrastructure</div>
                        <p class="text-muted small mb-3">Installation of solar-powered street lights along Purok 5 and the barangay hall access road.</p>
                        <div class="d-flex justify-content-between small mb-1"><span>Budget</span><strong>₱2,100,000</strong></div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 10%"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span class="project-timeline">Nov 2026 - Feb 2027</span>
                            <span>10%</span>
                        </div>
                        <button type="button" class="btn btn-primary-green btn-sm mt-3 align-self-start project-status-btn">Start Project</button>
                    </div>
                </div>
            </div>
        </div>

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
                            <input type="text" class="form-control" id="report-province" placeholder="Province" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-municipality" class="form-label">Municipality</label>
                            <input type="text" class="form-control" id="report-municipality" placeholder="Municipality" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-barangay" class="form-label">Barangay</label>
                            <input type="text" class="form-control" id="report-barangay" value="Bucana" required>
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
                            <input type="text" class="form-control" id="report-start" placeholder="MM/DD/YYYY" required>
                        </div>
                        <div class="col-md-4">
                            <label for="report-target" class="form-label">Target Completion Date</label>
                            <input type="text" class="form-control" id="report-target" placeholder="MM/DD/YYYY" required>
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
document.addEventListener('DOMContentLoaded', () => {
    const peso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 0 });
    const grid = document.getElementById('project-grid');
    const search = document.getElementById('project-search');
    const empty = document.getElementById('project-empty');

    const modal = document.getElementById('createProjectModal');
    const form = document.getElementById('create-project-form');
    const nameInput = document.getElementById('project-name');
    const sectorInput = document.getElementById('project-sector');
    const descInput = document.getElementById('project-description');
    const budgetInput = document.getElementById('project-budget');

    const reportModalEl = document.getElementById('projectReportModal');
    const reportForm = document.getElementById('project-report-form');
    const reportTitleInput = document.getElementById('report-title');
    const reportBudgetInput = document.getElementById('report-total-budget');
    const reportStartInput = document.getElementById('report-start');
    const reportTargetInput = document.getElementById('report-target');
    const reportProvinceInput = document.getElementById('report-province');
    const reportMunicipalityInput = document.getElementById('report-municipality');
    const reportBarangayInput = document.getElementById('report-barangay');
    let pendingProject = null;

    const statusMap = {
        planned: { label: 'Planned', className: 'status-released' },
        ongoing: { label: 'Ongoing', className: 'status-pending' },
        completed: { label: 'Completed', className: 'status-completed' }
    };

    const setProjectStatus = (item, statusKey) => {
        const status = statusMap[statusKey];
        item.dataset.status = statusKey;

        const pill = item.querySelector('.status-pill');
        pill.textContent = status.label;
        pill.className = `status-pill ${status.className}`;

        const button = item.querySelector('.project-status-btn');
        if (!button) {
            return;
        }
        if (statusKey === 'planned') {
            button.textContent = 'Start Project';
        } else if (statusKey === 'ongoing') {
            button.textContent = 'Complete';
        } else {
            button.remove();
        }
    };

    const syncReportHeader = () => {
        document.getElementById('report-province-label').textContent = reportProvinceInput.value.trim() || '[PROVINCE]';
        document.getElementById('report-municipality-label').textContent = reportMunicipalityInput.value.trim() || '[MUNICIPALITY]';
        document.getElementById('report-barangay-label').textContent = reportBarangayInput.value.trim() || '[BARANGAY NAME]';
    };

    const openProjectReport = (item) => {
        reportForm.reset();
        reportTitleInput.value = item.querySelector('h5').textContent.trim();
        reportBudgetInput.value = item.dataset.budget || '';
        reportStartInput.value = item.dataset.start || '';
        reportTargetInput.value = item.dataset.target || '';
        syncReportHeader();
        bootstrap.Modal.getOrCreateInstance(reportModalEl).show();
    };

    reportProvinceInput.addEventListener('input', syncReportHeader);
    reportMunicipalityInput.addEventListener('input', syncReportHeader);
    reportBarangayInput.addEventListener('input', syncReportHeader);

    reportModalEl.addEventListener('hidden.bs.modal', () => {
        pendingProject = null;
    });

    reportForm.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!pendingProject) {
            return;
        }
        const item = pendingProject;
        const start = reportStartInput.value.trim();
        const target = reportTargetInput.value.trim();

        item.dataset.start = start;
        item.dataset.target = target;
        const timeline = item.querySelector('.project-timeline');
        if (timeline) {
            timeline.textContent = start && target ? `${start} - ${target}` : (start || target || 'Not set');
        }

        setProjectStatus(item, 'ongoing');
        pendingProject = null;
        filterProjects();
        bootstrap.Modal.getOrCreateInstance(reportModalEl).hide();
    });

    grid.addEventListener('click', (event) => {
        const button = event.target.closest('.project-status-btn');
        if (!button) {
            return;
        }
        const item = button.closest('.project-item');
        if (item.dataset.status === 'planned') {
            if ((item.dataset.sector || '').toLowerCase() === 'infrastructure') {
                pendingProject = item;
                openProjectReport(item);
            } else {
                setProjectStatus(item, 'ongoing');
            }
        } else if (item.dataset.status === 'ongoing') {
            setProjectStatus(item, 'completed');
        }
        filterProjects();
    });

    const escapeHtml = (value) => value.replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    }[char]));

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

    search.addEventListener('input', filterProjects);

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const name = nameInput.value.trim();
        const sector = sectorInput.value;
        const description = descInput.value.trim();
        const budget = Number(budgetInput.value) || 0;
        const status = statusMap.planned;

        const column = document.createElement('div');
        column.className = 'col-md-4 project-item';
        column.dataset.status = 'planned';
        column.dataset.sector = sector;
        column.dataset.budget = String(budget);
        column.dataset.start = '';
        column.dataset.target = '';
        column.dataset.search = `${name} ${sector} ${description}`.toLowerCase();
        column.innerHTML = `
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="fw-bold mb-0">${escapeHtml(name)}</h5>
                        <span class="status-pill ${status.className}">${status.label}</span>
                    </div>
                    <div class="small text-muted mb-2"><i class="bi bi-tag"></i> ${escapeHtml(sector)}</div>
                    <p class="text-muted small mb-3">${escapeHtml(description) || 'No description provided.'}</p>
                    <div class="d-flex justify-content-between small mb-1"><span>Budget</span><strong>${peso.format(budget)}</strong></div>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 0%"></div>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span class="project-timeline">Not set</span>
                        <span>0%</span>
                    </div>
                    <button type="button" class="btn btn-primary-green btn-sm mt-3 align-self-start project-status-btn">Start Project</button>
                </div>
            </div>`;

        grid.prepend(column);
        form.reset();
        bootstrap.Modal.getInstance(modal).hide();
        filterProjects();
    });
});
</script>
</body>
</html>
