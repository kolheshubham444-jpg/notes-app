<?php
session_start();

// Read from environment variables (Docker) → fall back to local defaults
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: 'root';
$name = getenv('DB_NAME') ?: 'notes_app';

$conn = new mysqli($host, $user, $pass, $name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$col = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = '$name' AND TABLE_NAME = 'notes' AND COLUMN_NAME = 'user_id'");
if ($col->num_rows === 0) {
    $conn->query("ALTER TABLE notes ADD COLUMN user_id INT DEFAULT NULL");
}
?>
