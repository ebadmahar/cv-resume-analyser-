<?php
http_response_code(503);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Site Maintenance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: 'Outfit', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .maintenance-card {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <div class="mb-4 text-warning">
            <i class="fas fa-tools fa-4x"></i>
        </div>
        <h1 class="fw-bold mb-3">Under Maintenance</h1>
        <p class="text-secondary mb-4">
            We are currently upgrading our system to provide you with a better experience. 
            Please check back shortly.
        </p>
        <button class="btn btn-primary px-4 py-2" onclick="window.location.reload()">
            <i class="fas fa-sync-alt me-2"></i> Check Again
        </button>
    </div>
</body>
</html>
