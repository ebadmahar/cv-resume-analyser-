<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="/CV/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if(isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="/CV/dashboard/index.php">AI CV Analyzer</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/CV/dashboard/index.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/CV/history/index.php">History</a></li>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/CV/hiddenadminpanelofcv/">Admin</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-white me-3">Hello, <?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></span>
            <a href="/CV/auth/logout.php" class="btn btn-light btn-sm text-primary fw-bold">Logout</a>
        </div>
    </div>
</nav>
<?php endif; ?>
<div class="container">
