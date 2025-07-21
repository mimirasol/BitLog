<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        die("Error: All fields are required.");
    }

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        echo "Username or email already exists.";
        $stmt->close();
        exit();
    }
    $stmt->close();

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        die("Error preparing insert statement: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $email, $password_hash);

    if ($stmt->execute()) {
        echo "<script>
                alert('Account successfully created! Redirecting to log in...');
                window.location.href = '../login.php';
            </script>";
            exit();
    } else {
        echo "Error inserting user: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
