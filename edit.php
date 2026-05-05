<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Load note — must belong to the logged-in user
$stmt = $conn->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$note = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$note) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']);
    $filePath = $note['file'];

    if (!$title) {
        $error = 'Title cannot be empty.';
    } else {
        // Replace file only if a new one was uploaded
        if (!empty($_FILES['file']['name']) && $_FILES['file']['size'] > 0) {
            $fileName   = basename($_FILES['file']['name']);
            $newName    = time() . "_" . $fileName;
            $uploadPath = "uploads/" . $newName;

            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                if ($filePath && file_exists($filePath)) {
                    unlink($filePath);
                }
                $filePath = $uploadPath;
            } else {
                $error = 'File upload failed. Title was not saved.';
            }
        }

        if (!$error) {
            $upd = $conn->prepare("UPDATE notes SET title = ?, file = ? WHERE id = ? AND user_id = ?");
            $upd->bind_param("ssii", $title, $filePath, $id, $user_id);
            $upd->execute();
            $upd->close();
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Note – NoteVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">

    <nav class="navbar navbar-app">
        <div class="nav-brand">📝 NoteVault</div>
        <div class="nav-links">
            <a href="index.php" class="btn btn-outline btn-sm">← Back to Notes</a>
        </div>
    </nav>

    <div class="app-container" style="max-width:540px;">

        <div class="app-header">
            <h2>Edit Note</h2>
            <p>Update the title or replace the attached file.</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST" enctype="multipart/form-data" class="edit-form">

                <label>Note Title</label>
                <input type="text" name="title"
                       value="<?= htmlspecialchars($note['title']) ?>" required>

                <label>Replace File <span class="label-hint">(leave blank to keep current)</span></label>
                <div class="current-file">
                    📎 Current:
                    <a href="<?= htmlspecialchars($note['file']) ?>" target="_blank">
                        <?= htmlspecialchars(basename($note['file'])) ?>
                    </a>
                </div>
                <input type="file" name="file">

                <div class="edit-actions">
                    <a href="index.php" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </form>
        </div>

    </div>

    <script src="validation.js"></script>
</body>
</html>
