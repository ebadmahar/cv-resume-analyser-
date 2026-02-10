<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/pro_tips.php';

requireLogin();
requireAdmin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: users.php");
    exit();
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Admin Panel</title>
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
            <a href="index.php" class="nav-link">
                <i class="fas fa-home"></i> Overview
            </a>
            <a href="users.php" class="nav-link active">
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
                <h2 class="fw-bold mb-1">Manage Users</h2>
                <p class="text-secondary mb-0">View and manage registered users.</p>
            </div>
             <div class="d-flex align-items-center gap-3">
                 <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-normal">
                    <i class="fas fa-clock text-primary me-2"></i> <?= date('F j, Y') ?>
                 </span>
            </div>
        </div>
        
        <!-- Optional Pro Tip here too, why not -->
        <div class="row mb-4">
             <div class="col-12">
                <div class="card pro-tip-card p-3 border-0">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb fa-lg me-3 opacity-75"></i>
                        <p class="mb-0 fw-medium small opacity-90"><?= htmlspecialchars(getProTip()) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
             <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="fw-bold mb-0">Registered Users</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $user): ?>
                        <tr>
                            <td class="text-secondary">#<?= $user['id'] ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-3 text-secondary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="fw-medium"><?= htmlspecialchars($user['name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <?php if($user['role'] === 'admin'): ?>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">User</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary small"><?= date('M j, Y', strtotime($user['created_at'] ?? 'now')) ?></td>
                            <td class="text-end">
                                <?php if($user['role'] !== 'admin'): ?>
                                <a href="?delete=<?= $user['id'] ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Are you sure you want to delete this user? This cannot be undone.')">
                                    <i class="fas fa-trash-alt me-1"></i> Delete
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
