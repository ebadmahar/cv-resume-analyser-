<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

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
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="d-flex">
                <a href="users.php" class="btn btn-outline-light me-2">Manage Users</a>
                <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-4"><?= $stats['users'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Analyses</h5>
                        <p class="card-text display-4"><?= $stats['analyses'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">Gemini API Key</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="input-group">
                                <input type="text" name="api_key" class="form-control" value="<?= htmlspecialchars($stats['api_key'] ?? '') ?>" placeholder="Enter API Key">
                                <button class="btn btn-primary" type="submit">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <h4 class="mt-4">Recent Logins</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Time</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['name']) ?></td>
                    <td><?= $log['login_time'] ?></td>
                    <td><?= $log['ip_address'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
