<?php
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!$username || !$password) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['username']  = $row['username'];
                header("Location: index.php");
                exit;
            }
        }
        $error = 'Invalid username / email or password.';
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – NoteVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <a href="landing.php" class="auth-logo">📝 NoteVault</a>
        <h2>Welcome back!</h2>
        <p class="auth-sub">Log in to access your notes.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label>Username or Email</label>
            <input type="text" name="username" placeholder="Enter username or email" required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

            <label>Password</label>
            <input type="password" name="password" placeholder="Your password" required>

            <button type="submit" class="btn btn-primary btn-full">Log In</button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="register.php">Sign up free</a></p>
    </div>

    <script src="validation.js"></script>
</body>
</html>
