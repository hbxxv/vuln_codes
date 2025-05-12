<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!$username || !$password) {
        echo "Username and password are required.";
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        echo "Username already taken.";
    } else {
        $hash = md5($password);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "Registration successful!";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required /><br />
    <input type="password" name="password" placeholder="Password" required /><br />
    <button type="submit">Register</button>
</form>
