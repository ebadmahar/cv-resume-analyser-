<?php
if (session_status() === PHP_SESSION_NONE) {
    // strict session security
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'domain' => '',
        'secure' => isset($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Strict'
    ]);
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /CV/auth/login.php");
        exit;
    }
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin() {
    if (!isAdmin()) {
        header("Location: /CV/auth/login.php");
        exit;
    }
}

function sanitizeInput($d) {
    return htmlspecialchars(stripslashes(trim($d)));
}

function logLogin($pdo, $uid) {
    $pdo->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)")
        ->execute([$uid, $_SERVER['REMOTE_ADDR']]);
}

function getSetting($pdo, $key) {
    $q = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $q->execute([$key]);
    return $q->fetchColumn();
}
