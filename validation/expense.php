<?php
session_start();
include 'connect.php';

if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id'];
$item_name = trim($_POST['item_name']);
$amount = floatval($_POST['amount']);

$sql_check_item = "SELECT item_id FROM categories WHERE name = ? AND user_id = ?";
$stmt = $conn->prepare($sql_check_item);
$stmt->bind_param("si", $item_name, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $item_id = $row['item_id'];
} else {
    $sql_insert_item = "INSERT INTO categories (user_id, name) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert_item);
    $stmt_insert->bind_param("is", $user_id, $item_name);
    $stmt_insert->execute();
    $item_id = $stmt_insert->insert_id;
    $stmt_insert->close();
}

$sql_expense = "INSERT INTO expenses (user_id, item_id, amount) VALUES (?, ?, ?)";
$stmt_expense = $conn->prepare($sql_expense);
$stmt_expense->bind_param("iid", $user_id, $item_id, $amount);

if ($stmt_expense->execute()) {
    header("Location: ../wallet.php?message=expense_added");
    exit();
} else {
    echo "Error inserting expense: " . $stmt_expense->error;
}

$stmt_expense->close();
$stmt->close();
$conn->close();
?>
