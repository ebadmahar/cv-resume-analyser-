<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions.php';

// Check Maintenance (Simplistic check for now, can be refactored to a common file)
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
    <title>FAQ - CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar (Simplified) -->
    <nav class="navbar navbar-expand-lg sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-file-alt me-2"></i>CV Analyzer</a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary btn-sm">Home</a>
            </div>
        </div>
    </nav>

    <header class="hero-gradient text-center">
        <div class="container">
            <h1 class="display-4 fw-bold text-dark">Frequently Asked Questions</h1>
            <p class="lead text-secondary">Got questions? We've got answers.</p>
        </div>
    </header>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                How does the AI analysis work?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                We use advanced Large Language Models (Gemini) to parse the text of your CV. The AI evaluates your experience, skills, and formatting against industry standards to provide a comprehensive score and actionable feedback.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                Is my data safe?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Yes! We parse your CV to extract text for analysis and store the results securely. We do not sell your personal data to third parties.
                            </div>
                        </div>
                    </div>
                     <div class="accordion-item border-0 shadow-sm mb-3 rounded overflow-hidden">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                What file formats are supported?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary">
                                Currently, we support <strong>PDF</strong> files only. This ensures the most consistent formatting analysis.
                            </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
