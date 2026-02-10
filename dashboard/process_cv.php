<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/pdf_helper.php';
require_once '../api/gemini.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $file = $_FILES['cv_file'];
    
    // Validation
    if ($file['type'] !== 'application/pdf') {
        header("Location: index.php?error=" . urlencode("Only PDF files are allowed."));
        exit();
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB
        header("Location: index.php?error=" . urlencode("File too large. Max 5MB."));
        exit();
    }

    // Extract Text
    $cvText = PdfToText::extract($file['tmp_name']);
    
    if (strlen($cvText) < 50) {
        header("Location: index.php?error=" . urlencode("Could not extract text. PDF might be image-based."));
        exit();
    }

    // Hash check
    $cvHash = hash('sha256', $cvText);
    
    // Check if exists
    $stmt = $pdo->prepare("SELECT * FROM cv_analyses WHERE user_id = ? AND cv_text_hash = ?");
    $stmt->execute([$_SESSION['user_id'], $cvHash]);
    $existing = $stmt->fetch();

    if ($existing) {
        header("Location: index.php?warning=" . urlencode("You have already analyzed this CV!"));
        exit();
    }

    // Get API Key
    $apiKey = getSetting($pdo, 'gemini_api_key');
    if (!$apiKey) {
        header("Location: index.php?error=" . urlencode("System Error: API Key not configured."));
        exit();
    }

    // Call Gemini
    $gemini = new GeminiAPI($apiKey);
    $analysis = $gemini->analyzeCV($cvText);

    if (isset($analysis['error'])) {
        header("Location: index.php?error=" . urlencode("AI Analysis Failed: " . $analysis['error']));
        exit();
    }

    // Save Results
    $stmt = $pdo->prepare("INSERT INTO cv_analyses (user_id, cv_text_hash, cv_text, score, strengths, missing_skills, weaknesses, roadmap, suggestions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $cvHash,
        $cvText,
        $analysis['score'] ?? 0,
        json_encode($analysis['strengths'] ?? []),
        json_encode($analysis['missing_skills'] ?? []),
        json_encode($analysis['weaknesses'] ?? []),
        json_encode($analysis['roadmap'] ?? []),
        json_encode($analysis['suggestions'] ?? [])
    ]);

    header("Location: index.php");
    exit();
}
?>
