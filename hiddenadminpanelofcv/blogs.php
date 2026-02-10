<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit();
}

// Handle Delete
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $success = "Blog post deleted successfully.";
}

// Fetch Blogs
$stmt = $pdo->query("SELECT * FROM blogs ORDER BY created_at DESC");
$blogs = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Blogs - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #fff;
            border-right: 1px solid #e3e6f0;
            position: fixed;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        .nav-link {
            color: #5a5c69;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--primary-color);
            background: #f8f9fc;
            border-right: 3px solid var(--primary-color);
        }
        .nav-link i { width: 25px; }
        .blog-img-thumb {
            width: 60px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column">
        <a href="../index.php" class="d-flex align-items-center justify-content-center py-4 text-decoration-none">
            <h4 class="fw-bold text-primary m-0"><i class="fas fa-robot me-2"></i>Admin</h4>
        </a>
        <nav class="nav flex-column mt-2">
            <a href="index.php" class="nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="users.php" class="nav-link"><i class="fas fa-users"></i> Users</a>
            <a href="blogs.php" class="nav-link active"><i class="fas fa-newspaper"></i> Blogs</a>
            <a href="settings.php" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
            <a href="../auth/logout.php" class="nav-link mt-auto"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark">Manage Blogs</h2>
            <a href="blog_form.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Create New Post</a>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Title</th>
                                <th>Status</th>
                                <th>Author</th>
                                <th>Created</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($blogs as $blog): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php if($blog['image_url']): ?>
                                            <img src="<?= htmlspecialchars($blog['image_url']) ?>" class="blog-img-thumb me-3">
                                        <?php else: ?>
                                            <div class="blog-img-thumb me-3 bg-light d-flex align-items-center justify-content-center text-muted">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($blog['title']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars(substr($blog['short_description'], 0, 50)) ?>...</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if($blog['status'] === 'published'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success">Published</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td>Admin</td> <!-- Simplified for now -->
                                <td><?= date('M d, Y', strtotime($blog['created_at'])) ?></td>
                                <td class="text-end pe-4">
                                    <a href="blog_form.php?id=<?= $blog['id'] ?>" class="btn btn-sm btn-outline-primary me-2"><i class="fas fa-edit"></i></a>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                        <input type="hidden" name="delete_id" value="<?= $blog['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($blogs)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No blog posts found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
