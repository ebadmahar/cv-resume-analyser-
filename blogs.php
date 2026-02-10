<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// session_start(); // Already started in functions.php

// Fetch Published Blogs
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE status = 'published' ORDER BY created_at DESC");
$stmt->execute();
$blogs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog - CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
    </style>
</head>
<body>

   <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="fas fa-layer-group me-2"></i>CV Analyzer
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link mx-2" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link mx-2 active fw-bold text-primary" href="blogs.php">Blogs</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a href="dashboard/" class="btn btn-primary ms-3">Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link mx-2" href="auth/login.php">Login</a></li>
                        <li class="nav-item"><a href="auth/register.php" class="btn btn-primary ms-3">Get Started</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <header class="hero-gradient text-center py-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Latest Insights</h1>
            <p class="lead text-muted">Tips, tricks, and guides to land your dream job.</p>
        </div>
    </header>

    <!-- Blog Grid -->
    <div class="container my-5 flex-grow-1">
        <div class="row g-4">
            <?php foreach($blogs as $blog): ?>
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <?php if($blog['image_url']): ?>
                        <img src="<?= htmlspecialchars($blog['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($blog['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-muted" style="height: 200px;">
                            <i class="fas fa-image fa-3x"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <?php 
                                $tags = explode(',', $blog['tags']);
                                foreach($tags as $tag): 
                                    $tag = trim($tag);
                                    if(empty($tag)) continue;
                            ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary me-1"><?= htmlspecialchars($tag) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <h5 class="card-title fw-bold">
                            <a href="blog_details.php?slug=<?= htmlspecialchars($blog['slug']) ?>" class="text-decoration-none text-dark stretched-link">
                                <?= htmlspecialchars($blog['title']) ?>
                            </a>
                        </h5>
                        <p class="card-text text-muted small flex-grow-1">
                            <?= htmlspecialchars(substr($blog['short_description'], 0, 100)) ?>...
                        </p>
                        <div class="mt-3 text-muted small">
                            <i class="far fa-calendar-alt me-1"></i> <?= date('M d, Y', strtotime($blog['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($blogs)): ?>
                <div class="col-12 text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-newspaper fa-3x mb-3"></i>
                        <p>No blog posts published yet. Check back soon!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-5 mt-auto bg-white border-top">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-layer-group me-2 text-primary"></i>CV Analyzer</h5>
                    <p class="text-muted small">AI-powered resume analysis to help you land your dream job. secure, fast, and data-driven.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Product</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="index.php" class="text-decoration-none text-muted">Home</a></li>
                        <li class="mb-2"><a href="auth/register.php" class="text-decoration-none text-muted">Get Started</a></li>
                        <li class="mb-2"><a href="blogs.php" class="text-decoration-none text-muted">Blogs</a></li>
                    </ul>
                </div>
                 <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Company</h6>
                    <ul class="list-unstyled text-secondary small">
                        <li class="mb-2"><a href="about.php" class="text-decoration-none text-muted">About</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-decoration-none text-muted">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-top pt-4 mt-4 text-center text-muted small">
                <p class="mb-0">&copy; <?= date('Y') ?> CV Analyzer. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
