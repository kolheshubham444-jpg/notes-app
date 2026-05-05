<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id      = (int)$_POST['id'];
    $user_id = (int)$_SESSION['user_id'];

    // Fetch the file path (only if this note belongs to the logged-in user)
    $stmt = $conn->prepare("SELECT file FROM notes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($row['file'] && file_exists($row['file'])) {
            unlink($row['file']);
        }

        $del = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
        $del->bind_param("ii", $id, $user_id);
        $del->execute();
        $del->close();
    }

    $stmt->close();
}

header("Location: index.php");
exit;
?>
