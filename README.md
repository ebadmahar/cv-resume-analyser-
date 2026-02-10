# CV Analyzer & Blog System

A comprehensive web application for analyzing CVs using AI (Google Gemini) and managing a professional blog. Built with Core PHP, MySQL, and Bootstrap 5.

## Features

### 🚀 CV Analysis
- **AI-Powered Feedback**: Upload your CV (PDF) and get instant, detailed feedback using Google Gemini AI.
- **Scoring System**: Get a score based on industry standards.
- **Improvement Tips**: Actionable advice to enhance your resume.

### 📝 Blog Management System
- **Admin Panel**: 
  - Create, Edit, and Delete blog posts.
  - **Rich Text Editor**: Integrated TinyMCE editor for formatting content.
  - Manage post status (Draft/Published).
  - Dynamic API configuration (Gemini & TinyMCE keys).
- **Public Blog**: 
  - Responsive grid layout for blog posts.
  - Detailed single post pages with "Stick-to-bottom" footer.
  - SEO-friendly URL slugs.

### 🛡️ Admin Dashboard
- **User Management**: View and manage registered users.
- **Settings**: Configure API keys directly from the dashboard without touching code.
- **Secure Authentication**: Robust login and registration system with role-based access control.

## Installation

1.  **Clone the Repository**
    ```sh
    git clone https://github.com/yourusername/cv-analyzer.git
    cd cv-analyzer
    ```

2.  **Database Setup**
    - Create a MySQL database named `cv`.
    - Import the schema:
      ```sh
      mysql -u root -p cv < database/schema.sql
      ```
    - (Optional) Seed sample blog data:
      ```sh
      mysql -u root -p cv < database/seed_blogs.sql
      ```

3.  **Configuration**
    - Open `config/db.php` and verify your database credentials:
      ```php
      define('DB_HOST', 'localhost');
      define('DB_NAME', 'cv');
      define('DB_USER', 'root');
      define('DB_PASS', ''); 
      ```

4.  **Admin Setup**
    - Register a new account on the public site (`/auth/register.php`).
    - Go to your database user table and manually change the `role` of your user to `admin`.
      ```sql
      UPDATE users SET role='admin' WHERE email='your@email.com';
      ```

5.  **API Keys**
    - Log in as your new Admin.
    - Navigate to **Admin Panel > Settings**.
    - Enter your **Google Gemini API Key** (for CV Analysis).
    - Enter your **TinyMCE API Key** (for the Blog Editor). Get one for free at [tiny.cloud](https://www.tiny.cloud/).

## Usage

- **Public Site**: Users can register, upload CVs for analysis, and read blog posts.
- **Admin Panel**: Accessible via the "Dashboard" link for admin users. Use it to manage content and system settings.

## Folder Structure

```
/
├── assets/                 # CSS, JS, and Images
├── auth/                   # Login & Register scripts
├── config/                 # Database configuration
├── dashboard/              # User dashboard
├── database/               # SQL schema and seeds
├── hiddenadminpanelofcv/   # Admin Control Panel
├── includes/               # Helper functions
├── uploads/                # User uploaded CVs
├── index.php               # Landing Page
├── blogs.php               # Public Blog List
├── blog_details.php        # Single Blog Post
└── README.md               # Project Documentation
```

## License

This project is open-source and available under the [MIT License](LICENSE).
