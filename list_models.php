<?php
require_once 'config/db.php';
require_once 'includes/functions.php';

$apiKey = getSetting($pdo, 'gemini_api_key');
$url = "https://generativelanguage.googleapis.com/v1beta/models?key=$apiKey";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

echo "<h1>Available Models</h1>";
if (isset($data['models'])) {
    echo "<ul>";
    foreach ($data['models'] as $model) {
        if (strpos($model['name'], 'generateContent') !== false || true) { // List all to be sure
            echo "<li><strong>" . $model['name'] . "</strong><br>" . $model['description'] . "</li><br>";
        }
    }
    echo "</ul>";
} else {
    echo "Error fetching models: <pre>" . print_r($data, true) . "</pre>";
}
?>
