<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
require_once '../includes/pdf_helper.php';
require_once '../api/gemini.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv_file'])) {
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    
    function sendResponse($status, $message, $isAjax) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => $status, 'message' => $message]);
            exit();
        } else {
            if ($status === 'success') {
                header("Location: index.php");
            } else {
                header("Location: index.php?error=" . urlencode($message));
            }
            exit();
        }
    }

    try {
        $file = $_FILES['cv_file'];
        
        // Validation
        if ($file['type'] !== 'application/pdf') {
            sendResponse('error', "Only PDF files are allowed.", $isAjax);
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            sendResponse('error', "File too large. Max 5MB.", $isAjax);
        }

        // Extract Text
        try {
            $cvText = PdfToText::extract($file['tmp_name']);
        } catch (Exception $e) {
            // Log real error internally if needed
            sendResponse('error', "Failed to read PDF file.", $isAjax);
        }
        
        if (strlen($cvText) < 50) {
            sendResponse('error', "Could not extract text. PDF might be image-based or empty.", $isAjax);
        }

        // Hash check
        $cvHash = hash('sha256', $cvText);
        $stmt = $pdo->prepare("SELECT * FROM cv_analyses WHERE user_id = ? AND cv_text_hash = ?");
        $stmt->execute([$_SESSION['user_id'], $cvHash]);
        if ($stmt->fetch()) {
             // For AJAX, maybe treat this as error or warning.
             // User asked "don't show api error say something wents wrong" but this is a logic check.
             // I'll return it as a warning message.
             sendResponse('error', "You have already analyzed this CV!", $isAjax);
        }

        // Get API Key
        $apiKey = getSetting($pdo, 'gemini_api_key');
        if (!$apiKey) {
            sendResponse('error', "System configuration error. Please contact admin.", $isAjax);
        }

        // Call Gemini
        $gemini = new GeminiAPI($apiKey);
        $analysis = $gemini->analyzeCV($cvText);

        if (isset($analysis['error'])) {
            // User request: "when there is api 503 error don't show api error say something wents wrong"
            // We'll log the specific error for debugging but show a generic one.
            error_log("Gemini API Error: " . $analysis['error']);
            sendResponse('error', "Something went wrong while analyzing your CV. Please try again later.", $isAjax);
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

        sendResponse('success', "Analysis complete!", $isAjax);

    } catch (Exception $e) {
        error_log("Process CV Exception: " . $e->getMessage());
        sendResponse('error', "Something went wrong. Please try again.", $isAjax);
    }
}
?>
