<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

// Fetch all analyses for history
$stmt = $pdo->prepare("SELECT * FROM cv_analyses WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$analyses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History - AI CV Analyzer</title>
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
        
        /* Sidebar (Same structure as dashboard) */
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

        /* Table Styling */
        .table thead th {
            border-top: none;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
            color: #4e73df;
            text-transform: uppercase;
            font-size: 0.8rem;
            padding: 1rem;
        }
        
        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
            color: #5a5c69;
            border-bottom: 1px solid #f8f9fc;
        }

        .score-badge {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            color: #fff;
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
        <a href="../dashboard/index.php" class="nav-link">
            <i class="fas fa-columns"></i> Dashboard
        </a>
        <a href="index.php" class="nav-link active">
            <i class="fas fa-history"></i> History
        </a>
        <a href="../auth/logout.php" class="nav-link text-danger mt-5">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>
</div>

<!-- Main Content -->
<div class="main-content">
    <h2 class="mb-4">Analysis History</h2>

    <div class="card bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Score</th>
                            <th>Summary</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($analyses): foreach($analyses as $a): ?>
                        <tr>
                            <td>
                                <div class="fw-bold"><?= date('M d, Y', strtotime($a['created_at'])) ?></div>
                                <small class="text-muted"><?= date('h:i A', strtotime($a['created_at'])) ?></small>
                            </td>
                            <td>
                                <?php 
                                    $score = $a['score'];
                                    $color = $score >= 80 ? 'bg-success' : ($score >= 50 ? 'bg-warning' : 'bg-danger');
                                ?>
                                <div class="score-badge <?= $color ?>">
                                    <?= $score ?>
                                </div>
                            </td>
                            <td>
                                <p class="mb-0 text-truncate" style="max-width: 300px;">
                                    Analyzed CV ID #<?= $a['id'] ?> - 
                                    <span class="text-muted">Analysis complete.</span>
                                </p>
                            </td>
                            <td class="text-end">
                                <a href="../dashboard/index.php?view_id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    View Report
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <p class="text-muted mb-0">No analysis history found.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
