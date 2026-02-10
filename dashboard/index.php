<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/pro_tips.php';

requireLogin();

// Fetch latest analysis
$stmt = $pdo->prepare("SELECT * FROM cv_analyses WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$_SESSION['user_id']]);
$lastAnalysis = $stmt->fetch();

// Fetch history count
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM cv_analyses WHERE user_id = ?");
$stmtCount->execute([$_SESSION['user_id']]);
$totalAnalyses = $stmtCount->fetchColumn();

// Don't include the default header; we'll build a custom dashboard layout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - AI CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4e73df;
            --bg-color: #f8f9fc;
        }
        body {
            background-color: var(--bg-color);
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #fff;
            position: fixed;
            top: 0;
            left: 0;
            border-right: 1px solid #e3e6f0;
            z-index: 100;
        }
        
        .sidebar-brand {
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 20px;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-color);
            border-bottom: 1px solid #f0f0f0;
        }

        .nav-link {
            padding: 15px 20px;
            color: #5a5c69;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: 0.2s;
        }
        
        .nav-link i {
            width: 25px;
            margin-right: 10px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: #f8f9fc;
            border-right: 4px solid var(--primary-color);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }

        .score-display {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: conic-gradient(var(--primary-color) 0%, var(--primary-color) var(--score, 0%), #e9ecef var(--score, 0%), #e9ecef 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            position: relative;
        }

        .score-inner {
            width: 100px;
            height: 100px;
            background: #fff;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .score-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-robot me-2"></i> CV Analyzer
    </div>
    <nav class="nav flex-column mt-4">
        <a href="index.php" class="nav-link active">
            <i class="fas fa-columns"></i> Dashboard
        </a>
        <a href="../history/index.php" class="nav-link">
            <i class="fas fa-history"></i> History
        </a>
        <a href="../auth/logout.php" class="nav-link text-danger mt-5">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
            <p class="text-muted">Here's what's happening with your job applications.</p>
        </div>
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-plus me-2"></i> New Analysis
        </button>
    </div>

    <?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <strong>Error:</strong> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if(isset($_GET['warning'])): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> <strong>Notice:</strong> <?= htmlspecialchars($_GET['warning']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Pro Tip Section -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted text-uppercase fw-bold">Total Scans</small>
                        <h3 class="mb-0 mt-1"><?= $totalAnalyses ?></h3>
                    </div>
                    <div class="bg-light p-3 rounded-circle text-primary">
                        <i class="fas fa-file-alt fa-lg"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card p-3 h-100 border-0 bg-gradient text-white" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);">
                <div class="d-flex align-items-center h-100">
                    <div class="me-3 opacity-75"><i class="fas fa-lightbulb fa-2x"></i></div>
                    <div>
                        <h6 class="fw-bold mb-1">Pro Tip for your CV:</h6>
                        <p class="mb-0 small opacity-90"><?= htmlspecialchars(getProTip()) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Analysis Display -->
        <div class="col-lg-8">
            <?php if($lastAnalysis): 
                $strengths = json_decode($lastAnalysis['strengths'], true);
                $weaknesses = json_decode($lastAnalysis['weaknesses'], true);
                $missing_skills = json_decode($lastAnalysis['missing_skills'], true);
                $roadmap = json_decode($lastAnalysis['roadmap'], true);
                $suggestions = json_decode($lastAnalysis['suggestions'], true);
                $score = $lastAnalysis['score'];
            ?>
            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary fw-bold">Latest Report Analysis</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-4 text-center">
                            <div class="score-display" style="--score: <?= $score ?>%">
                                <div class="score-inner">
                                    <span class="score-number"><?= $score ?></span>
                                    <small class="text-muted">/100</small>
                                </div>
                            </div>
                            <p class="mt-3 fw-bold mb-0">Overall Score</p>
                            <small class="text-muted"><?= date('M d, Y', strtotime($lastAnalysis['created_at'])) ?></small>
                        </div>
                        <div class="col-md-8">
                             <div class="alert alert-light border">
                                <strong><i class="fas fa-lightbulb text-warning me-2"></i>Quick Summary:</strong>
                                <p class="mb-0 mt-2 text-muted small">Your CV is performing above average. Focus on the missing skills identified to boost your score.</p>
                             </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-4">
                            <h6 class="fw-bold text-success"><i class="fas fa-check-circle me-2"></i>Strengths</h6>
                            <ul class="small text-muted ps-3">
                                <?php if($strengths) foreach($strengths as $s): ?>
                                    <li class="mb-1"><?= htmlspecialchars($s) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold text-danger"><i class="fas fa-exclamation-circle me-2"></i>Weaknesses</h6>
                            <ul class="small text-muted ps-3">
                                <?php if($weaknesses) foreach($weaknesses as $w): ?>
                                    <li class="mb-1"><?= htmlspecialchars($w) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold text-warning"><i class="fas fa-search-minus me-2"></i>Missing Skills</h6>
                            <ul class="small text-muted ps-3">
                                <?php if($missing_skills) foreach($missing_skills as $m): ?>
                                    <li class="mb-1"><?= htmlspecialchars($m) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="fw-bold text-info"><i class="fas fa-road me-2"></i>Action Plan</h6>
                    <div class="list-group list-group-flush small mb-4">
                         <?php if($roadmap) foreach($roadmap as $index => $r): ?>
                            <div class="list-group-item px-0">
                                <span class="badge bg-info me-2"><?= $index + 1 ?></span> <?= htmlspecialchars($r) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <h6 class="fw-bold text-primary"><i class="fas fa-lightbulb me-2"></i>Pro Suggestions</h6>
                    <ul class="list-group list-group-flush small">
                         <?php if($suggestions) foreach($suggestions as $s): ?>
                            <li class="list-group-item px-0"><i class="fas fa-angle-right text-primary me-2"></i><?= htmlspecialchars($s) ?></li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <img src="../assets/no-data.svg" alt="No Data" style="max-width: 200px; opacity: 0.5;">
                <h4 class="mt-4 text-muted">No analysis yet</h4>
                <p>Upload your first CV to get started!</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload CV</button>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Additional Resources / History Preview -->
            <div class="card p-4">
                <h5 class="fw-bold mb-3">Quick Actions</h5>
                <button class="btn btn-outline-primary mb-2 w-100" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="fas fa-cloud-upload-alt me-2"></i> Upload New CV</button>
                <a href="../history/index.php" class="btn btn-outline-secondary w-100"><i class="fas fa-history me-2"></i> View History</a>
            </div>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload CV for AI Analysis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="uploadError"></div>
                    <div class="mb-3">
                        <label class="form-label">Select PDF File</label>
                        <input type="file" name="cv_file" class="form-control" accept=".pdf" required>
                        <small class="text-muted">Max file size: 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Start Analysis</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.9); z-index: 1050; align-items: center; justify-content: center; flex-direction: column;">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h4 class="text-white mt-4 fw-bold">Analyzing your CV...</h4>
    <p class="text-white-50">This might take a few moments. We are processing your data.</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    let overlay = document.getElementById('loadingOverlay');
    let modalEl = document.getElementById('uploadModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    let errorDiv = document.getElementById('uploadError');
    
    modal.hide();
    overlay.style.display = 'flex';
    errorDiv.classList.add('d-none');
    
    fetch('process_cv.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            window.location.reload();
        } else {
            overlay.style.display = 'none';
            modal.show();
            errorDiv.textContent = data.message || 'Something went wrong.';
            errorDiv.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        overlay.style.display = 'none';
        modal.show();
        errorDiv.textContent = 'Network error or server unavailable. Please try again.';
        errorDiv.classList.remove('d-none');
    });
});
</script>
</body>
</html>
