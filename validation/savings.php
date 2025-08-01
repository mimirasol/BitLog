<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$goal_name = trim($_POST['goal_name'] ?? '');
$target_amount = $_POST['target_amount'] ?? null;
$created_at = date('Y-m-d H:i:s');

if ($goal_name !== '' && is_numeric($target_amount)) {
    $check_sql = "SELECT goal_id FROM savings WHERE user_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $update_sql = "UPDATE savings SET goal_name = ?, target_amount = ?, current_amount = 0, created_at = ? WHERE user_id = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("sdsi", $goal_name, $target_amount, $created_at, $user_id);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        $insert_sql = "INSERT INTO savings (user_id, goal_name, target_amount, current_amount, created_at)
                       VALUES (?, ?, ?, 0, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("isds", $user_id, $goal_name, $target_amount, $created_at);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    $stmt_check->close();
    header("Location: ../wallet.php?edit=success");
    exit();
} else {
    echo "Invalid input. Please provide both goal name and target amount.";
}

$conn->close();
?>
