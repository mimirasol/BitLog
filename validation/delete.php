<?php
session_start();
include("connect.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    // Optional: Delete related expenses first if needed to maintain referential integrity
    $sql_delete_expenses = "DELETE FROM expenses WHERE user_id = ? AND item_id = ?";
    $stmt_exp = $conn->prepare($sql_delete_expenses);
    $stmt_exp->bind_param("ii", $user_id, $item_id);
    $stmt_exp->execute();
    $stmt_exp->close();

    // Now delete from categories
    $sql = "DELETE FROM categories WHERE item_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $item_id, $user_id);

    if ($stmt->execute()) {
        header("Location: ../wallet.php?message=category_deleted");
        exit();
    } else {
        echo "Error deleting item: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
