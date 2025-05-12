<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $db = new SQLite3('database.db');

    $username = $_GET['username'];
    $file = basename($_GET['file']);

    $allowed_extensions = ["pdf", "doc", "docx", "xls", "xlsx", "odt"];
    $file_extension = pathinfo($file, PATHINFO_EXTENSION);

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "<div class='error'>Invalid file extension.</div>";
        exit();
    }

    $stmt = $db->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($row = $result->fetchArray()) {
        $user_id = $row['id'];

        $stmt = $db->prepare('SELECT * FROM uploads WHERE user_id = :user_id AND file_name = :file');
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $stmt->bindValue(':file', $file, SQLITE3_TEXT);
        $result = $stmt->execute();

        if ($row = $result->fetchArray()) {
            $file_path = 'uploads/' . $file;

            if (file_exists($file_path)) {
                ob_clean();
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                readfile($file_path);
                exit();
            } else {
                echo "<div class='error'>File not found on the server.</div>";
                showAvailableFiles($user_id, $db);
            }
        } else {
            echo "<div class='error'>File does not exist.</div>";
            showAvailableFiles($user_id, $db);
        }
    } else {
        echo "<div class='error'>User not found.</div>";
    }

    function showAvailableFiles($user_id, $db) {
        $stmt = $db->prepare('SELECT file_name FROM uploads WHERE user_id = :user_id');
        $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
        $result = $stmt->execute();

        echo "<h2>Available files for download:</h2>";
        echo "<ul>";

        while ($row = $result->fetchArray()) {
            $file_name = $row['file_name'];
            echo '<li><a href="view.php?username=' . urlencode($_GET['username']) . '&file=' . urlencode($file_name) . '">' . htmlspecialchars($file_name) . '</a></li>';
        }

        echo "</ul>";
    }
 ?>