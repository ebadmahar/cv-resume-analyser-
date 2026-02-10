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
    <title>Contact Us - CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-file-alt me-2"></i>CV Analyzer</a>
            <div class="ms-auto">
                 <a href="index.php" class="btn btn-outline-primary btn-sm me-2">Home</a>
                 <a href="about.php" class="btn btn-outline-primary btn-sm">About</a>
            </div>
        </div>
    </nav>

    <header class="hero-gradient contact text-center">
        <div class="container">
            <h1 class="display-4 fw-bold text-dark">Get in Touch</h1>
            <p class="lead text-secondary">We'd love to hear from you.</p>
        </div>
    </header>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card p-5 border-0 shadow-lg">
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Name</label>
                                <input type="text" class="form-control bg-light border-0 py-2" placeholder="Your Name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Email</label>
                                <input type="email" class="form-control bg-light border-0 py-2" placeholder="name@example.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary">Subject</label>
                                <input type="text" class="form-control bg-light border-0 py-2" placeholder="How can we help?">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small text-secondary">Message</label>
                                <textarea class="form-control bg-light border-0" rows="5" placeholder="Write your message here..."></textarea>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="button" class="btn btn-primary px-5 py-2 fw-bold" onclick="alert('Thanks for contacting us! We will get back to you soon.')">Send Message</button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="row mt-5 pt-4 border-top text-center">
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-envelope text-primary fa-lg mb-2"></i>
                            <p class="small text-muted">support@cvanalyzer.com</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-phone text-primary fa-lg mb-2"></i>
                            <p class="small text-muted">+1 (555) 123-4567</p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <i class="fas fa-map-marker-alt text-primary fa-lg mb-2"></i>
                            <p class="small text-muted">123 AI Street, Tech City</p>
                        </div>
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
