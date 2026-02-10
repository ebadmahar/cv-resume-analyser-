<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Check Maintenance Mode (requires $pdo from db.php)
$maintenance = getSetting($pdo, 'maintenance_mode');
if ($maintenance === '1' && !isset($_SESSION['user_id'])) {
    // Check if user is admin
    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
    $isAdmin = false;
    if(isset($_SESSION['user_id'])){
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if($user && $user['role'] === 'admin') $isAdmin = true;
    }
    
    if(!$isAdmin){
        header("Location: maintenance.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI CV Analyzer - Professional Resume Insights</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --dark: #0f172a;
            --light: #f8fafc;
            --surface: #ffffff;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--dark);
            background-color: var(--light);
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            font-weight: 700;
            letter-spacing: -0.025em;
        }

        /* Navbar */
        .navbar {
            background: var(--surface);
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--secondary);
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-link:hover {
            color: var(--primary);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
            padding: 0.6rem 1.5rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary);
            color: #fff;
        }

        /* Hero Section */
        .hero {
            padding: 5rem 0;
            background: linear-gradient(180deg, var(--surface) 0%, var(--light) 100%);
        }

        .hero h1 {
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            background: linear-gradient(to right, var(--dark), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--secondary);
            margin-bottom: 2rem;
            max-width: 600px;
        }

        /* Features */
        .feature-card {
            background: var(--surface);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 2rem;
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        /* Testimonials */
        .testimonial-card {
            background: var(--surface);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Footer */
        footer {
            background: var(--surface);
            border-top: 1px solid #e2e8f0;
            padding: 4rem 0;
            margin-top: 5rem;
        }

    </style>
<link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-layer-group me-2"></i>CV Analyzer
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link mx-2" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link mx-2" href="#testimonials">Testimonials</a></li>
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

    <!-- Hero Section -->
    <section class="hero-gradient d-flex align-items-center">
        <div class="container">
            <div class="row align-items-center pt-5 pb-5">
                <div class="col-lg-6">
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 mb-3 px-3 py-2 rounded-pill">
                        <i class="fas fa-sparkles me-1"></i> AI-Powered Resume Analysis
                    </span>
                    <h1 class="display-4 fw-bold mb-4">Land your dream job with data-driven insights.</h1>
                    <p class="lead mb-4">Upload your CV and get an instant, professional analysis. Identify skill gaps, improve your ATS score, and follow a personalized roadmap.</p>
                    <div class="d-flex gap-3">
                        <a href="auth/register.php" class="btn btn-primary btn-lg shadow-sm">Start Free Analysis</a>
                        <a href="blogs.php" class="btn btn-outline-primary btn-lg">Blogs</a>
                    </div>
                    <div class="mt-4 d-flex align-items-center text-muted small pb-5">
                        <i class="fas fa-check-circle text-success me-1"></i> 100% Free &nbsp;&nbsp;
                        <i class="fas fa-check-circle text-success me-1"></i> Instant results
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0 text-center">
                    <img src="assets/hero_illustration.svg" alt="Analysis Dashboard" class="img-fluid rounded-3 shadow-lg" style="transform: perspective(1000px) rotateY(-5deg); max-width: 90%;">
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-5 bg-white">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="h1 mb-3">Everything you need to improve</h2>
                <p class="text-secondary">Comprehensive tools to analyze and enhance your professional profile.</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box">
                            <i class="fas fa-tachometer-alt fa-lg"></i>
                        </div>
                        <h4>Instant ATS Scoring</h4>
                        <p class="text-secondary">Our AI evaluates your resume against industry standards and gives you a score out of 100.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box">
                            <i class="fas fa-microscope fa-lg"></i>
                        </div>
                        <h4>Deep Skill Analysis</h4>
                        <p class="text-secondary">We identify missing keywords and skills that recruiters in your field are looking for.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="icon-box">
                            <i class="fas fa-map-signs fa-lg"></i>
                        </div>
                        <h4>Career Roadmap</h4>
                        <p class="text-secondary">Get a step-by-step action plan to address weaknesses and improve your hireability.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Trusted by Professionals</h2>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/women/44.jpg" class="user-avatar me-3" alt="Sarah J.">
                            <div>
                                <h6 class="mb-0">Sarah Jenkins</h6>
                                <small class="text-secondary">Senior Developer at TechCorp</small>
                            </div>
                            <div class="ms-auto text-warning">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-secondary mb-0">"The roadmap feature was incredible. It pinpointed exactly what I needed to learn, and I got the promotion within months."</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" class="user-avatar me-3" alt="David C.">
                            <div>
                                <h6 class="mb-0">David Chen</h6>
                                <small class="text-secondary">Product Manager</small>
                            </div>
                            <div class="ms-auto text-warning">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                        <p class="text-secondary mb-0">"Simple, fast, and accurate. The ATS scoring helped me rewrite my resume to get past the initial screening bots."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Company</h6>
                    <ul class="list-unstyled text-secondary">
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">About</a></li>
                        <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Contact</a></li>
                         <li class="mb-2"><a href="#" class="text-decoration-none text-secondary">Blogs</a></li>
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
