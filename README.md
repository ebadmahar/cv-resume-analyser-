# AI CV Analyzer

A strict, core-PHP application for analyzing CVs using Google Gemini API.

## Installation

1.  **Database**:
    *   Create a database named `ai_cv_analyzer`.
    *   Import `database/schema.sql`.

2.  **Configuration**:
    *   Edit `config/db.php` to match your database credentials.

3.  **Setup**:
    *   The project expects to be in `c:/xampp/htdocs/CV`.
    *   Access via `http://localhost/CV`.

4.  **Admin Panel**:
    *   URL: `/hiddenadminpanelofcv/`
    *   To create an admin: Register a normal user, then manually update the `role` to `'admin'` in the `users` table in your database.
    *   **Login to Admin Panel** -> Enter your Gemini API Key. This is required for the app to work.

## Usage

1.  Register/Login.
2.  Upload a PDF CV.
3.  View Analysis (Score, Strengths, Weaknesses, Roadmap).
4.  View History.

## Features

*   **Core PHP**: No frameworks specific dependencies.
*   **Smart Cache**: Hashes PDF content to avoid redundant API calls.
*   **Security**: PDF validation, Password Hashing, Prepared Statements.
