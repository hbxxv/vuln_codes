<?php
session_start();
require 'logindbs.php5';
if (!empty($_POST['username'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    if (strpos( strtolower($username), 'sleep') === false && strpos( strtolower($password), 'sleep') === false && strpos( strtolower($username), 'benchmark') === false && strpos( strtolower($password), 'benchmark') === false) {
        try {
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $stmt = $pdo->query("SELECT * FROM login WHERE username='$username' AND password='$password'");
            $user = $stmt->fetch();
            $count = 0;
            foreach ($user as $value) {
                $count += 1;
            }
            Database::disconnect();
            if ($count > 0) {
                $_SESSION['user_id'] = $user->id;
                header("Location: upload.php");
            } else {
                print("<script>alert('Wrong Username or Password')</script>");
                //print('Wrong Username or Password');
            }
        } catch (PDOException $e) {
            //echo "Error: " . $e->getMessage();
            //echo "An SQL Error occurred!";
        }
    }
}
?>