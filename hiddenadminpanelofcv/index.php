<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/pro_tips.php';

requireLogin();
requireAdmin();

// Fetch Stats
$stats = [];
$stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['analyses'] = $pdo->query("SELECT COUNT(*) FROM cv_analyses")->fetchColumn();
$stats['api_key'] = getSetting($pdo, 'gemini_api_key');

// Update API Key
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['api_key'])) {
    $apiKey = sanitizeInput($_POST['api_key']);
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('gemini_api_key', ?) ON DUPLICATE KEY UPDATE setting_value = ?");
    $stmt->execute([$apiKey, $apiKey]);
    header("Location: index.php");
    exit();
}

// Fetch Logs
$logs = $pdo->query("SELECT l.login_time, l.ip_address, u.name FROM login_logs l JOIN users u ON l.user_id = u.id ORDER BY l.login_time DESC LIMIT 10")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - AI CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #4f46e5;
            --bg-color: #f3f4f6;
            --text-main: #111827;
            --text-secondary: #6b7280;
        }

        body {
            background-color: var(--bg-color);
            font-family: 'Outfit', sans-serif;
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            background: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            border-right: 1px solid #e5e7eb;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            height: 70px;
            display: flex;
            align-items: center;
            padding: 0 24px;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary-color);
            border-bottom: 1px solid #f3f4f6;
        }

        .nav-link {
            padding: 12px 24px;
            color: var(--text-secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: all 0.2s;
            margin: 4px 12px;
            border-radius: 8px;
        }

        .nav-link i {
            width: 24px;
            margin-right: 12px;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: #eef2ff;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 32px;
        }

        .card {
            border: none;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .table thead th {
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            border-bottom-width: 1px;
            padding: 12px 16px;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            color: var(--text-main);
            border-bottom: 1px solid #f3f4f6;
        }

        .pro-tip-card {
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            color: #fff;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-shield-alt me-2"></i> Admin Panel
        </div>
        <nav class="nav flex-column mt-4 flex-grow-1">
            <a href="index.php" class="nav-link active">
                <i class="fas fa-home"></i> Overview
            </a>
            <a href="users.php" class="nav-link">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="../index.php" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </nav>
        <div class="p-3 border-top">
             <a href="../auth/logout.php" class="nav-link text-danger m-0">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold mb-1">Dashboard Overview</h2>
                <p class="text-secondary mb-0">Welcome back, Admin.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                 <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-normal">
                    <i class="fas fa-clock text-primary me-2"></i> <?= date('F j, Y') ?>
                 </span>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card stat-card h-100 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1">Total Users</p>
                            <h2 class="fw-bold mb-0"><?= number_format($stats['users']) ?></h2>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card h-100 p-4">
                     <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-secondary mb-1">Total Analyses</p>
                            <h2 class="fw-bold mb-0"><?= number_format($stats['analyses']) ?></h2>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-md-4">
                <div class="card pro-tip-card h-100 p-4 border-0">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-lightbulb fa-lg me-3 mt-1 opacity-75"></i>
                        <div>
                            <h6 class="fw-bold opacity-90 mb-2">Pro Tip of the Moment</h6>
                            <p class="small mb-0 opacity-90" style="line-height: 1.5;"><?= htmlspecialchars(getProTip()) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Recent Logins</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>User</th>
                                    <th>Date & Time</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($logs as $log): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded-circle p-2 me-3 text-secondary">
                                                <i class="fas fa-user-circle"></i>
                                            </div>
                                            <span class="fw-medium"><?= htmlspecialchars($log['name']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-secondary small"><?= date('M j, Y H:i', strtotime($log['login_time'])) ?></td>
                                    <td class="font-monospace text-secondary small"><?= $log['ip_address'] ?></td>
                                    <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill" style="font-size: 0.7rem;">Success</span></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="fw-bold mb-0">Settings</h5>
                    </div>
                    <div class="card-body px-4">
                         <form method="POST">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Gemini API Key</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-secondary"><i class="fas fa-key"></i></span>
                                    <input type="text" name="api_key" class="form-control border-start-0 ps-0" value="<?= htmlspecialchars($stats['api_key'] ?? '') ?>" placeholder="Paste key here">
                                </div>
                                <div class="form-text small">Required for CV analysis functionalities.</div>
                            </div>
                            <button class="btn btn-primary w-100 py-2 rounded-3 fw-bold" type="submit">
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
