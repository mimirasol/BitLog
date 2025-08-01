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
        LEFT JOIN expenses e ON c.item_id = e.item_id AND e.user_id = $user_id
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
    document.addEventListener('DOMContentLoaded', function () {
        const modalTypes = ['add', 'set', 'edit', 'log', 'delete'];

        modalTypes.forEach(type => {
            const trigger = document.querySelector(`.${type}`);
            const modal = document.getElementById(`${type}ModalOverlay`);

            if (trigger && modal) {
                trigger.addEventListener('click', () => {
                    modal.style.display = 'flex';
                });
            }

            const closeButton = modal?.querySelector('.closeButton');
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    modal.style.display = 'none';
                });
            }
        });
    });
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
                <div class="content-sections">
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
                </div>
                
                <div class="set">
                    <a class="button" style="cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">set</span>
                        </div>
                    </a>
                </div>

                <div class="add">
                    <a class="button" style="cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">add</span>
                        </div>
                    </a>
                </div>

                <div class="delete">
                    <a class="button" style="cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">delete</span>
                        </div>
                    </a>
                </div>
                
                <div class="edit">
                    <a class="button" style="cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">edit</span>
                        </div>
                    </a>
                </div>

                <div class="log">
                    <a class="button" style="cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">log</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="setModalOverlay" class="modal-overlay">
        <div class="closeButton" onclick="closeModal()">X</div>

        <div class="inputbox">
            <div class="boxwrapper">
                <img src="assets/box.PNG" alt="input box" class="boxIcon">
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                <span class="inputHeader" style="font-size: 3vw;">ALLOWANCE</span>

                <form action="validation/allowance.php" method="POST" class="inputForm">

                    <input type="number" id="amount" name="amount" required placeholder="Enter amount" min="0" step="0.01">

                    <button type="submit" class="addButton" style="background: none; border: none; cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">set</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div id="addModalOverlay" class="modal-overlay">
        <div class="closeButton" onclick="closeModal()">X</div>

        <div class="inputbox">
            <div class="boxwrapper">
                <img src="assets/box.PNG" alt="input box" class="boxIcon">
                <span class="inputHeader" style="font-size: 3vw;">EXPENSES</span>

                <form action="validation/expense.php" method="POST" class="inputForm">
                    <label for="item_name">Purchased Item</label>
                    <input type="text" id="item_name" name="item_name" required placeholder="Enter item name">

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

    <div id="deleteModalOverlay" class="modal-overlay">
        <div class="closeButton" onclick="closeModal()">X</div>

        <div class="inputbox">
            <div class="boxwrapper">
                <img src="assets/box.PNG" alt="input box" class="boxIcon">

                <span class="inputHeader" style="font-size: 3vw; margin-top: 20px;">EXPENSES</span>

                <form action="validation/delete.php" method="POST" class="inputForm" style="display: flex; flex-direction: column; gap: 15px; align-items: center; margin-top: 90px;">

                    <select id="item_id" name="item_id" required style="padding: 8px; width: 80%; font-size: 1vw;">
                        <option value="">Select Item to Delete</option>
                        <?php
                        include("connect.php");
                        session_start();

                        $user_id = $_SESSION['user_id'] ?? null;
                        if ($user_id) {
                            $sql = "SELECT item_id, name FROM categories WHERE user_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $item_id = htmlspecialchars($row['item_id']);
                                    $name = htmlspecialchars($row['name']);
                                    echo "<option value=\"$item_id\">$name</option>";
                                }
                            } else {
                                echo '<option disabled>No items available</option>';
                            }

                            $stmt->close();
                            $conn->close();
                        } else {
                            echo '<option disabled>Please log in first</option>';
                        }
                        ?>
                    </select>

                    <button type="submit" class="addButton" style="background: none; border: none; cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">delete</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="editModalOverlay" class="modal-overlay">
        <div class="closeButton" onclick="closeModal()">X</div>

        <div class="inputbox">
            <div class="boxwrapper">
                <img src="assets/box.PNG" alt="input box" class="boxIcon">
                <span class="inputHeader" style="font-size: 2.7vw;">SET NEW GOAL</span>

                <form action="validation/savings.php" method="POST" class="inputForm">
                    <label for="goal_name">Goal</label>
                    <input type="text" id="goal_name" name="goal_name" required placeholder="Enter goal">

                    <label for="target_amount">Target Amount</label>
                    <input type="number" id="target_amount" name="target_amount" required placeholder="Enter amount" min="0" step="0.01">

                    <button type="submit" class="addButton" style="background: none; border: none; cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">edit</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="logModalOverlay" class="modal-overlay">
        <div class="closeButton" onclick="closeModal()">X</div>
        
        <div class="inputbox">
            <div class="boxwrapper">
                <img src="assets/box.PNG" alt="input box" class="boxIcon">
                <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                <span class="inputHeader" style="font-size: 3vw;">LOG SAVINGS</span>

                <form action="validation/log.php" method="POST" class="inputForm">

                    <input type="number" id="current_amount" name="current_amount" required placeholder="Log Amount" min="0" step="0.01">

                    <button type="submit" class="addButton" style="background: none; border: none; cursor: pointer;">
                        <div class="buttonWrapper">
                            <img src="assets/greenButton.PNG" alt="green button" class="signupIcon">
                            <span class="buttonText" style="font-size: 1.05vw;">log</span>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>