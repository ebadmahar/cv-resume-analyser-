<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = "Email is already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $hashed_password])) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - AI CV Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: var(--bg-color);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-card {
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            border-radius: 0.5rem;
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        .logo {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .form-control {
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        .btn-primary {
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <a href="../index.php" class="logo">
            <i class="fas fa-robot me-2"></i>CV Analyzer
        </a>
        <h4 class="mb-4 text-center text-dark fw-bold">Create an Account</h4>
        
        <?php if($error): ?>
             <div class="alert alert-danger text-center py-2 border-0 small rounded-3 mb-4">
                <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if($success): ?>
             <div class="alert alert-success text-center py-2 border-0 small rounded-3 mb-4">
                <i class="fas fa-check-circle me-1"></i> <?= $success ?> <br> <a href="login.php" class="fw-bold text-decoration-underline">Login here</a>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Full Name</label>
                <input type="text" name="name" class="form-control" required placeholder="Full Name">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Email Address">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small text-muted fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="Password">
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label small text-muted fw-bold">Confirm</label>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="Repeat Password">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">Register Account</button>
        </form>

        <hr>
        <div class="text-center small">
            <a href="login.php" class="text-decoration-none">Already have an account? Login!</a>
        </div>
        <div class="text-center small mt-2">
            <a href="../index.php" class="text-decoration-none text-muted">Back to Home</a>
        </div>
    </div>
</body>
</html>
