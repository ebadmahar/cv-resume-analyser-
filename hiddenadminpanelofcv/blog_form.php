<?php
require_once '../config/db.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit;
}

$blog = [
    'id' => '', 'title' => '', 'slug' => '', 'short_description' => '',
    'content' => '', 'image_url' => '', 'tags' => '', 'status' => 'draft'
];

$err = '';
$msg = '';

if (isset($_GET['id'])) {
    $q = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
    $q->execute([$_GET['id']]);
    if ($fetched = $q->fetch()) $blog = $fetched;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $slug  = !empty($_POST['slug']) ? trim($_POST['slug']) : strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $desc  = trim($_POST['short_description']);
    $body  = $_POST['content']; 
    $img   = trim($_POST['image_url']); 
    $tags  = trim($_POST['tags']);
    $stats = $_POST['status'];
    $uid   = $_SESSION['user_id'];

    if (isset($_GET['id'])) {
        $q = $pdo->prepare("UPDATE blogs SET title=?, slug=?, short_description=?, content=?, image_url=?, tags=?, status=? WHERE id=?");
        if ($q->execute([$title, $slug, $desc, $body, $img, $tags, $stats, $_GET['id']])) {
            $msg = "Blog updated.";
            $blog = array_merge($blog, $_POST);
        } else {
            $err = "Update failed.";
        }
    } else {
        $q = $pdo->prepare("INSERT INTO blogs (title, slug, short_description, content, image_url, tags, status, author_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($q->execute([$title, $slug, $desc, $body, $img, $tags, $stats, $uid])) {
            header("Location: blogs.php");
            exit;
        } else {
            $err = "Creation failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $blog['id'] ? 'Edit' : 'Create' ?> Blog - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <?php
        $tiny_key = getSetting($pdo, 'tinymce_api_key') ?: 'no-api-key';
    ?>
    <script src="https://cdn.tiny.cloud/1/<?= htmlspecialchars($tiny_key) ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#content',
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 500,
        promotion: false
      });
    </script>
    <style>
        .sidebar { width: 250px; height: 100vh; background: #fff; border-right: 1px solid #e3e6f0; position: fixed; }
        .main-content { margin-left: 250px; padding: 2rem; }
        .form-label { font-weight: 600; font-size: 0.9rem; color: var(--text-color); }
    </style>
</head>
<body>

    <div class="sidebar d-flex flex-column">
        <a href="../index.php" class="d-flex align-items-center justify-content-center py-4 text-decoration-none">
            <h4 class="fw-bold text-primary m-0"><i class="fas fa-robot me-2"></i>Admin</h4>
        </a>
        <nav class="nav flex-column mt-2">
            <a href="index.php" class="nav-link text-secondary px-4 py-3"><i class="fas fa-arrow-left me-2"></i> Back to Dashboard</a>
            <a href="blogs.php" class="nav-link active text-primary px-4 py-3 border-end border-3 border-primary bg-light"><i class="fas fa-newspaper me-2"></i> Manage Blogs</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark"><?= $blog['id'] ? 'Edit Post' : 'Create New Post' ?></h2>
             <a href="blogs.php" class="btn btn-outline-secondary">Cancel</a>
        </div>

        <?php if($err): ?>
            <div class="alert alert-danger"><?= $err ?></div>
        <?php endif; ?>
        <?php if($msg): ?>
            <div class="alert alert-success"><?= $msg ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-8">
                             <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($blog['title']) ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Short Description</label>
                                <textarea name="short_description" class="form-control" rows="3" required><?= htmlspecialchars($blog['short_description']) ?></textarea>
                                <div class="form-text">Shown in the blog list.</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Content</label>
                                <textarea id="content" name="content"><?= htmlspecialchars($blog['content']) ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card bg-light border-0 mb-3">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="draft" <?= $blog['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                                            <option value="published" <?= $blog['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tags</label>
                                        <input type="text" name="tags" class="form-control" value="<?= htmlspecialchars($blog['tags']) ?>" placeholder="e.g. Career, Resume, AI">
                                        <div class="form-text">Comma separated.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Slug (Optional)</label>
                                        <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($blog['slug']) ?>" placeholder="auto-generated-if-empty">
                                    </div>
                                </div>
                            </div>

                            <div class="card bg-light border-0">
                                <div class="card-body">
                                    <label class="form-label">Featured Image URL</label>
                                    <input type="text" name="image_url" class="form-control mb-3" value="<?= htmlspecialchars($blog['image_url']) ?>" placeholder="https://...">
                                    
                                    <?php if($blog['image_url']): ?>
                                        <div class="mt-2">
                                            <label class="form-label small">Preview:</label>
                                            <img src="<?= htmlspecialchars($blog['image_url']) ?>" class="img-fluid rounded border">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-2"></i> Save Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
