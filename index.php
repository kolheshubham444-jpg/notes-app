<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Notes – NoteVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">

    <!-- App Navbar -->
    <nav class="navbar navbar-app">
        <div class="nav-brand">📝 NoteVault</div>
        <div class="nav-links">
            <span class="nav-user">👤 <?= htmlspecialchars($username) ?></span>
            <a href="logout.php" class="btn btn-outline btn-sm">Logout</a>
        </div>
    </nav>

    <div class="app-container">

        <div class="app-header">
            <h2>My Notes</h2>
            <p>Hello, <strong><?= htmlspecialchars($username) ?></strong>! Here are all your notes.</p>
        </div>

        <!-- Add Note Form -->
        <div class="form-card">
            <h3>Add New Note</h3>
            <form action="upload.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Note title..." required>
                <div class="file-row">
                    <input type="file" name="file" required>
                    <button type="submit" class="btn btn-primary">Add Note</button>
                </div>
            </form>
        </div>

        <hr class="divider">

        <!-- Notes List -->
        <div class="notes-grid">
        <?php
        $stmt = $conn->prepare("SELECT * FROM notes WHERE user_id = ? ORDER BY id DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0):
        ?>
            <div class="empty-state">
                <p>✏️ No notes yet. Add your first note above!</p>
            </div>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="note-card">
                    <h4><?= htmlspecialchars($row['title']) ?></h4>
                    <div class="note-actions">
                        <a href="<?= htmlspecialchars($row['file']) ?>" target="_blank" class="btn btn-outline btn-sm">
                            📎 View
                        </a>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">
                            ✏️ Edit
                        </a>
                        <form method="POST" action="delete.php"
                              onsubmit="return confirm('Delete this note? This cannot be undone.')">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">🗑️ Delete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
        </div>

    </div>

    <script src="validation.js"></script>
</body>
</html>
