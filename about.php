<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';

// Maintenance Check
$maintenance = getSetting($pdo, 'maintenance_mode');
if ($maintenance === '1') {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $isAdmin = false;
    if(isset($_SESSION['user_id'])){
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if($user && $user['role'] === 'admin') $isAdmin = true;
    }
    if(!$isAdmin){ header("Location: maintenance.php"); exit(); }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us - CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-file-alt me-2"></i>CV Analyzer</a>
            <div class="ms-auto">
                 <a href="index.php" class="btn btn-outline-primary btn-sm me-2">Home</a>
                 <a href="contact.php" class="btn btn-outline-primary btn-sm">Contact</a>
            </div>
        </div>
    </nav>

    <header class="hero-gradient about text-center">
        <div class="container">
            <h1 class="display-4 fw-bold text-dark">About Us</h1>
            <p class="lead text-secondary">Empowering job seekers with AI-driven insights.</p>
        </div>
    </header>

    <div class="container my-5">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3 text-dark">Our Mission</h2>
                <p class="text-secondary lead text-justify">We believe that every candidate deserves a fair chance to showcase their true potential. Our mission is to bridge the gap between talented individuals and their dream jobs by providing instant, professional-grade CV analysis.</p>
                <p class="text-secondary text-justify">Using state-of-the-art AI technology, we decode the complexities of Applicant Tracking Systems (ATS) and recruiter expectations, giving you a competitive edge.</p>
            </div>
            <div class="col-lg-6 text-center">
                 <img src="https://cdni.iconscout.com/illustration/premium/thumb/team-working-on-project-3329598-2809494.png" alt="Team" class="img-fluid" style="max-width: 400px;">
            </div>
        </div>

        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="card p-4 h-100">
                    <div class="card-body">
                        <div class="text-primary mb-3"><i class="fas fa-rocket fa-3x"></i></div>
                        <h5 class="fw-bold">Innovation</h5>
                        <p class="text-secondary small">Leveraging the latest in AI to provide cutting-edge analysis.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100">
                     <div class="card-body">
                        <div class="text-success mb-3"><i class="fas fa-user-shield fa-3x"></i></div>
                        <h5 class="fw-bold">Privacy</h5>
                        <p class="text-secondary small">Your career data is personal. We keep it secure and private.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100">
                     <div class="card-body">
                        <div class="text-warning mb-3"><i class="fas fa-bolt fa-3x"></i></div>
                        <h5 class="fw-bold">Speed</h5>
                        <p class="text-secondary small">Instant feedback to help you iterate and improve quickly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="py-5 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3"><i class="fas fa-layer-group me-2"></i>CV Analyzer</h5>
                    <p class="text-secondary">Empowering professionals with AI-driven career insights. Get your resume analyzed instantly.</p>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Product</h6>
                    <ul class="list-unstyled text-secondary">
                        <li class="mb-2"><a href="index.php" class="text-decoration-none text-secondary">Home</a></li>
                        <li class="mb-2"><a href="auth/register.php" class="text-decoration-none text-secondary">Get Started</a></li>
                        <li class="mb-2"><a href="faq.php" class="text-decoration-none text-secondary">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Company</h6>
                    <ul class="list-unstyled text-secondary">
                        <li class="mb-2"><a href="about.php" class="text-decoration-none text-secondary">About</a></li>
                        <li class="mb-2"><a href="contact.php" class="text-decoration-none text-secondary">Contact</a></li>
                         <li class="mb-2"><a href="blogs.php" class="text-decoration-none text-secondary">Blogs</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Stay Updated</h6>
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button class="btn btn-primary">Subscribe</button>
                    </div>
                </div>
            </div>
            <div class="border-top pt-4 mt-4 text-center text-secondary">
                <p class="small mb-0">&copy; <?= date('Y') ?> CV Analyzer. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
