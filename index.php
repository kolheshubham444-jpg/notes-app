<?php
$conn = new mysqli("localhost", "root", "Root@123", "notes_app");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notes App new Update</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">

<h2>Notes App with File Upload</h2>

<form action="upload.php" method="POST" enctype="multipart/form-data">
    
    <input type="text" name="title" placeholder="Enter note" required>
    
    <input type="file" name="file" required>
    
    <button type="submit">Add Note</button>

</form>

<hr>

<h3>All Notes</h3>

<?php
$result = $conn->query("SELECT * FROM notes ORDER BY id DESC");

while($row = $result->fetch_assoc()){
?>
    <div class="note">
        <h4><?php echo $row['title']; ?></h4>

        <a href="<?php echo $row['file']; ?>" target="_blank">
            View File
        </a>
    </div>
<?php
}
?>

</div>

</body>
</html>