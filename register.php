<?php
require 'db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username      = trim($_POST['username']);
    $email         = trim($_POST['email']);
    $password      = $_POST['password'];
    $confirm       = $_POST['confirm'];
    // captcha validated via session word set by captcha.php

    if (!$username || !$email || !$password || !$confirm) {
        $error = 'All fields are required.';
    } elseif (strtoupper(trim($_POST['captcha'] ?? '')) !== ($_SESSION['captcha_word'] ?? '')) {
        $error = 'Incorrect CAPTCHA. Please try again.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $ins->bind_param("sss", $username, $email, $hash);
            $ins->execute();

            $success = 'Account created! <a href="login.php">Log in now →</a>';
        }
        $stmt->close();
    }
}

// captcha image is generated on-demand by captcha.php (sets $_SESSION['captcha_word'])
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – NoteVault</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">

    <div class="auth-card">
        <a href="landing.php" class="auth-logo">📝 NoteVault</a>
        <h2>Create your account</h2>
        <p class="auth-sub">Free forever. No credit card needed.</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <label>Username</label>
            <input type="text" name="username" placeholder="e.g. john_doe" required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">

            <label>Email</label>
            <input type="email" name="email" placeholder="you@example.com" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label>Password</label>
            <input type="password" name="password" placeholder="Min 6 characters" required>

            <label>Confirm Password</label>
            <input type="password" name="confirm" placeholder="Repeat password" required>

            <label>CAPTCHA – type the characters you see</label>
            <div class="captcha-wrap">
                <img src="captcha.php" alt="CAPTCHA image" id="captchaImg" class="captcha-img">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('captchaImg').src='captcha.php?r='+Math.random()">&#8635; Refresh</button>
            </div>
            <input type="text" name="captcha" placeholder="Enter characters above" required autocomplete="off" maxlength="6">

            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>

        <p class="auth-footer">Already have an account? <a href="login.php">Log in</a></p>
    </div>

    <script src="validation.js"></script>
</body>
</html>
