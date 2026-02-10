<?php
require_once 'config/db.php';

$name = 'Admin';
$email = 'ebad@admin.com';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = 'admin';

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        // Update existing user to admin
        $updateForAdmin = $pdo->prepare("UPDATE users SET password = ?, role = 'admin' WHERE email = ?");
        $updateForAdmin->execute([$hashed_password, $email]);
        echo "User $email updated to Admin role with password: $password";
    } else {
        // Insert new admin
        $insertStmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $insertStmt->execute([$name, $email, $hashed_password, $role]);
        echo "Admin user created successfully.<br>";
        echo "Email: $email<br>";
        echo "Password: $password<br>";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
