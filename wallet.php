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

$sql_savings = "SELECT goal_name, target_amount, current_amount 
                FROM savings 
                WHERE user_id = $user_id 
                ORDER BY created_at DESC 
                LIMIT 1";
$result_savings = mysqli_query($conn, $sql_savings);
$row_savings = mysqli_fetch_assoc($result_savings);

$goal_name = $row_savings['goal_name'] ?? 'No Goal';
$target_amount = $row_savings['target_amount'] ?? 0;
$current_amount = $row_savings['current_amount'] ?? 0;
?>

<script>
function openModal() {
    document.getElementById("modalOverlay").style.display = "flex";
}

function closeModal() {
    document.getElementById("modalOverlay").style.display = "none";
}
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet</title>
    <link rel="stylesheet" href="css/wallet.css">
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

        <div class="backgrounding">
            <div class="wrapper">
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

                <div class="savings">
                    <span class="savingstext" style="font-size: 3vw;">
                        SAVINGS<br>
                        <span class="goal">Goal: <?php echo htmlspecialchars($goal_name); ?></span><br>
                        <br>
                        <span class="progress">P <?php echo number_format($current_amount, 2); ?> / <?php echo number_format($target_amount, 2); ?></span>
                    </span>
                </div>
                
                <div class="set">
                <a href="signup.php" class="addButton">
                    <div class="buttonWrapper">
                    <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                    <span class="buttonText" style="font-size: 1.05vw;">set</span>
                    </div>
                </a>
                </div>

                <div class="add">
                    <a class="addButton" onclick="openModal()" style="cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">add</span>
                        </div>
                    </a>
                </div>
                
                <div class="edit">
                <a href="signup.php" class="addButton">
                    <div class="buttonWrapper">
                    <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                    <span class="buttonText" style="font-size: 1.05vw;">edit</span>
                    </div>
                </a>
                </div>

                <div class="log">
                <a href="signup.php" class="addButton">
                    <div class="buttonWrapper">
                    <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                    <span class="buttonText" style="font-size: 1.05vw;">log</span>
                    </div>
                </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Overlay and Box -->
            <div id="modalOverlay" class="modal-overlay">
                <div class="closeButton" onclick="closeModal()">
                    X
                </div>
                <div class="inputbox">
                    <div class="boxwrapper">
                        <img src="assets/box.PNG" alt="input box" class="boxIcon">
                        <span class="inputHeader" style="font-size: 3vw;">EXPENSES</span>

                        <form action="validation/expense.php" method="POST" class="inputForm">

                            <label for="category">Category</label>
                            <select id="category" name="category" required>
                                <option value="" disabled selected>Select category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label for="amount">Expense Amount</label>
                            <input type="number" id="amount" name="amount" required placeholder="Enter amount" min="0" step="0.01">

                            <button type="submit" class="addButton" style="background: none; border: none; cursor: pointer;">
                                <div class="buttonWrapper">
                                    <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                                    <span class="buttonText" style="font-size: 1.05vw;">add</span>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
</body>
</html>