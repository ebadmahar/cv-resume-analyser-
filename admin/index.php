<?php
require_once '../config/db.php';

// Log the attempt
$ip = $_SERVER['REMOTE_ADDR'];
$url = $_SERVER['REQUEST_URI'];
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

try {
    $stmt = $pdo->prepare("INSERT INTO security_logs (ip_address, attempted_url, user_agent) VALUES (?, ?, ?)");
    $stmt->execute([$ip, $url, $userAgent]);
} catch (Exception $e) {
    // Silent fail
}

http_response_code(404);
echo "<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>Not Found</h1><p>The requested URL was not found on this server.</p></body></html>";
exit();
?>
