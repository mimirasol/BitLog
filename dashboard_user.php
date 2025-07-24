<?php
session_start();
require "validation/connect.php";

$user_id = $_SESSION["user_id"] ?? null;
$username = $_SESSION["username"] ?? null;

$sql_expenses = "SELECT SUM(amount) AS total_expense FROM expenses WHERE user_id = $user_id";
$result_expenses = mysqli_query($conn, $sql_expenses);
$row_expenses = mysqli_fetch_assoc($result_expenses);
$total_expense = $row_expenses['total_expense'] ?? 0;

$sql_allowance = "SELECT amount AS allowance FROM allowances WHERE user_id = $user_id";
$result_allowance = mysqli_query($conn, $sql_allowance);
$row_allowance = mysqli_fetch_assoc($result_allowance);
$allowance = $row_allowance['allowance'] ?? 0;

$budget = $allowance - $total_expense;

$sql = "SELECT c.name AS category, 
        IFNULL(SUM(e.amount), 0) AS total
        FROM categories c
        LEFT JOIN expenses e ON c.category_id = e.category_id AND e.user_id = $user_id
        WHERE c.user_id = $user_id
        GROUP BY c.name";
$result = mysqli_query($conn, $sql);

$categories = [];
$totals = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = htmlspecialchars($row['category']);
        $totals[] = number_format($row['total'], 2);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
</head>
<body>
    <div class="dashboardpage">
        <div class="sidebar">
            <div class="icons">
                <img src="assets/logo.png" alt="logo" class="logo" style="width: 4vw;">

                <a href="dashboard_user.php">
                    <img src="assets/homeicon.png" alt="home" class="home">
                </a>

                <a href="wallet.php">
                    <img src="assets/walleticon.png" alt="wallet" class="wallet" style="width: 3vw;">
                </a>

                <a href="logout.php">
                    <img src="assets/logouticon.png" alt="logout" class="logout" style="width: 3vw; margin-top: 42vh;">
                </a>
            </div>
        </div>

        <div class="backgroundimg">
            <div class="wrapper">
                <div class="character">
                    <div class="characterwrapper">
                        <img src="assets/characbg.png" alt="background" class="characbg">
                        
                        <img src="assets/frog.png" alt="frog" class="frog">

                        <span class="welcometext" style="font-size: 3vw;">
                            Welcome,<br>
                            <span class="username" style="color: #54138d;"><?php echo $username; ?>!</span>
                        </span>
                    </div>

                </div>

                <div class="overview">
                <div class="label-row">
                    <ul class="list">
                        <li>Allowance</li>
                        <li>Budget</li>
                        <li>Expenses</li>
                    </ul>
                </div>

                <div class="value-row">
                    <ul class="listAmount">
                        <li><?php echo $allowance; ?></li>
                        <li><?php echo number_format($budget, 2, '.', ''); ?></li>
                        <li><?php echo $total_expense; ?></li>
                    </ul>
                </div>
            </div>

                <div class="expenses">
                <ul class="categoryExpenseList">
                    <?php 
                    if (!empty($categories) && !empty($totals)) {
                        for ($i = 0; $i < count($categories); $i++) {
                            echo "<li><span class='catname'>{$categories[$i]}</span>
                            <span class='catamount'>P {$totals[$i]}</span></li>";
                        }
                    } else {
                        echo "<li>No expenses</li>";
                    }
                    ?>
                </ul>
            </div>
            </div>
        </div>

    </div>
</body>
</html>