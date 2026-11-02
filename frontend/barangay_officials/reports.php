<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - E-Barangay Official</title>
  
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
      
      <a class="nav-link" href="settings.php">
        <span class="nav-dot"></span><span>Settings</span>
      </a>
    </div>
    
    <div class="user-profile">
      <img src="img/captain.jpg" alt="Captain" class="avatar">
      <div>
        <div class="name">Capt. Choi Seungcheol Batumbakal</div>
        <div class="role">Barangay Captain of Bucana</div>
      </div>
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
      
      <div class="d-flex align-items-center gap-3">
        <!-- YEAR PICKER -->
        <ul class="nav nav-pills year-picker" id="yearTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="year2021-tab" data-bs-toggle="pill" data-bs-target="#year2021" type="button" role="tab">2021</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="year2022-tab" data-bs-toggle="pill" data-bs-target="#year2022" type="button" role="tab">2022</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="year2023-tab" data-bs-toggle="pill" data-bs-target="#year2023" type="button" role="tab">2023</button>
          </li>
        </ul>
      </div>
    </div>

    <div class="eb-content">
      <div class="tab-content" id="yearTabsContent">
        
        <!-- ================= 2021 CONTENT ================= -->
        <div class="tab-pane fade show active" id="year2021" role="tabpanel">
          <div class="row g-3">
            <div class="col-md-6 col-lg-4">
              <div class="report-card">
                <div class="report-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="report-title">FY 2021 Budget Report</div>
                <div class="d-flex gap-2 mt-auto">
                  <button class="btn-generate"><i class="bi bi-download"></i> Generate PDF</button>
                  <button class="btn-csv">CSV</button>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
              <div class="report-card">
                <div class="report-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="report-title">Resident Census 2021</div>
                <div class="d-flex gap-2 mt-auto">
                  <button class="btn-generate"><i class="bi bi-download"></i> Generate PDF</button>
                  <button class="btn-csv">CSV</button>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
              <div class="report-card">
                <div class="report-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="report-title">Disaster Response Log</div>
                <div class="d-flex gap-2 mt-auto">
                  <button class="btn-generate"><i class="bi bi-download"></i> Generate PDF</button>
                  <button class="btn-csv">CSV</button>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
              <div class="report-card">
                <div class="report-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="report-title">Healthcare Utilization</div>
                <div class="d-flex gap-2 mt-auto">
                  <button class="btn-generate"><i class="bi bi-download"></i> Generate PDF</button>
                  <button class="btn-csv">CSV</button>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
              <div class="report-card">
                <div class="report-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="report-title">Education Assistance</div>
                <div class="d-flex gap-2 mt-auto">
                  <button class="btn-generate"><i class="bi bi-download"></i> Generate PDF</button>
                  <button class="btn-csv">CSV</button>
                </div>
              </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
              <div class="report-card">
                <div class="report-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div class="report-title">Voter Registration</div>
                <div class="d-flex gap-2 mt-auto">
                  <button class="btn-generate"><i class="bi bi-download"></i> Generate PDF</button>
                  <button class="btn-csv">CSV</button>
                </div>
              </div>
            </div>
          </div>
        </div>

  <!-- Bootstrap JS -->
  <script src="libs/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>