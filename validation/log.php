<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$log_amount = floatval($_POST['current_amount']);

$query = $conn->prepare("SELECT goal_id, current_amount FROM savings WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("No savings goal found.");
}

$row = $result->fetch_assoc();
$new_amount = $row['current_amount'] + $log_amount;

$update = $conn->prepare("UPDATE savings SET current_amount = ? WHERE goal_id = ?");
$update->bind_param("di", $new_amount, $row['goal_id']);

if ($update->execute()) {
    header("Location: ../wallet.php?message=log_success");
    exit();
} else {
    echo "Failed to log savings: " . $update->error;
}

$query->close();
$update->close();
$conn->close();
?>
