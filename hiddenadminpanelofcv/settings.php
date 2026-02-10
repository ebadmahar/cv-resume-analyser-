<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit();
}

$success = '';
$error = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api_key = $_POST['gemini_api_key'];
    $tiny_key = $_POST['tinymce_api_key'];
    
    // Save Gemini Key
    $stmt = $pdo->prepare("SELECT id FROM settings WHERE setting_key = 'gemini_api_key'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $update = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'gemini_api_key'");
        $update->execute([$api_key]);
    } else {
        $insert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('gemini_api_key', ?)");
        $insert->execute([$api_key]);
    }

    // Save TinyMCE Key
    $stmt = $pdo->prepare("SELECT id FROM settings WHERE setting_key = 'tinymce_api_key'");
    $stmt->execute();
    if ($stmt->fetch()) {
        $update = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'tinymce_api_key'");
        $update->execute([$tiny_key]);
    } else {
        $insert = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('tinymce_api_key', ?)");
        $insert->execute([$tiny_key]);
    }
    
    $success = "Settings updated successfully.";
}

// Fetch Current Settings
$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'gemini_api_key'");
$stmt->execute();
$current_key = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'tinymce_api_key'");
$stmt->execute();
$current_tiny_key = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #fff;
            border-right: 1px solid #e3e6f0;
            position: fixed;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        .nav-link {
            color: #5a5c69;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: #f8f9fc;
            border-right: 3px solid var(--primary-color);
        }
        .nav-link i { width: 25px; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <a href="../index.php" class="d-flex align-items-center justify-content-center py-4 text-decoration-none">
            <h4 class="fw-bold text-primary m-0"><i class="fas fa-robot me-2"></i>Admin</h4>
        </a>
        <nav class="nav flex-column mt-2">
            <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php" class="nav-link"><i class="fas fa-users"></i> Users</a>
            <a href="blogs.php" class="nav-link"><i class="fas fa-newspaper"></i> Blogs</a>
            <a href="settings.php" class="nav-link active"><i class="fas fa-cog"></i> Settings</a>
            <a href="../auth/logout.php" class="nav-link mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h2 class="fw-bold text-dark mb-4">System Settings</h2>

        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="m-0 fw-bold text-primary">API Configuration</h6>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Google Gemini API Key</label>
                        <div class="input-group">
                             <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="text" name="gemini_api_key" class="form-control" value="<?= htmlspecialchars($current_key) ?>" placeholder="Enter Gemini API Key">
                        </div>
                        <div class="form-text">Required for CV analysis functionality.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">TinyMCE API Key</label>
                        <div class="input-group">
                             <span class="input-group-text"><i class="fas fa-pen-nib"></i></span>
                            <input type="text" name="tinymce_api_key" class="form-control" value="<?= htmlspecialchars($current_tiny_key) ?>" placeholder="Enter TinyMCE API Key (or use 'no-api-key')">
                        </div>
                        <div class="form-text">Required for the rich text editor in Blogs. Get one for free at tiny.cloud.</div>
                    </div>

                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
