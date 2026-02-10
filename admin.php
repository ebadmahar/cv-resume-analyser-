<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

// Log the attempt
$ip = $_SERVER['REMOTE_ADDR'];
$url = $_SERVER['REQUEST_URI'];
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

try {
    $stmt = $pdo->prepare("INSERT INTO security_logs (ip_address, attempted_url, user_agent) VALUES (?, ?, ?)");
    $stmt->execute([$ip, $url, $userAgent]);
} catch (Exception $e) {
    // Silently fail logging if DB issue, but still show 404
}

// Show 404 Not Found to mislead the attacker
http_response_code(404);
include '404.php'; // Assuming you have one, or just echo generic 404
if (!file_exists('404.php')) {
    echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>";
}
exit();
?>
