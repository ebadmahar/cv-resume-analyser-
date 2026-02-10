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
            max-width: 400px;
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
        <h4 class="mb-4 text-center text-dark fw-bold">Welcome Back!</h4>
        
        <?php if($error): ?>
            <div class="alert alert-danger text-center py-2 border-0 small rounded-3 mb-4">
                <i class="fas fa-exclamation-circle me-1"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small text-muted fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="Enter Email Address...">
            </div>
            <div class="mb-4">
                <label class="form-label small text-muted fw-bold">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
        </form>

        <hr>
        <div class="text-center small">
            <a href="register.php" class="text-decoration-none">Create an Account!</a>
        </div>
        <div class="text-center small mt-2">
            <a href="../index.php" class="text-decoration-none text-muted">Back to Home</a>
        </div>
    </div>
</body>
</html>
