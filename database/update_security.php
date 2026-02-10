<?php
require_once __DIR__ . '/../config/db.php';

try {
    // Security Logs Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS security_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45) NOT NULL,
        attempted_url VARCHAR(255) NOT NULL,
        user_agent VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'security_logs' created successfully.<br>";

    // Ensure 'settings' table exists (it should, but just in case)
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(50) PRIMARY KEY,
        setting_value TEXT
    )");
    echo "Table 'settings' checked.<br>";
    
    // Add maintenance mode setting default if not exists
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('maintenance_mode', '0')");
    $stmt->execute();
    echo "Maintenance mode setting initialized.<br>";

} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}
?>
