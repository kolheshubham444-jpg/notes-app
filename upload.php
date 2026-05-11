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

    $phpError = $_FILES['file']['error'];
    if ($phpError !== UPLOAD_ERR_OK) {
        $msgs = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server upload limit (2 MB).',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was selected.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'Upload blocked by a PHP extension.',
        ];
        $msg = $msgs[$phpError] ?? "Upload error code: $phpError";
        die('<p style="color:red">' . htmlspecialchars($msg) . ' <a href="javascript:history.back()">Go back</a></p>');
    }

    if (move_uploaded_file($tmpName, $uploadPath)) {
        $stmt = $conn->prepare("INSERT INTO notes (title, file, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $uploadPath, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
    } else {
        die('<p style="color:red">File upload failed — could not move file to uploads directory. <a href="javascript:history.back()">Go back</a></p>');
    }
}
?>
