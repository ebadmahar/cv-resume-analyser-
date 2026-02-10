<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        
        logLogin($pdo, $user['id']);

        if ($user['role'] === 'admin') {
            header("Location: ../hiddenadminpanelofcv/index.php");
        } else {
            header("Location: ../dashboard/index.php");
        }
        exit();
    } else {
        $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - AI CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
         body {
            font-family: 'Outfit', sans-serif;
            background-image: url('../assets/hero_bg_blur.jpg'); /* You might need to create this or use a CSS gradient fallback */
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        /* CSS Gradient Fallback/Overlay */
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.8) 0%, rgba(15, 23, 42, 0.9) 100%);
            z-index: -1;
        }

        /* Glassmorphism Card */
        .auth-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            color: #fff;
        }

        .auth-card h4 {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 0.8rem 1rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .btn-primary {
            background: #fff;
            color: #2563eb;
            border: none;
            padding: 0.8rem;
            font-weight: 700;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #e2e8f0;
            color: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .logo i {
            margin-right: 0.5rem;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.2);
            border: 1px solid rgba(220, 38, 38, 0.3);
            color: #fecaca;
        }
        
        a.text-primary {
            color: #60a5fa !important;
        }
        
        a.text-primary:hover {
            text-decoration: underline !important;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <a href="../index.php" class="logo">
            <i class="fas fa-robot"></i> CV Analyzer
        </a>
        <h4 class="mb-2 text-center">Welcome back</h4>
        <p class="text-center text-white-50 mb-4 small">Enter your credentials to access your dashboard.</p>
        
        <?php if($error): ?>
            <div class="alert alert-danger text-center py-2 border-0 small rounded-3 mb-4">
                <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white-50 ps-0" style="position: absolute; z-index: 10; left: 10px; top: 10px;"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control ps-5" required placeholder="you@example.com" style="padding-left: 2.5rem !important;">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 text-white-50 ps-0" style="position: absolute; z-index: 10; left: 10px; top: 10px;"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control ps-5" required placeholder="••••••••" style="padding-left: 2.5rem !important;">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Sign In</button>
        </form>

        <p class="text-center text-white-50 small mb-0">
            Don't have an account? <a href="register.php" class="text-primary text-decoration-none fw-bold">Sign up</a>
        </p>
    </div>
</body>
</html>
