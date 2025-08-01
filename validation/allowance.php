<?php
session_start();
include("connect.php"); // adjust path if needed

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];
$amount = $_POST['amount'] ?? null;

if ($amount !== null && is_numeric($amount)) {
    $week_start = date('Y-m-d', strtotime('monday this week'));
    $created_at = date('Y-m-d H:i:s');

    // Check if the user already has an allowance set for this week
    $check_sql = "SELECT allowance_id FROM allowances WHERE user_id = ? AND week_start = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("is", $user_id, $week_start);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        // Update existing allowance
        $update_sql = "UPDATE allowances SET amount = ?, created_at = ? WHERE user_id = ? AND week_start = ?";
        $stmt_update = $conn->prepare($update_sql);
        $stmt_update->bind_param("dsis", $amount, $created_at, $user_id, $week_start);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Insert new allowance
        $insert_sql = "INSERT INTO allowances (user_id, amount, week_start, created_at) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($insert_sql);
        $stmt_insert->bind_param("idss", $user_id, $amount, $week_start, $created_at);
        $stmt_insert->execute();
        $stmt_insert->close();
    }

    $stmt_check->close();
    header("Location: ../wallet.php?set=success");
    exit();
} else {
    echo "Invalid amount.";
}

$conn->close();
?>