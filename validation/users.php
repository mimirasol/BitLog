<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: ../login.php?error=" . urlencode("All fields are required!"));
        exit();
    }

    $stmt = $conn->prepare("SELECT user_id, username, password, role FROM users WHERE username = ?");
    
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_username, $db_password, $db_role);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $db_username;
            $_SESSION["role"] = $db_role;

            if ($db_role === 'admin') {
                header("Location: http://localhost/BitLog/test.html"); 
            } elseif ($db_role === 'user') {
                header("Location: http://localhost/BitLog/dashboard_user.php");
            } else {
                header("Location: ../login.php?error=" . urlencode("Invalid role assigned!"));
            }
            exit();
        } else {
            echo "<script>
                alert('Invalid username or password!');
                window.location.href = '../login.php';
            </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('User not found!');
                window.location.href = '../login.php';
            </script>";
            exit();
    }

    $stmt->close();
}

$conn->close();
?>
