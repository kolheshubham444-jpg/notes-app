<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['title']) && isset($_FILES['file'])) {
    $user_id  = $_SESSION['user_id'];
    $title    = trim($_POST['title']);
    $fileName = basename($_FILES['file']['name']);
    $tmpName  = $_FILES['file']['tmp_name'];
    $newName  = time() . "_" . $fileName;
    $uploadPath = "uploads/" . $newName;

    if (move_uploaded_file($tmpName, $uploadPath)) {
        $stmt = $conn->prepare("INSERT INTO notes (title, file, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $uploadPath, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
    } else {
        echo "File upload failed!";
    }
}
?>
