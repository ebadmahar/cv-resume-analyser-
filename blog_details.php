<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// session_start(); // Already started in functions.php

if (!isset($_GET['slug'])) {
    header("Location: blogs.php");
    exit();
}

$slug = $_GET['slug'];
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    header("Location: blogs.php"); // Or 404 page
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($blog['title']) ?> - CV Analyzer Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .blog-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
        }
        .blog-content h2 { margin-top: 2rem; margin-bottom: 1rem; font-weight: 700; color: #4e73df; }
        .blog-content h3 { margin-top: 1.5rem; margin-bottom: 1rem; font-weight: 600; }
        .blog-content p { line-height: 1.8; color: #4b5563; margin-bottom: 1.5rem; }
        .blog-content ul, .blog-content ol { margin-bottom: 1.5rem; color: #4b5563; }
        .blog-header-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 12px;
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

    <div class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="blogs.php" class="text-decoration-none">Blogs</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($blog['title']) ?></li>
                    </ol>
                </nav>

                <!-- Post Header -->
                <h1 class="display-4 fw-bold mb-3 text-dark"><?= htmlspecialchars($blog['title']) ?></h1>
                
                <div class="d-flex align-items-center mb-4 text-muted">
                    <div class="me-3">
                        <i class="far fa-calendar-alt me-1"></i> <?= date('M d, Y', strtotime($blog['created_at'])) ?>
                    </div>
                    <div class="me-3">
                        <i class="far fa-user me-1"></i> Admin
                    </div>
                    <div>
                        <?php 
                            $tags = explode(',', $blog['tags']);
                            foreach($tags as $tag): 
                                $tag = trim($tag);
                                if(empty($tag)) continue;
                        ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary me-1"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if($blog['image_url']): ?>
                    <img src="<?= htmlspecialchars($blog['image_url']) ?>" class="blog-header-img mb-5 shadow-sm" alt="<?= htmlspecialchars($blog['title']) ?>">
                <?php endif; ?>

                <!-- Content -->
                <article class="blog-content">
                    <?= $blog['content'] // Rich Text Content: Trusted Source (Admin) ?>
                </article>

                <hr class="my-5">

                <!-- CTA -->
                <div class="card bg-light border-0 p-4 text-center">
                    <h4 class="fw-bold mb-2">Ready to improve your CV?</h4>
                    <p class="text-muted mb-3">Get a free AI analysis in seconds.</p>
                    <a href="auth/register.php" class="btn btn-primary">Analyze My CV Now</a>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-5 mt-auto bg-white border-top">
        <div class="container">
            <div class="border-top pt-4 mt-4 text-center text-muted small">
                <p class="mb-0">&copy; <?= date('Y') ?> CV Analyzer. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>
