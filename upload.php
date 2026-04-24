<?php

$conn = new mysqli("localhost", "root", "Root@123", "notes_app");

if(isset($_POST['title']) && isset($_FILES['file'])){

    $title = $_POST['title'];

    $fileName = $_FILES['file']['name'];
    $tmpName  = $_FILES['file']['tmp_name'];

    // unique name (important)
    $newName = time() . "_" . $fileName;

    $uploadPath = "uploads/" . $newName;

    // upload file
    if(move_uploaded_file($tmpName, $uploadPath)){

        // save in DB
        $conn->query("INSERT INTO notes (title, file) VALUES ('$title', '$uploadPath')");

        header("Location: index.php");

    } else {
        echo "File upload failed!";
    }
}
?>