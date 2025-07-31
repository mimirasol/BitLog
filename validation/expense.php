<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Sanitize inputs
$category_name = isset($_POST['category']) ? trim($_POST['category']) : '';
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$date = date('Y-m-d');

if (empty($category_name) || $amount <= 0) {
    header("Location: ../wallet.php?error=Invalid+input");
    exit();
}

// Step 1: Get category_id based on category name and user
$sql = "SELECT category_id FROM categories WHERE name = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "si", $category_name, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $category_id = $row['category_id'];

    // Step 2: Insert into expenses
    $insert_sql = "INSERT INTO expenses (user_id, category_id, amount, description, date) 
                   VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "iiiss", $user_id, $category_id, $amount, $description, $date);

    if (mysqli_stmt_execute($insert_stmt)) {
        header("Location: ../wallet.php?success=1");
        exit();
    } else {
        header("Location: ../wallet.php?error=Failed+to+add+expense");
        exit();
    }
} else {
    header("Location: ../wallet.php?error=Category+not+found");
    exit();
}
?>
